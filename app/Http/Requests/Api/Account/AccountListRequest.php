<?php

namespace App\Http\Requests\Api\Account;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $sorts = match ($this->route()?->getName()) {
            'api.me.subscriptions.index' => ['date', 'title', 'amount', 'payment_method', 'status'],
            default => ['date', 'title', 'type'],
        };

        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
            'sort' => ['sometimes', Rule::in($sorts)],
            'direction' => ['sometimes', Rule::in(['asc', 'desc'])],
        ];
    }
}
