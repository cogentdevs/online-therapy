<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteVisitEngagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'size:64'],
            'active_seconds' => ['required', 'integer', 'min:1', 'max:30'],
            'ended' => ['sometimes', 'boolean'],
        ];
    }
}
