<?php

use App\Mail\AccountActivationLinkMailToUser;
use App\Mail\NewUserRegisterMailToAdmin;
use App\Mail\WelcomeMailToUser;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(TestCase::class);

test('auth mail sends immediately even when the application uses a database queue', function (string $mailClass) {
    config(['queue.default' => 'database']);
    Mail::fake();
    $user = new User(['name' => 'Reader', 'email' => 'reader@example.com']);
    $mail = $mailClass === AccountActivationLinkMailToUser::class
        ? new $mailClass($user, 'https://example.com/account/activate?token=test')
        : new $mailClass($user);

    Mail::to($user)->send($mail);

    Mail::assertSent($mailClass, fn ($sent) => $sent->hasTo($user->email));
    Mail::assertSentCount(1);
    Mail::assertNothingQueued();
})->with([
    NewUserRegisterMailToAdmin::class,
    AccountActivationLinkMailToUser::class,
    WelcomeMailToUser::class,
]);
