<?php

use App\Models\AskQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Role::findOrCreate('user', 'web');
    $this->questioner = User::factory()->create(['is_active' => true]);
    $this->questioner->assignRole('user');
    $this->otherQuestioner = User::factory()->create(['is_active' => true]);
    $this->otherQuestioner->assignRole('user');
    Mail::fake();
});

function accountQuestion(User $user, array $attributes = []): AskQuestion
{
    return AskQuestion::factory()->for($user)->create(array_merge([
        'name' => 'Submitted Name',
        'email' => 'submitted@example.test',
        'phone' => '03001234567',
        'subject' => 'Submitted subject',
        'sawal' => 'Submitted question',
    ], $attributes));
}

test('guests enter the existing login flow for both My Questions pages', function () {
    $question = accountQuestion($this->questioner);

    $this->get(route('front.account.questions.index'))->assertRedirect(route('front.login'));
    $this->get(route('front.account.questions.show', $question))->assertRedirect(route('front.login'));
});

test('list shows only own questions newest first with persisted data and Urdu statuses', function () {
    $older = accountQuestion($this->questioner, ['subject' => 'Older subject']);
    $older->forceFill(['created_at' => now()->subDay()])->save();
    $newer = accountQuestion($this->questioner, ['subject' => 'Newer subject']);
    $newer->forceFill(['status' => AskQuestion::STATUS_ANSWERED])->save();
    $closed = accountQuestion($this->questioner, ['subject' => 'Closed subject']);
    $closed->forceFill(['status' => AskQuestion::STATUS_CLOSED, 'created_at' => now()->subHours(2)])->save();
    $other = accountQuestion($this->otherQuestioner, ['subject' => 'Hidden subject']);

    $response = $this->actingAs($this->questioner)
        ->get(route('front.account.questions.index'))
        ->assertSuccessful()
        ->assertViewIs('frontend.user-account.questions')
        ->assertSeeInOrder([$newer->question_no, $closed->question_no, $older->question_no])
        ->assertSee('Newer subject')
        ->assertSee('جواب موصول ہو گیا')
        ->assertSee('مکمل / بند')
        ->assertDontSee($other->question_no)
        ->assertDontSee('Hidden subject');

    expect($response->viewData('questions')->first()->is($newer))->toBeTrue();
    Mail::assertNothingOutgoing();
});

test('list paginates ten questions at a time', function () {
    AskQuestion::factory()->for($this->questioner)->count(11)->create();

    $this->actingAs($this->questioner)
        ->get(route('front.account.questions.index'))
        ->assertSuccessful()
        ->assertViewHas('questions', fn ($questions): bool => $questions->count() === 10 && $questions->total() === 11);
});

test('empty state and account sidebar link to the existing question form and stay active', function () {
    $this->actingAs($this->questioner)
        ->get(route('front.account.questions.index'))
        ->assertSuccessful()
        ->assertSee('آپ نے ابھی تک کوئی سوال نہیں پوچھا۔')
        ->assertSee('اپنا پہلا سوال پوچھیں')
        ->assertSee(route('front.ask-question'), false)
        ->assertSee('میرے سوالات')
        ->assertSee('aria-current="page"', false);
});

test('detail is owner scoped and displays persisted snapshots and original question safely', function () {
    $own = accountQuestion($this->questioner, [
        'subject' => '<b>Unsafe subject</b>',
        'sawal' => "First line\n<script>alert('question')</script>",
    ]);
    $other = accountQuestion($this->otherQuestioner);

    $this->actingAs($this->questioner)
        ->get(route('front.account.questions.show', $own))
        ->assertSuccessful()
        ->assertViewIs('frontend.user-account.question-detail')
        ->assertSee($own->question_no)
        ->assertSee('Submitted Name')
        ->assertSee('submitted@example.test')
        ->assertSee('03001234567')
        ->assertSee('&lt;b&gt;Unsafe subject&lt;/b&gt;', false)
        ->assertSee('&lt;script&gt;alert(&#039;question&#039;)&lt;/script&gt;', false)
        ->assertSee('aria-current="page"', false);

    $this->get(route('front.account.questions.show', $other))->assertNotFound();
    $this->get('/account/questions/999999')->assertNotFound();
    Mail::assertNothingOutgoing();
});

test('pending detail shows the waiting message without inventing an answer', function () {
    $question = accountQuestion($this->questioner);

    $this->actingAs($this->questioner)
        ->get(route('front.account.questions.show', $question))
        ->assertSuccessful()
        ->assertSee('جواب کا منتظر')
        ->assertSee('آپ کا سوال موصول ہو چکا ہے اور ہماری ٹیم اس کا جائزہ لے رہی ہے۔')
        ->assertDontSee('ماہر کا جواب');
});

test('answered and closed questions display only their persisted response state safely', function (string $status, ?string $response, string $expected) {
    $question = accountQuestion($this->questioner);
    $question->forceFill(['status' => $status, 'admin_response' => $response])->save();

    $result = $this->actingAs($this->questioner)
        ->get(route('front.account.questions.show', $question))
        ->assertSuccessful()
        ->assertSee($expected);

    if ($response !== null) {
        $result->assertSee('&lt;script&gt;alert(&#039;answer&#039;)&lt;/script&gt;', false)
            ->assertDontSee("<script>alert('answer')</script>", false);
    }
})->with([
    'answered with response' => [AskQuestion::STATUS_ANSWERED, "Answer line\n<script>alert('answer')</script>", 'جواب موصول ہو گیا'],
    'closed with response' => [AskQuestion::STATUS_CLOSED, "Closed response\n<script>alert('answer')</script>", 'مکمل / بند'],
    'closed without response' => [AskQuestion::STATUS_CLOSED, null, 'یہ سوال بند کر دیا گیا ہے۔'],
]);

test('My Questions routes are read-only GET routes and account views send no mail', function () {
    $question = accountQuestion($this->questioner);

    $this->actingAs($this->questioner)->get(route('front.account.questions.index'))->assertSuccessful();
    $this->get(route('front.account.questions.show', $question))->assertSuccessful();

    $routes = collect(app('router')->getRoutes()->getRoutes())
        ->filter(fn ($route): bool => str_starts_with((string) $route->getName(), 'front.account.questions.'));

    expect($routes)->toHaveCount(2)
        ->and($routes->every(fn ($route): bool => $route->methods() === ['GET', 'HEAD']))->toBeTrue();
    Mail::assertNothingOutgoing();
});
