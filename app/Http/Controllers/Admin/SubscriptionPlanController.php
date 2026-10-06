<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubscriptionPlanRequest;
use App\Http\Requests\Admin\UpdateSubscriptionPlanRequest;
use App\Models\Currency;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\Video;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanController extends Controller
{
    public function index(): View
    {
        return view('admin.subscription-plan.view-subscription-plan', ['plans' => $this->plans()->with(['currency', 'subscriptionTypes', 'creator.roles'])->latest()->get()]);
    }

    public function create(): View
    {
        return view('admin.subscription-plan.add-subscription-plan', $this->formOptions());
    }

    public function store(StoreSubscriptionPlanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $plan = SubscriptionProduct::query()->create([...Arr::except($validated, ['subscription_type_id', 'video_ids']), 'product_for' => SubscriptionProduct::FOR_PLAN, 'promotion_id' => null, 'isActive' => true]);
            $plan->subscriptionTypes()->sync([(int) $validated['subscription_type_id']]);
            $plan->videos()->sync($validated['video_ids'] ?? []);
        });

        return redirect()->route('admin.subscription-plan.index')->with('status', 'Subscription Plan added successfully.');
    }

    public function edit(int $id): View
    {
        $plan = $this->plans()->with(['subscriptionTypes', 'videos:id'])->findOrFail($id);

        return view('admin.subscription-plan.edit-subscription-plan', [...$this->formOptions($plan), 'plan' => $plan]);
    }

    public function update(UpdateSubscriptionPlanRequest $request, int $id): RedirectResponse
    {
        $validated = $request->validated();
        DB::transaction(function () use ($id, $validated): void {
            $plan = $this->plans()->findOrFail($id);
            $plan->update(Arr::except($validated, ['subscription_type_id', 'video_ids']));
            $plan->subscriptionTypes()->sync([(int) $validated['subscription_type_id']]);
            $plan->videos()->sync($validated['video_ids'] ?? []);
        });

        return redirect()->route('admin.subscription-plan.index')->with('status', 'Subscription Plan updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->plans()->findOrFail($id)->delete();

        return redirect()->route('admin.subscription-plan.index')->with('status', 'Subscription Plan deleted successfully.');
    }

    private function plans(): Builder
    {
        return SubscriptionProduct::query()->where('product_for', SubscriptionProduct::FOR_PLAN);
    }

    /** @return array{currencies: Collection, subscriptionTypes: Collection, videos: Collection} */
    private function formOptions(?SubscriptionProduct $plan = null): array
    {
        return [
            'currencies' => Currency::query()->where('isActive', true)->orderBy('code')->get(),
            'subscriptionTypes' => SubscriptionType::query()->where('isActive', true)->orderBy('name')->get(),
            'videos' => Video::query()
                ->where(function (Builder $query) use ($plan): void {
                    $query->where('is_active', true)
                        ->where('status', Video::STATUS_PUBLISHED)
                        ->when($plan, fn (Builder $query) => $query->orWhereIn('id', $plan->videos()->select('videos.id')));
                })
                ->orderBy('title')
                ->get(['id', 'title', 'is_active', 'status']),
        ];
    }
}
