<?php

namespace App\Http\Requests\Api;

use App\Services\AdPlacementAvailabilityService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAdvertisingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'from_date' => ['bail', 'required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'to_date' => ['bail', 'required', 'date_format:Y-m-d', 'after_or_equal:from_date'],
            'details' => ['nullable', 'string', 'max:10000'],
            'placements' => ['required', 'array', 'min:1'],
            'placements.*' => ['required', 'string'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $placements = $this->input('placements');

            if (! is_array($placements)) {
                return;
            }

            $availability = app(AdPlacementAvailabilityService::class);
            $seen = [];

            foreach ($placements as $placement) {
                if (! is_string($placement)) {
                    continue;
                }

                if (isset($seen[$placement])) {
                    $validator->errors()->add('placements', 'Each advertising placement may only be selected once.');

                    return;
                }

                $seen[$placement] = true;
                [$pageName, $place] = array_pad(explode(':', $placement, 2), 2, '');

                if (! $availability->isValidPlacement($pageName, $place)) {
                    $validator->errors()->add('placements', 'The selected advertising placement is invalid.');

                    return;
                }
            }
        }];
    }

    /** @return array<int, array{page_name: string, place: string}> */
    public function selectedPlacements(): array
    {
        return array_map(static function (string $placement): array {
            [$pageName, $place] = explode(':', $placement, 2);

            return ['page_name' => $pageName, 'place' => $place];
        }, $this->validated('placements'));
    }
}
