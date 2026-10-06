<?php

use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use App\Mail\AskQuestionReceivedMailToAdmin;
use App\Mail\AskQuestionReceivedMailToUser;
use App\Models\AskQuestion;
use App\Models\GeneralSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Mail\PendingMail;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    config(['captcha.sitekey' => 'ask-test-site-key', 'captcha.secret' => 'ask-test-secret']);
    config(['mail.admin_address' => 'admin@example.test']);
    Role::findOrCreate('user', 'web');
    $this->questioner = User::factory()->create([
        'name' => 'Questioner',
        'email' => 'questioner@example.test',
        'phone' => '03001234567',
        'is_active' => true,
    ]);
    $this->questioner->assignRole('user');
});

function askQuestionPayload(array $overrides = []): array
{
    return array_replace([
        'name' => 'Snapshot Name',
        'email' => 'snapshot@example.test',
        'phone' => '03009998888',
        'subject' => 'My question subject',
        'sawal' => 'The complete question text?',
        'g-recaptcha-response' => 'verified-token',
    ], $overrides);
}

test('guests enter the existing intended-login flow for all question routes', function () {
    $question = AskQuestion::factory()->for($this->questioner)->create();
    $this->get(route('front.ask-question'))
        ->assertRedirect(route('front.login'))
        ->assertSessionHas('url.intended', route('front.ask-question'));
    $this->post(route('front.ask-question.store'), askQuestionPayload())->assertRedirect(route('front.login'));
    $this->get(route('front.ask-question.success', $question))->assertRedirect(route('front.login'));
});

test('page uses the shared layout and prefills authenticated contact fields', function () {
    $response = $this->actingAs($this->questioner)->get(route('front.ask-question'))
        ->assertSuccessful()
        ->assertViewIs('frontend.ask-question')
        ->assertSee('value="Questioner"', false)
        ->assertSee('value="questioner@example.test"', false)
        ->assertSee('value="03001234567"', false)
        ->assertSee('ask-test-site-key', false)
        ->assertSee('front-footer', false);

    $html = $response->getContent();
    preg_match('/<div class="collapse navbar-collapse[^>]*id="frontNavigation">(.*?)<\/div>\s*<\/div>\s*<\/nav>/s', $html, $navigation);
    expect($navigation[1] ?? '')
        ->toContain('href="'.route('front.ask-question').'"')
        ->not->toContain('href="'.route('front.advertise').'"');
    $response->assertSee('سوال پوچھیں')->assertSee(route('front.ask-question'), false);
});

test('valid submission stores snapshots and server-owned fields, then emails both recipients', function () {
    Mail::fake();
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);
    $other = User::factory()->create();
    $other->assignRole('user');

    $this->actingAs($this->questioner)->post(route('front.ask-question.store'), askQuestionPayload([
        'user_id' => $other->id,
        'question_no' => 'FAKE',
        'status' => 'answered',
        'admin_response' => 'Fake answer',
    ]))->assertRedirect();

    $this->assertDatabaseCount('ask_questions', 1);
    $question = AskQuestion::query()->sole();
    expect($question->user_id)->toBe($this->questioner->id)
        ->and($question->question_no)->toBe('ASK-000001')
        ->and($question->status)->toBe(AskQuestion::STATUS_PENDING)
        ->and($question->admin_response)->toBeNull()
        ->and($question->name)->toBe('Snapshot Name')
        ->and($question->email)->toBe('snapshot@example.test')
        ->and($question->phone)->toBe('03009998888')
        ->and($question->subject)->toBe('My question subject')
        ->and($question->sawal)->toBe('The complete question text?');

    Mail::assertSent(AskQuestionReceivedMailToUser::class, fn (AskQuestionReceivedMailToUser $mail): bool => $mail->hasTo('snapshot@example.test')
        && $mail->askQuestion->is($question)
        && str_contains($mail->envelope()->subject, $question->question_no)
        && str_contains($mail->render(), $question->subject)
        && str_contains($mail->render(), $question->sawal));
    Mail::assertSent(AskQuestionReceivedMailToAdmin::class, fn (AskQuestionReceivedMailToAdmin $mail): bool => $mail->hasTo('admin@example.test')
        && $mail->askQuestion->is($question)
        && str_contains($mail->render(), $question->question_no));
    Mail::assertSentCount(2);

    $this->get(route('front.ask-question.success', $question))->assertSuccessful()->assertSee($question->question_no);
    $this->get(route('front.ask-question.success', $question))->assertSuccessful();
    Mail::assertSentCount(2);
    $this->actingAs($other)->get(route('front.ask-question.success', $question))->assertNotFound();
});

test('question numbers are unique and immutable and relationship is correct', function () {
    $first = AskQuestion::factory()->for($this->questioner)->create();
    $second = AskQuestion::factory()->for($this->questioner)->create();

    expect($first->question_no)->toBe('ASK-000001')
        ->and($second->question_no)->toBe('ASK-000002')
        ->and($first->user->is($this->questioner))->toBeTrue()
        ->and($this->questioner->askQuestions()->count())->toBe(2);

    $first->question_no = 'ASK-999999';
    expect(fn () => $first->save())->toThrow(LogicException::class);
});

test('captcha and required fields are server validated without creating a question', function () {
    Mail::fake();
    $this->actingAs($this->questioner)->post(route('front.ask-question.store'), askQuestionPayload([
        'g-recaptcha-response' => null,
        'subject' => '',
        'sawal' => str_repeat('x', 2001),
    ]))->assertSessionHasErrors(['g-recaptcha-response', 'subject', 'sawal']);

    $this->assertDatabaseEmpty('ask_questions');
    Mail::assertNothingOutgoing();

    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(false);
    $this->post(route('front.ask-question.store'), askQuestionPayload())->assertSessionHasErrors('g-recaptcha-response');
    $this->assertDatabaseEmpty('ask_questions');
});

test('invalid submission sends no emails', function () {
    Mail::fake();
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);

    $this->actingAs($this->questioner)->post(route('front.ask-question.store'), askQuestionPayload([
        'email' => 'not-an-email',
    ]))->assertSessionHasErrors('email');

    $this->assertDatabaseEmpty('ask_questions');
    Mail::assertNothingOutgoing();
});

test('admin address falls back to general settings when config is absent', function () {
    config(['mail.admin_address' => null]);
    GeneralSetting::query()->create(['email' => 'fallback@example.test']);
    Mail::fake();
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);

    $this->actingAs($this->questioner)->post(route('front.ask-question.store'), askQuestionPayload())->assertRedirect();

    Mail::assertSent(AskQuestionReceivedMailToAdmin::class, fn (AskQuestionReceivedMailToAdmin $mail): bool => $mail->hasTo('fallback@example.test'));
});

test('user mail failure preserves the question and still attempts admin mail', function () {
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);
    Mail::shouldReceive('to')->once()->with('snapshot@example.test')->andThrow(new RuntimeException('User mail failed'));
    $adminPendingMail = Mockery::mock(PendingMail::class);
    $adminPendingMail->shouldReceive('send')->once()->with(Mockery::type(AskQuestionReceivedMailToAdmin::class));
    Mail::shouldReceive('to')->once()->with('admin@example.test')->andReturn($adminPendingMail);

    $this->actingAs($this->questioner)->post(route('front.ask-question.store'), askQuestionPayload())->assertRedirect();
    $this->assertDatabaseCount('ask_questions', 1);
});

test('admin mail failure preserves the question after user mail attempt', function () {
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);
    $userPendingMail = Mockery::mock(PendingMail::class);
    $userPendingMail->shouldReceive('send')->once()->with(Mockery::type(AskQuestionReceivedMailToUser::class));
    Mail::shouldReceive('to')->once()->with('snapshot@example.test')->andReturn($userPendingMail);
    Mail::shouldReceive('to')->once()->with('admin@example.test')->andThrow(new RuntimeException('Admin mail failed'));

    $this->actingAs($this->questioner)->post(route('front.ask-question.store'), askQuestionPayload())->assertRedirect();
    $this->assertDatabaseCount('ask_questions', 1);
});
