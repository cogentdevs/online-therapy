<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentAccountRequest;
use App\Http\Requests\Admin\UpdatePaymentAccountRequest;
use App\Models\PaymentAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentAccountsController extends Controller
{
    public function index(): View
    {
        return view('admin.payment-account.view-payment-account', [
            'paymentAccounts' => PaymentAccount::query()->with('creator.roles')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.payment-account.add-payment-account');
    }

    public function store(StorePaymentAccountRequest $request): RedirectResponse
    {
        PaymentAccount::query()->create([
            ...$request->validated(),
            'is_active' => true,
        ]);

        return redirect()->route('admin.payment-account.index')->with('status', 'Payment Account added successfully.');
    }

    public function edit(int $id): View
    {
        return view('admin.payment-account.edit-payment-account', [
            'paymentAccount' => PaymentAccount::query()->findOrFail($id),
        ]);
    }

    public function update(UpdatePaymentAccountRequest $request, int $id): RedirectResponse
    {
        PaymentAccount::query()->findOrFail($id)->update($request->validated());

        return redirect()->route('admin.payment-account.index')->with('status', 'Payment Account updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        PaymentAccount::query()->findOrFail($id)->delete();

        return redirect()->route('admin.payment-account.index')->with('status', 'Payment Account deleted successfully.');
    }
}
