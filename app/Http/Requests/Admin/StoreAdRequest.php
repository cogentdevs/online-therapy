<?php

namespace App\Http\Requests\Admin;

use App\Models\Ad;
use App\Models\AdRequestPlacement;
use App\Models\Language;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $placement = $this->route('id')
            ? Ad::query()->find($this->route('id'))?->adRequestPlacement
            : AdRequestPlacement::query()->with('adRequest')->find($this->input('ad_request_placement_id'));

        if ($placement?->adRequest) {
            $this->merge([
                'page_name' => $placement->page_name,
                'place' => $placement->place,
                'start_date' => $placement->adRequest->from_date->toDateString(),
                'expiry_date' => $placement->adRequest->to_date->toDateString(),
            ]);
        }
    }

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
        return [
            'ad_request_placement_id' => ['nullable', 'integer', Rule::exists((new AdRequestPlacement)->getTable(), 'id')],
            'language' => ['nullable', 'string', Rule::exists((new Language)->getTable(), 'code')],
            'title' => ['required', 'string', 'max:255'],
            'page_name' => ['required', 'string', Rule::in(array_keys(Ad::PAGE_PLACEMENTS))],
            'place' => ['required', 'string', Rule::in(array_keys(Ad::placementsForPage($this->string('page_name')->toString())))],
            'ad_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'ad_url' => ['nullable', 'url', 'max:2048'],
            'google_ad_code' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'isActive' => ['nullable', 'boolean'],
        ];
    }
}
