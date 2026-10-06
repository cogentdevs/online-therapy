<?php

use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use App\Mail\ContactMailToAdmin;
use App\Models\Contact;
use App\Models\GeneralSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'captcha.sitekey' => 'contact-test-site-key',
        'captcha.secret' => 'contact-test-secret',
        'mail.admin_address' => 'cogentdevs@gmail.com',
    ]);
});

function contactPayload(array $overrides = []): array
{
    return array_replace([
        'name' => 'Ali Khan',
        'phone' => '03001234567',
        'email' => 'ali@example.test',
        'subject' => 'Magazine information',
        'message' => 'Please share details about the latest magazine.',
        'g-recaptcha-response' => 'verified-google-token',
    ], $overrides);
}

test('contact page renders the urdu form real recaptcha and general settings details', function () {
    GeneralSetting::query()->create([
        'app_name' => 'Digital Magazine',
        'contact_1' => '03009998888',
        'email' => 'contact@example.test',
        'address' => 'Karachi, Pakistan',
        'youtube' => 'https://youtube.com/example',
    ]);

    $this->get(route('front.contact'))
        ->assertSuccessful()
        ->assertViewIs('frontend.contact')
        ->assertSee('رابطہ کریں')
        ->assertSee('ہمیں پیغام بھیجیں')
        ->assertSee('contact-test-site-key', false)
        ->assertSee('g-recaptcha', false)
        ->assertSee('03009998888')
        ->assertSee('contact@example.test')
        ->assertSee('Karachi, Pakistan')
        ->assertSee('https://youtube.com/example', false)
        ->assertSee('name="_token"', false)
        ->assertDontSee('Working Hours')
        ->assertDontSee('<iframe', false);
});

test('required contact fields and recaptcha are validated', function (string $field, mixed $value) {
    Mail::fake();
    NoCaptcha::shouldReceive('verifyResponse')->zeroOrMoreTimes()->andReturn(true);

    $this->post(route('front.contact.store'), contactPayload([$field => $value]))
        ->assertSessionHasErrors($field);

    $this->assertDatabaseEmpty('contacts');
    Mail::assertNothingOutgoing();
})->with([
    'name' => ['name', ''],
    'phone' => ['phone', ''],
    'email required' => ['email', ''],
    'email invalid' => ['email', 'not-an-email'],
    'subject' => ['subject', ''],
    'message' => ['message', ''],
    'recaptcha' => ['g-recaptcha-response', ''],
]);

test('failed google recaptcha prevents contact submission', function () {
    Mail::fake();
    NoCaptcha::shouldReceive('verifyResponse')->once()->with('verified-google-token', '127.0.0.1')->andReturn(false);

    $this->post(route('front.contact.store'), contactPayload())
        ->assertSessionHasErrors('g-recaptcha-response');

    $this->assertDatabaseEmpty('contacts');
    Mail::assertNothingOutgoing();
});

test('valid contact submission stores one unread record and sends the admin mail', function () {
    Mail::fake();
    NoCaptcha::shouldReceive('verifyResponse')->once()->with('verified-google-token', '127.0.0.1')->andReturn(true);

    $this->post(route('front.contact.store'), contactPayload() + ['is_read' => true])
        ->assertRedirect(route('front.contact'))
        ->assertSessionHas('success');

    $this->assertDatabaseCount('contacts', 1);
    $contact = Contact::query()->sole();
    expect($contact->name)->toBe('Ali Khan')
        ->and($contact->phone)->toBe('03001234567')
        ->and($contact->email)->toBe('ali@example.test')
        ->and($contact->subject)->toBe('Magazine information')
        ->and($contact->message)->toBe('Please share details about the latest magazine.')
        ->and($contact->is_read)->toBeFalse();

    Mail::assertSent(ContactMailToAdmin::class, fn (ContactMailToAdmin $mail): bool => $mail->hasTo('admin@example.test') && $mail->contact->is($contact));
    Mail::assertSentCount(1);

    expect((new ContactMailToAdmin($contact))->render())
        ->toContain('Ali Khan')
        ->toContain('ali@example.test')
        ->toContain('03001234567')
        ->toContain('Magazine information')
        ->toContain('Please share details about the latest magazine.');
});

test('mail failure does not remove the saved contact record or expose the exception', function () {
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);
    Mail::shouldReceive('to')->once()->with('admin@example.test')->andThrow(new RuntimeException('SMTP unavailable'));

    $this->post(route('front.contact.store'), contactPayload())
        ->assertRedirect(route('front.contact'))
        ->assertSessionHas('success');

    $this->assertDatabaseCount('contacts', 1);
    $this->assertDatabaseHas('contacts', ['email' => 'ali@example.test', 'is_read' => false]);
});
