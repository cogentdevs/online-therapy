<?php

namespace App\Services;

use App\Mail\AskQuestionReceivedMailToAdmin;
use App\Mail\AskQuestionReceivedMailToUser;
use App\Models\AskQuestion;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AskQuestionMailService
{
    public function sendForCreatedQuestion(AskQuestion $question): void
    {
        try {
            Mail::to($question->email)->send(new AskQuestionReceivedMailToUser($question));
        } catch (Throwable $exception) {
            report($exception);
        }

        $adminEmail = $this->adminEmail($question);

        if ($adminEmail === null) {
            return;
        }

        try {
            Mail::to($adminEmail)->send(new AskQuestionReceivedMailToAdmin($question));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function adminEmail(AskQuestion $question): ?string
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
            Log::warning('Ask Question admin notification skipped: no valid configured recipient.', [
                'ask_question_id' => $question->getKey(),
            ]);

            return null;
        }

        return $adminEmail;
    }
}
