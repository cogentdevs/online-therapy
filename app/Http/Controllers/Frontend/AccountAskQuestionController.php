<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AskQuestion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountAskQuestionController extends Controller
{
    public function index(Request $request): View
    {
        $questions = AskQuestion::query()
            ->where('user_id', $request->user('web')->getKey())
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('frontend.user-account.questions', compact('questions'));
    }

    public function show(Request $request, int $askQuestion): View
    {
        $question = AskQuestion::query()
            ->where('user_id', $request->user('web')->getKey())
            ->findOrFail($askQuestion);

        return view('frontend.user-account.question-detail', compact('question'));
    }
}
