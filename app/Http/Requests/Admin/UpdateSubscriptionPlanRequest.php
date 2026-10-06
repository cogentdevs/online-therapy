<?php

namespace App\Http\Requests\Admin;

use App\Models\Currency;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\Video;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateSubscriptionPlanRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'subscription_type_id' => ['required', 'integer', Rule::exists((new SubscriptionType)->getTable(), 'id')->where('isActive', true)],
            'currency_id' => ['required', 'integer', Rule::exists((new Currency)->getTable(), 'id')->where('isActive', true)],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_value' => ['required', 'integer', 'min:1'],
            'duration_unit' => ['required', Rule::in(['day', 'month', 'year'])],
            'discount_type' => ['nullable', Rule::in(['fixed', 'percentage'])],
            'discount_value' => ['nullable', 'required_with:discount_type', 'numeric', 'min:0', Rule::when($this->input('discount_type') === 'percentage', ['max:100'])],
            'isActive' => ['required', 'boolean'],
            'video_ids' => ['nullable', 'array'],
            'video_ids.*' => ['integer', 'distinct', Rule::exists((new Video)->getTable(), 'id')],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('discount_type')) {
            $this->merge(['discount_type' => null, 'discount_value' => null]);
        }
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $videoIds = collect($this->input('video_ids', []))
                ->filter(fn (mixed $id): bool => is_numeric($id))
                ->map(fn (mixed $id): int => (int) $id)
                ->unique()
                ->values();

            if ($videoIds->isEmpty()) {
                return;
            }

            $plan = SubscriptionProduct::query()->find($this->route('id'));
            $existingVideoIds = $plan?->videos()->pluck('videos.id') ?? collect();
            $ineligibleNewVideoIds = Video::query()
                ->whereIn('id', $videoIds)
                ->whereNotIn('id', $existingVideoIds)
                ->where(function ($query): void {
                    $query->where('is_active', false)
                        ->orWhereNull('status')
                        ->orWhere('status', '!=', Video::STATUS_PUBLISHED);
                })
                ->pluck('id');

            if ($ineligibleNewVideoIds->isNotEmpty()) {
                $validator->errors()->add('video_ids', 'Only active, published Videos can be added to a Subscription Plan.');
            }
        }];
    }
}
