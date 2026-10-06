<?php

namespace App\Http\Requests\Api;

use App\Models\Ad;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'page' => ['required', 'string', Rule::in(array_keys(Ad::PAGE_PLACEMENTS))],
            'placement' => [
                'nullable',
                'string',
                Rule::in(array_keys(Ad::placementsForPage($this->string('page')->toString()))),
            ],
        ];
    }
}
