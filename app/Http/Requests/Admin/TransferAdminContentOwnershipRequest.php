<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Services\AdminContentOwnershipTransferService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferAdminContentOwnershipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() instanceof User && $this->user()->can('users.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'new_owner_id' => ['required', 'integer', Rule::exists((new User)->getTable(), 'id')],
            'content_types' => ['required', 'array', 'min:1'],
            'content_types.*' => [
                'required',
                'string',
                'distinct',
                Rule::in([
                    AdminContentOwnershipTransferService::TYPE_MAGAZINES,
                    AdminContentOwnershipTransferService::TYPE_ARTICLES,
                    AdminContentOwnershipTransferService::TYPE_CHILD_ADMINS,
                ]),
            ],
            'deactivate_source' => ['nullable', 'boolean'],
            'confirm_transfer' => ['required', 'accepted'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'content_types.required' => 'Select at least one content or responsibility type to transfer.',
            'content_types.min' => 'Select at least one content or responsibility type to transfer.',
            'confirm_transfer.accepted' => 'Confirm that you understand this Ownership Transfer.',
        ];
    }
}
