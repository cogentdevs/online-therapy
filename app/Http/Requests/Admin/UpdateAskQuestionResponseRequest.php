<?php

namespace App\Http\Requests\Admin;

use App\Models\AskQuestion;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAskQuestionResponseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('ask-questions.edit') === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(AskQuestion::statuses())],
            'admin_response' => ['nullable', 'string', 'max:10000', 'required_if:status,'.AskQuestion::STATUS_ANSWERED],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'status.in' => 'Select a valid Ask Question status.',
            'admin_response.required_if' => 'A response is required when marking the question as answered.',
            'admin_response.max' => 'The response may not be greater than 10,000 characters.',
        ];
    }
}
