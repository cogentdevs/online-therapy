<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiteAnalyticsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'string', Rule::in(array_keys((array) config('site_analytics.types', [])))],
            'from_date' => ['nullable', 'date_format:Y-m-d'],
            'to_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from_date'],
            'draw' => ['sometimes', 'integer', 'min:0'],
            'start' => ['sometimes', 'integer', 'min:0'],
            'length' => ['sometimes', 'integer'],
            'search.value' => ['sometimes', 'nullable', 'string', 'max:100'],
            'order.0.column' => ['sometimes', 'integer', 'min:0'],
            'order.0.dir' => ['sometimes', Rule::in(['asc', 'desc'])],
        ];
    }

    /** @return array<string, mixed> */
    public function filters(): array
    {
        return [
            'type' => $this->validated('type', 'pages'),
            'from_date' => $this->validated('from_date'),
            'to_date' => $this->validated('to_date'),
        ];
    }
}
