<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCurrencyRequest;
use App\Http\Requests\Admin\UpdateCurrencyRequest;
use App\Models\Currency;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CurrencyController extends Controller
{
    public function index(): View
    {
        return view('admin.currency.view-currency', [
            'currencies' => Currency::query()->with('creator.roles')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.currency.add-currency');
    }

    public function store(StoreCurrencyRequest $request): RedirectResponse
    {
        Currency::query()->create([
            ...$request->validated(),
            'isActive' => true,
        ]);

        return redirect()->route('admin.currency.index')->with('status', 'Currency added successfully.');
    }

    public function edit(int $id): View
    {
        return view('admin.currency.edit-currency', [
            'currency' => Currency::query()->findOrFail($id),
        ]);
    }

    public function update(UpdateCurrencyRequest $request, int $id): RedirectResponse
    {
        Currency::query()->findOrFail($id)->update($request->validated());

        return redirect()->route('admin.currency.index')->with('status', 'Currency updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Currency::query()->findOrFail($id)->delete();

        return redirect()->route('admin.currency.index')->with('status', 'Currency deleted successfully.');
    }
}
