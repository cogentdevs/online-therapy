<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSubscriptionNotificationSettingRequest;
use App\Models\SubscriptionNotificationSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SubscriptionNotificationSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.subscription-notification-settings.edit', [
            'setting' => SubscriptionNotificationSetting::current(),
        ]);
    }

    public function update(UpdateSubscriptionNotificationSettingRequest $request): RedirectResponse
    {
        $setting = SubscriptionNotificationSetting::current();
        $setting->fill($request->validated())->save();

        return redirect()
            ->route('admin.subscription-notification-settings.edit')
            ->with('status', 'Subscription reminder settings updated successfully.');
    }
}
