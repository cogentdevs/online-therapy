<?php

namespace App\Services;

use App\Mail\AskQuestionResponseMailToAdmin;
use App\Mail\AskQuestionResponseMailToUser;
use App\Models\AskQuestion;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AskQuestionResponseMailService
{
    public function send(AskQuestion $askQuestion): void
    {
        try {
            Mail::to($askQuestion->email)->send(new AskQuestionResponseMailToUser($askQuestion));
        } catch (Throwable $exception) {
            report($exception);
        }

        $adminEmail = $this->adminEmail($askQuestion);

        if ($adminEmail === null) {
            return;
        }

        try {
            Mail::to($adminEmail)->send(new AskQuestionResponseMailToAdmin($askQuestion));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function adminEmail(AskQuestion $askQuestion): ?string
    {
        try {
            $configuredAddress = 'cogentdevs@gmail.com';
            $adminEmail = is_string($configuredAddress) && filter_var($configuredAddress, FILTER_VALIDATE_EMAIL)
                ? $configuredAddress
                : GeneralSetting::query()->value('email');
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }

        if (! is_string($adminEmail) || filter_var($adminEmail, FILTER_VALIDATE_EMAIL) === false) {
            Log::warning('Ask Question response admin notification skipped: no valid configured recipient.', [
                'ask_question_id' => $askQuestion->getKey(),
            ]);

            return null;
        }

        return $adminEmail;
    }
}
