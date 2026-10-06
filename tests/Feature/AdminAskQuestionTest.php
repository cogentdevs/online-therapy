<?php

use App\Mail\AskQuestionResponseMailToAdmin;
use App\Mail\AskQuestionResponseMailToUser;
use App\Models\ActivityLog;
use App\Models\AskQuestion;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    $this->frontendUser = User::factory()->create(['is_active' => true]);
    $this->frontendUser->assignRole(Role::findOrCreate('user', 'web'));
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
    Mail::fake();
});

function askQuestionAdmin(array $permissions): User
{
    $role = Role::findOrCreate('ask-question-admin-'.str()->random(8), 'web');
    $role->syncPermissions(array_values(array_unique(['admin.access', 'dashboard.view', ...$permissions])));
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

function submittedQuestion(array $attributes = []): AskQuestion
{
    $question = AskQuestion::factory()->for(test()->frontendUser)->create(array_merge([
        'name' => 'Submitted Name',
        'email' => 'snapshot@example.test',
        'phone' => '03001234567',
        'subject' => 'Submitted Subject',
        'sawal' => 'Submitted original question.',
    ], $attributes));

    return $question;
}

test('guest and normal frontend user cannot access the admin module', function () {
    $question = submittedQuestion();

    $this->get(route('admin.ask-questions.index'))->assertRedirect(route('admin.login'));
    $this->actingAs($this->frontendUser)->get(route('admin.ask-questions.index'))->assertForbidden();
    $this->get(route('admin.ask-questions.show', $question))->assertForbidden();
});

test('super admin and view-permitted admin can list and inspect persisted questions', function () {
    $older = submittedQuestion(['subject' => 'Older Subject', 'created_at' => now()->subDay()]);
    $newer = submittedQuestion(['subject' => 'Newer Subject', 'sawal' => '<script>alert("unsafe")</script>', 'created_at' => now()]);

    $superResponse = $this->actingAs($this->superAdmin)->get(route('admin.ask-questions.index'))
        ->assertSuccessful()->assertSee($older->question_no)->assertSee($newer->question_no);
    expect(strpos($superResponse->getContent(), $newer->question_no))->toBeLessThan(strpos($superResponse->getContent(), $older->question_no));

    $viewer = askQuestionAdmin(['ask-questions.view']);
    $this->actingAs($viewer)->get(route('admin.ask-questions.index'))
        ->assertSuccessful()
        ->assertSee('Ask Questions')
        ->assertSee(route('admin.ask-questions.index'), false);

    $this->get(route('admin.ask-questions.show', $newer))
        ->assertSuccessful()
        ->assertSee('Submitted Name')
        ->assertSee('snapshot@example.test')
        ->assertSee('03001234567')
        ->assertSee('Newer Subject')
        ->assertSee('&lt;script&gt;alert(&quot;unsafe&quot;)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert("unsafe")</script>', false)
        ->assertDontSee('Save / Send Response');

    $this->actingAs($this->superAdmin)->get(route('admin.ask-questions.show', $newer))
        ->assertSuccessful()
        ->assertSee('Save / Send Response')
        ->assertSee('data-action-confirm', false)
        ->assertSee('data-action-confirm-text="Yes, Update"', false);

    Mail::assertNothingOutgoing();
});

test('admin without view permission sees neither module pages nor sidebar item', function () {
    $admin = askQuestionAdmin([]);

    $this->actingAs($admin)->get(route('admin.ask-questions.index'))->assertForbidden();
    $this->get(route('admin.dashboard'))->assertSuccessful()->assertDontSee('data-dashboard-module="ask-questions"', false);
});

test('view-only admin cannot update while edit admin can save only response fields', function () {
    $question = submittedQuestion();
    $viewer = askQuestionAdmin(['ask-questions.view']);
    $editor = askQuestionAdmin(['ask-questions.view', 'ask-questions.edit']);
    $original = $question->only(['question_no', 'user_id', 'name', 'email', 'phone', 'subject', 'sawal', 'created_at']);

    $this->actingAs($viewer)->patch(route('admin.ask-questions.update-response', $question), [
        'status' => AskQuestion::STATUS_ANSWERED,
        'admin_response' => 'Forbidden response',
    ])->assertForbidden();

    $this->actingAs($editor)->patch(route('admin.ask-questions.update-response', $question), [
        'status' => AskQuestion::STATUS_ANSWERED,
        'admin_response' => 'A meaningful expert response.',
        'question_no' => 'ASK-999999',
        'user_id' => $editor->id,
        'name' => 'Changed',
        'email' => 'changed@example.test',
        'phone' => '000',
        'subject' => 'Changed',
        'sawal' => 'Changed',
    ])->assertRedirect(route('admin.ask-questions.show', $question));

    $question->refresh();
    expect($question->status)->toBe(AskQuestion::STATUS_ANSWERED)
        ->and($question->admin_response)->toBe('A meaningful expert response.')
        ->and($question->only(array_keys($original)))->toEqual($original);

    Mail::assertSent(AskQuestionResponseMailToUser::class, fn (AskQuestionResponseMailToUser $mail): bool => $mail->hasTo('snapshot@example.test')
        && str_contains($mail->envelope()->subject, $question->question_no)
        && str_contains($mail->render(), $question->subject)
        && str_contains($mail->render(), $question->sawal)
        && str_contains($mail->render(), $question->admin_response));
    Mail::assertSent(AskQuestionResponseMailToAdmin::class, fn (AskQuestionResponseMailToAdmin $mail): bool => $mail->hasTo('muzammilken95@gmail.com')
        && str_contains($mail->envelope()->subject, $question->question_no)
        && str_contains($mail->render(), $question->subject)
        && str_contains($mail->render(), $question->admin_response));
    expect(ActivityLog::query()->where('module', 'ask_questions')->where('subject_id', $question->id)->where('action', 'updated')->exists())->toBeTrue();
});

test('status and answered response validation use only Ask Question statuses', function () {
    $question = submittedQuestion();

    $this->actingAs($this->superAdmin)->patch(route('admin.ask-questions.update-response', $question), [
        'status' => 'published',
        'admin_response' => 'Invalid status.',
    ])->assertSessionHasErrors('status');

    $this->patch(route('admin.ask-questions.update-response', $question), [
        'status' => AskQuestion::STATUS_ANSWERED,
        'admin_response' => '',
    ])->assertSessionHasErrors('admin_response');

    expect($question->fresh()->status)->toBe(AskQuestion::STATUS_PENDING);
    Mail::assertNothingOutgoing();
});

test('pending and closed updates do not notify and closing without a new answer preserves response', function () {
    $question = submittedQuestion();

    $this->actingAs($this->superAdmin)->patch(route('admin.ask-questions.update-response', $question), [
        'status' => AskQuestion::STATUS_PENDING,
        'admin_response' => 'Internal draft response',
    ])->assertRedirect();
    Mail::assertNothingOutgoing();

    $this->patch(route('admin.ask-questions.update-response', $question), [
        'status' => AskQuestion::STATUS_CLOSED,
        'admin_response' => '',
    ])->assertRedirect();

    expect($question->fresh()->status)->toBe(AskQuestion::STATUS_CLOSED)
        ->and($question->fresh()->admin_response)->toBe('Internal draft response');
    Mail::assertNothingOutgoing();
});

test('unchanged answered save does not resend while changed answer does', function () {
    $question = submittedQuestion();
    $question->forceFill(['status' => AskQuestion::STATUS_ANSWERED, 'admin_response' => 'Existing response'])->save();
    Mail::fake();

    $this->actingAs($this->superAdmin)->patch(route('admin.ask-questions.update-response', $question), [
        'status' => AskQuestion::STATUS_ANSWERED,
        'admin_response' => 'Existing response',
    ])->assertRedirect();
    Mail::assertNothingOutgoing();

    $this->patch(route('admin.ask-questions.update-response', $question), [
        'status' => AskQuestion::STATUS_ANSWERED,
        'admin_response' => 'Updated response',
    ])->assertRedirect();
    Mail::assertSent(AskQuestionResponseMailToUser::class, 1);
    Mail::assertSent(AskQuestionResponseMailToAdmin::class, 1);
});

test('user mail failure still attempts admin mail and cannot roll back the saved response', function () {
    $question = submittedQuestion();
    Mail::shouldReceive('to')->once()->with('snapshot@example.test')->andThrow(new RuntimeException('User SMTP unavailable'));
    $adminPendingMail = Mockery::mock();
    $adminPendingMail->shouldReceive('send')->once()->with(Mockery::type(AskQuestionResponseMailToAdmin::class));
    Mail::shouldReceive('to')->once()->with('muzammilken95@gmail.com')->andReturn($adminPendingMail);

    $this->actingAs($this->superAdmin)->patch(route('admin.ask-questions.update-response', $question), [
        'status' => AskQuestion::STATUS_ANSWERED,
        'admin_response' => 'Saved despite mail failure.',
    ])->assertRedirect(route('admin.ask-questions.show', $question));

    expect($question->fresh()->status)->toBe(AskQuestion::STATUS_ANSWERED)
        ->and($question->fresh()->admin_response)->toBe('Saved despite mail failure.');
});

test('dashboard card is permission-aware and counts only pending Ask Questions', function () {
    submittedQuestion();
    submittedQuestion();
    $answered = submittedQuestion();
    $answered->forceFill(['status' => AskQuestion::STATUS_ANSWERED, 'admin_response' => 'Answered'])->save();
    $closed = submittedQuestion();
    $closed->forceFill(['status' => AskQuestion::STATUS_CLOSED])->save();

    $permitted = askQuestionAdmin(['ask-questions.view']);
    $response = $this->actingAs($permitted)->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('data-dashboard-module="ask-questions"', false)
        ->assertSee('data-dashboard-pending="2"', false)
        ->assertSee(route('admin.ask-questions.index'), false);
    expect(data_get($response->viewData('summaries'), 'ask_questions.pending'))->toBe(2);

    $unpermitted = askQuestionAdmin([]);
    $queries = [];
    DB::listen(function ($query) use (&$queries): void {
        $queries[] = strtolower($query->sql);
    });
    $response = $this->actingAs($unpermitted)->get(route('admin.dashboard'));
    $response->assertDontSee('data-dashboard-module="ask-questions"', false);
    expect($response->viewData('summaries'))->not->toHaveKey('ask_questions')
        ->and(implode("\n", $queries))->not->toContain('from "ask_questions"');
});

test('detail GET does not send mail and no delete route exists', function () {
    $question = submittedQuestion();

    $this->actingAs($this->superAdmin)->get(route('admin.ask-questions.show', $question))->assertSuccessful();
    Mail::assertNothingOutgoing();
    expect(collect(app('router')->getRoutes())->contains(fn ($route): bool => str_contains((string) $route->getName(), 'ask-questions') && in_array('DELETE', $route->methods(), true)))->toBeFalse();
});
