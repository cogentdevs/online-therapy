<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreAskQuestionRequest;
use App\Models\AskQuestion;
use App\Services\AskQuestionMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AskQuestionController extends Controller
{
    public function __construct(private readonly AskQuestionMailService $mailService) {}

    public function index(Request $request): View
    {
        return view('frontend.ask-question', ['user' => $request->user('web')]);
    }

    public function store(StoreAskQuestionRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'phone', 'subject', 'sawal']);
        $question = AskQuestion::query()->create([
            ...$data,
            'user_id' => $request->user('web')->getKey(),
        ]);

        $this->mailService->sendForCreatedQuestion($question);

        return to_route('front.ask-question.success', $question);
    }

    public function success(Request $request, AskQuestion $askQuestion): View
    {
        abort_unless($askQuestion->user_id === $request->user('web')->getKey(), 404);

        return view('frontend.ask-question-success', compact('askQuestion'));
    }
}
