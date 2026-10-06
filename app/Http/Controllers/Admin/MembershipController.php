<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMembershipRequest;
use App\Http\Requests\Admin\UpdateMembershipRequest;
use App\Models\Currency;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class MembershipController extends Controller
{
    public function index(): View
    {
        return view('admin.membership.view-membership', ['memberships' => $this->memberships()->with(['currency', 'subscriptionTypes', 'creator.roles'])->latest()->get()]);
    }

    public function create(): View
    {
        return view('admin.membership.add-membership', $this->formOptions());
    }

    public function store(StoreMembershipRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        DB::transaction(function () use ($validated): void {
            $membership = SubscriptionProduct::query()->create([...Arr::except($validated, ['subscription_type_ids']), 'product_for' => SubscriptionProduct::FOR_MEMBERSHIP, 'promotion_id' => null, 'isActive' => true]);
            $membership->subscriptionTypes()->sync($validated['subscription_type_ids']);
        });

        return redirect()->route('admin.membership.index')->with('status', 'Membership added successfully.');
    }

    public function edit(int $id): View
    {
        return view('admin.membership.edit-membership', [...$this->formOptions(), 'membership' => $this->memberships()->with('subscriptionTypes')->findOrFail($id)]);
    }

    public function update(UpdateMembershipRequest $request, int $id): RedirectResponse
    {
        $validated = $request->validated();
        DB::transaction(function () use ($id, $validated): void {
            $membership = $this->memberships()->findOrFail($id);
            $membership->update(Arr::except($validated, ['subscription_type_ids']));
            $membership->subscriptionTypes()->sync($validated['subscription_type_ids']);
        });

        return redirect()->route('admin.membership.index')->with('status', 'Membership updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->memberships()->findOrFail($id)->delete();

        return redirect()->route('admin.membership.index')->with('status', 'Membership deleted successfully.');
    }

    private function memberships(): Builder
    {
        return SubscriptionProduct::query()->where('product_for', SubscriptionProduct::FOR_MEMBERSHIP);
    }

    /** @return array{currencies: Collection, subscriptionTypes: Collection} */
    private function formOptions(): array
    {
        return ['currencies' => Currency::query()->where('isActive', true)->orderBy('code')->get(), 'subscriptionTypes' => SubscriptionType::query()->where('isActive', true)->orderBy('name')->get()];
    }
}
