<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SubscriptionProductIndexRequest;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Services\SubscriptionCatalogueApiService;
use Illuminate\Http\JsonResponse;

class SubscriptionProductController extends Controller
{
    public function __construct(private readonly SubscriptionCatalogueApiService $subscriptionCatalogueApiService) {}

    public function index(SubscriptionProductIndexRequest $request): JsonResponse
    {
        $type = $request->validated('type');

        return response()->json(['success' => true, 'data' => [
            'type' => $type,
            'products' => $this->subscriptionCatalogueApiService->products($type)
                ->map(fn (SubscriptionProduct $product): array => $this->productData($product))->values(),
        ]]);
    }

    /** @return array<string, mixed> */
    private function productData(SubscriptionProduct $product): array
    {
        $price = (float) $product->price;
        $effectivePrice = $product->effectivePrice();
        $subscriptionTypes = $product->subscriptionTypes->map(fn (SubscriptionType $type): array => [
            'id' => $type->id, 'name' => $type->name, 'slug' => $type->slug,
        ])->values();

        return [
            'id' => $product->id, 'name' => $product->name, 'product_for' => $product->product_for,
            'price' => number_format($price, 2, '.', ''),
            'discount' => [
                'type' => $product->discount_type,
                'value' => $product->discount_value === null ? null : number_format((float) $product->discount_value, 2, '.', ''),
                'amount' => number_format(max(0, $price - $effectivePrice), 2, '.', ''),
            ],
            'effective_price' => number_format($effectivePrice, 2, '.', ''),
            'currency' => $product->currency === null ? null : [
                'id' => $product->currency->id, 'name' => $product->currency->name,
                'code' => $product->currency->code, 'symbol' => $product->currency->symbol,
            ],
            'duration' => ['value' => $product->duration_value, 'unit' => $product->duration_unit],
            'subscription_types' => $subscriptionTypes,
            'included_types' => $product->product_for === SubscriptionProduct::FOR_MEMBERSHIP ? $subscriptionTypes : [],
        ];
    }
}
