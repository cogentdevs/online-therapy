<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAuthorSettingRequest;
use App\Models\AuthorGeneralSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AuthorSettingController extends Controller
{
    public function edit(): View
    {
        $storedSettings = AuthorGeneralSetting::query()
            ->whereIn('column_name', AuthorGeneralSetting::configurableFields())
            ->get(['column_name', 'isView'])
            ->keyBy('column_name');
        $visibilityValues = [];

        foreach (AuthorGeneralSetting::DEFAULT_VISIBILITY as $field => $defaultValue) {
            $visibilityValues[$field] = (bool) ($storedSettings->get($field)?->isView ?? $defaultValue);
        }

        return view('admin.author.author-setting', [
            'fieldLabels' => AuthorGeneralSetting::FIELD_LABELS,
            'visibilityValues' => $visibilityValues,
        ]);
    }

    public function update(UpdateAuthorSettingRequest $request): RedirectResponse
    {
        $visibilityValues = $request->validated('visibility');

        DB::transaction(function () use ($visibilityValues): void {
            foreach (AuthorGeneralSetting::configurableFields() as $field) {
                AuthorGeneralSetting::query()->updateOrCreate(
                    ['column_name' => $field],
                    ['isView' => (bool) $visibilityValues[$field]],
                );
            }
        });

        return redirect()
            ->route('admin.author.settings')
            ->with('status', 'Author settings updated successfully.');
    }
}
