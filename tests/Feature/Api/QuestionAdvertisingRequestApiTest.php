<?php

use App\Mail\AdvertisingRequestReceivedMailToAdmin;
use App\Mail\AdvertisingRequestReceivedMailToUser;
use App\Mail\AskQuestionReceivedMailToAdmin;
use App\Mail\AskQuestionReceivedMailToUser;
use App\Models\Ad;
use App\Models\AdRequest;
use App\Models\AskQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->travelTo(now()->setDate(2026, 9, 24));
    $this->user = User::factory()->create(['is_active' => true]);
    $this->otherUser = User::factory()->create(['is_active' => true]);
    Mail::fake();
});

function apiQuestionPayload(array $overrides = []): array
{
    return array_replace([
        'name' => 'Question Snapshot',
        'email' => 'question@example.test',
        'phone' => '03001234567',
        'subject' => 'Business question',
        'sawal' => 'Please explain this business matter.',
    ], $overrides);
}

function apiAdvertisingPayload(array $overrides = []): array
{
    return array_replace([
        'name' => 'Advertiser Snapshot',
        'email' => 'advertiser@example.test',
        'phone' => '03001234567',
        'company' => 'Example Brand',
        'from_date' => '2026-10-10',
        'to_date' => '2026-10-20',
        'details' => 'Campaign details.',
        'placements' => ['header:header_ad', 'home:home_horizontal_small'],
    ], $overrides);
}

test('question api requires authentication and creates server controlled question without captcha', function () {
    $this->postJson(route('api.me.questions.store'), apiQuestionPayload())->assertUnauthorized();
    Sanctum::actingAs($this->user);

    $response = $this->postJson(route('api.me.questions.store'), apiQuestionPayload([
        'user_id' => $this->otherUser->id,
        'question_no' => 'ATTACK',
        'status' => AskQuestion::STATUS_ANSWERED,
        'admin_response' => 'Injected response',
    ]))->assertCreated()
        ->assertJsonPath('data.question.status', AskQuestion::STATUS_PENDING)
        ->assertJsonPath('data.question.admin_response', null);

    $question = AskQuestion::query()->sole();
    expect($question->user_id)->toBe($this->user->id)
        ->and($question->question_no)->toBe('ASK-'.str_pad((string) $question->id, 6, '0', STR_PAD_LEFT));
    $response->assertJsonPath('data.question.question_no', $question->question_no);
    Mail::assertSent(AskQuestionReceivedMailToUser::class, 1);
    Mail::assertSent(AskQuestionReceivedMailToAdmin::class, 1);
});

test('question list and detail are paginated searchable and owner scoped', function () {
    $own = AskQuestion::factory()->for($this->user)->create(['subject' => 'Unique mobile subject']);
    $own->forceFill(['admin_response' => 'Persisted expert response', 'status' => AskQuestion::STATUS_ANSWERED])->save();
    $other = AskQuestion::factory()->for($this->otherUser)->create();
    Sanctum::actingAs($this->user);

    $this->getJson(route('api.me.questions.index', ['search' => 'Unique mobile']))
        ->assertOk()
        ->assertJsonCount(1, 'data.questions')
        ->assertJsonPath('data.questions.0.id', $own->id)
        ->assertJsonMissing(['id' => $other->id])
        ->assertJsonPath('data.pagination.per_page', 10);
    $this->getJson(route('api.me.questions.show', $own))
        ->assertOk()->assertJsonPath('data.question.admin_response', 'Persisted expert response');
    $this->getJson(route('api.me.questions.show', $other))->assertNotFound();
});

test('advertising api validates canonical placements and server controlled ownership status and number', function () {
    $this->postJson(route('api.me.advertising-requests.store'), apiAdvertisingPayload())->assertUnauthorized();
    Sanctum::actingAs($this->user);

    $this->postJson(route('api.me.advertising-requests.store'), apiAdvertisingPayload([
        'user_id' => $this->otherUser->id,
        'request_no' => 'ATTACK',
        'status' => 'confirmed',
    ]))->assertCreated()
        ->assertJsonPath('data.advertising_request.status', 'pending')
        ->assertJsonCount(2, 'data.advertising_request.placements');

    $record = AdRequest::query()->sole();
    expect($record->user_id)->toBe($this->user->id)
        ->and($record->request_no)->toBe('AD-'.str_pad((string) $record->id, 6, '0', STR_PAD_LEFT))
        ->and($record->placements)->toHaveCount(2);
    Mail::assertSent(AdvertisingRequestReceivedMailToUser::class, 1);
    Mail::assertSent(AdvertisingRequestReceivedMailToAdmin::class, 1);

    $this->postJson(route('api.me.advertising-requests.store'), apiAdvertisingPayload([
        'placements' => ['invalid:placement'],
    ]))->assertUnprocessable()->assertJsonValidationErrors('placements');
});

test('advertising api reuses existing conflict rules and rejects the complete request atomically', function () {
    Ad::query()->create([
        'title' => 'Booked Header',
        'page_name' => 'header',
        'place' => 'header_ad',
        'start_date' => '2026-10-10',
        'expiry_date' => '2026-10-20',
    ]);
    Sanctum::actingAs($this->user);

    $this->postJson(route('api.me.advertising-requests.store'), apiAdvertisingPayload())
        ->assertUnprocessable()->assertJsonValidationErrors('placements');

    $this->assertDatabaseEmpty('ad_requests');
    $this->assertDatabaseEmpty('ad_request_placements');
    Mail::assertNothingSent();
});

test('advertising list and detail expose canonical placement labels and enforce ownership', function () {
    $own = AdRequest::query()->create([
        'user_id' => $this->user->id,
        'name' => 'Snapshot',
        'email' => 'snapshot@example.test',
        'phone' => '03001234567',
        'company' => 'Searchable Brand',
        'from_date' => '2026-10-10',
        'to_date' => '2026-10-20',
    ]);
    $own->placements()->create(['page_name' => 'header', 'place' => 'header_ad']);
    $other = AdRequest::query()->create([
        'user_id' => $this->otherUser->id,
        'name' => 'Other',
        'email' => 'other@example.test',
        'phone' => '03000000000',
        'company' => 'Other Brand',
        'from_date' => '2026-11-01',
        'to_date' => '2026-11-02',
    ]);
    Sanctum::actingAs($this->user);

    $this->getJson(route('api.me.advertising-requests.index', ['search' => 'Searchable']))
        ->assertOk()->assertJsonCount(1, 'data.advertising_requests')
        ->assertJsonPath('data.advertising_requests.0.id', $own->id)
        ->assertJsonMissing(['id' => $other->id]);
    $this->getJson(route('api.me.advertising-requests.show', $own))
        ->assertOk()
        ->assertJsonPath('data.advertising_request.placements.0.page_label', Ad::PAGE_PLACEMENTS['header']['label'])
        ->assertJsonPath('data.advertising_request.placements.0.placement_label', Ad::PAGE_PLACEMENTS['header']['places']['header_ad']);
    $this->getJson(route('api.me.advertising-requests.show', $other))->assertNotFound();
});
