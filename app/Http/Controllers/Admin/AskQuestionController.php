<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAskQuestionResponseRequest;
use App\Models\AskQuestion;
use App\Services\AskQuestionResponseMailService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AskQuestionController extends Controller
{
    public function __construct(private readonly AskQuestionResponseMailService $responseMail) {}

    public function index(): View
    {
        return view('admin.ask-questions.index', [
            'questions' => AskQuestion::query()->latest()->get(),
        ]);
    }

    public function show(AskQuestion $askQuestion): View
    {
        return view('admin.ask-questions.show', compact('askQuestion'));
    }

    public function updateResponse(UpdateAskQuestionResponseRequest $request, AskQuestion $askQuestion): RedirectResponse
    {
        $data = $request->validated();
        $oldStatus = $askQuestion->status;
        $oldResponse = $askQuestion->admin_response;
        $newResponse = filled($data['admin_response'] ?? null) ? trim($data['admin_response']) : null;

        if ($data['status'] === AskQuestion::STATUS_CLOSED && $newResponse === null) {
            $newResponse = $oldResponse;
        }

        $askQuestion->admin_response = $newResponse;
        $askQuestion->status = $data['status'];

        if ($askQuestion->isDirty()) {
            $askQuestion->save();
        }

        $shouldNotify = $askQuestion->status === AskQuestion::STATUS_ANSWERED
            && filled($askQuestion->admin_response)
            && ($oldStatus !== AskQuestion::STATUS_ANSWERED || $oldResponse !== $askQuestion->admin_response);

        if ($shouldNotify) {
            $this->responseMail->send($askQuestion);
        }

        return to_route('admin.ask-questions.show', $askQuestion)
            ->with('status', 'Question response updated successfully.');
    }
}
