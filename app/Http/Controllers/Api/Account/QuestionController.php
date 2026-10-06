<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\QuestionListRequest;
use App\Http\Requests\Api\StoreAskQuestionRequest;
use App\Models\AskQuestion;
use App\Services\Api\MobileAccountReadService;
use App\Services\AskQuestionMailService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct(
        private readonly AskQuestionMailService $mailService,
        private readonly MobileAccountReadService $accountReadService,
    ) {}

    public function store(StoreAskQuestionRequest $request): JsonResponse
    {
        $question = AskQuestion::query()->create([
            ...$request->safe()->only(['name', 'email', 'phone', 'subject', 'sawal']),
            'user_id' => $request->user()->getKey(),
        ]);

        $this->mailService->sendForCreatedQuestion($question);

        return response()->json([
            'success' => true,
            'message' => 'Your question has been received successfully.',
            'data' => ['question' => $this->question($question)],
        ], 201);
    }

    public function index(QuestionListRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $query = $request->user()->askQuestions();
        $search = trim((string) ($filters['search'] ?? ''));

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('question_no', 'like', '%'.$search.'%')
                    ->orWhere('subject', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%');
            });
        }

        $column = match ($filters['sort'] ?? 'date') {
            'question_no' => 'question_no',
            'subject' => 'subject',
            'status' => 'status',
            default => 'created_at',
        };
        $questions = $query->orderBy($column, $filters['direction'] ?? 'desc')
            ->orderByDesc('id')->paginate(10)->withQueryString();

        return response()->json(['success' => true, 'data' => [
            'questions' => collect($questions->items())->map(fn (AskQuestion $question): array => $this->questionSummary($question))->all(),
            'pagination' => $this->accountReadService->pagination($questions),
        ]]);
    }

    public function show(Request $request, int $question): JsonResponse
    {
        $record = $request->user()->askQuestions()->findOrFail($question);

        return response()->json(['success' => true, 'data' => ['question' => $this->question($record)]]);
    }

    /** @return array<string, mixed> */
    private function questionSummary(AskQuestion $question): array
    {
        return [
            'id' => $question->id,
            'question_no' => $question->question_no,
            'subject' => $question->subject,
            'status' => $question->status,
            'status_label' => $question->userStatusLabel(),
            'submitted_at' => $question->created_at?->toIso8601String(),
        ];
    }

    /** @return array<string, mixed> */
    private function question(AskQuestion $question): array
    {
        return [
            ...$this->questionSummary($question),
            'name' => $question->name,
            'email' => $question->email,
            'phone' => $question->phone,
            'sawal' => $question->sawal,
            'admin_response' => $question->admin_response,
            'updated_at' => $question->updated_at?->toIso8601String(),
        ];
    }
}
