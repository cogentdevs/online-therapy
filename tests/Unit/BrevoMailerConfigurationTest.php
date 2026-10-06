<?php

use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Tests\TestCase;

uses(TestCase::class);

test('brevo is an explicitly selected smtp mailer and default mail remains separate', function () {
    $defaultMailer = config('mail.default');
    $mailManager = app('mail.manager');
    $brevoMailer = $mailManager->mailer('brevo');
    $resolvedDefaultMailer = $mailManager->mailer();

    expect(config('mail.mailers.brevo.transport'))->toBe('smtp')
        ->and(config('mail.mailers.brevo.host'))->toBe('smtp-relay.brevo.com')
        ->and(config('mail.mailers.brevo.port'))->toBe(587)
        ->and(config('mail.mailers.brevo.scheme'))->toBe('smtp')
        ->and(config('mail.mailers.brevo.require_tls'))->toBeTrue()
        ->and($defaultMailer)->not->toBe('brevo')
        ->and($mailManager->getDefaultDriver())->toBe($defaultMailer)
        ->and($brevoMailer)->toBeInstanceOf(Mailer::class)
        ->and($brevoMailer->getSymfonyTransport())->toBeInstanceOf(EsmtpTransport::class)
        ->and($resolvedDefaultMailer)->toBeInstanceOf(Mailer::class)
        ->and($resolvedDefaultMailer)->not->toBe($brevoMailer);
});

test('brevo configuration uses isolated environment keys and sender identity', function () {
    $mailConfiguration = file_get_contents(config_path('mail.php'));
    $environmentExample = file_get_contents(base_path('.env.example'));

    foreach ([
        'BREVO_MAIL_HOST',
        'BREVO_MAIL_PORT',
        'BREVO_MAIL_ENCRYPTION',
        'BREVO_MAIL_USERNAME',
        'BREVO_MAIL_PASSWORD',
        'BREVO_MAIL_FROM_ADDRESS',
        'BREVO_MAIL_FROM_NAME',
        'BREVO_MAIL_EHLO_DOMAIN',
    ] as $environmentKey) {
        expect($mailConfiguration)->toContain($environmentKey)
            ->and($environmentExample)->toContain($environmentKey.'=');
    }

    expect(config('mail.brevo_from'))->toBe([
        'address' => null,
        'name' => config('app.name'),
    ]);
});

test('configuration checks never send external mail', function () {
    Mail::fake();

    Mail::mailer('brevo');
    Mail::mailer((string) config('mail.default'));

    Mail::assertNothingOutgoing();
});
