<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePageSectionHeading;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomePageSectionHeadingController extends Controller
{
    /** @var array<string, array{label: string, fields: list<string>}> */
    private const SECTIONS = [
        'latest_articles' => ['label' => 'Slider Right - Latest Articles', 'fields' => ['main_title', 'content_position']],
        'below_slider_home_cards' => ['label' => 'Below Slider - Home Cards', 'fields' => ['short_title', 'main_title', 'short_detail', 'content_position']],
        'editorial' => ['label' => 'Editorial', 'fields' => ['main_title', 'content_position']],
        'weekly_magazine' => ['label' => 'Weekly Magazine', 'fields' => ['main_title', 'content_position']],
        'audio' => ['label' => 'Audio', 'fields' => ['main_title', 'content_position']],
        'newsletter' => ['label' => 'Newsletter', 'fields' => ['main_title', 'short_detail', 'content_position']],
        'advertise_with_us' => ['label' => 'Advertise With Us', 'fields' => ['main_title', 'short_detail', 'content_position']],
        'categories' => ['label' => 'Category Section', 'fields' => ['short_title', 'main_title', 'short_detail', 'content_position']],
        'above_footer_home_cards' => ['label' => 'Above Footer - Home Cards', 'fields' => ['short_title', 'main_title', 'short_detail', 'content_position']],
    ];

    public function index(): View
    {
        $headings = HomePageSectionHeading::query()
            ->where('language', config('content_language.code'))
            ->whereIn('section_name', array_keys(self::SECTIONS))
            ->get()
            ->keyBy('section_name');

        return view('admin.home-heading.home-heading', [
            'headings' => $headings,
            'sections' => self::SECTIONS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->validationRules());
        $submittedSections = $validated['sections'] ?? [];

        DB::transaction(function () use ($submittedSections): void {
            foreach (self::SECTIONS as $sectionName => $configuration) {
                $submittedSection = $submittedSections[$sectionName] ?? [];
                $values = [
                    'content_position' => null,
                    'short_title' => null,
                    'main_title' => null,
                    'short_detail' => null,
                ];

                foreach ($configuration['fields'] as $field) {
                    $values[$field] = $submittedSection[$field] ?? null;
                }

                HomePageSectionHeading::query()->updateOrCreate(
                    ['language' => config('content_language.code'), 'section_name' => $sectionName],
                    $values,
                );
            }
        });

        return redirect()
            ->route('admin.home-headings.index')
            ->with('status', 'Home headings updated successfully.');
    }

    /** @return array<string, list<string>> */
    private function validationRules(): array
    {
        $rules = ['sections' => ['nullable', 'array']];

        foreach (self::SECTIONS as $sectionName => $configuration) {
            foreach ($configuration['fields'] as $field) {
                $rules["sections.{$sectionName}.{$field}"] = match ($field) {
                    'content_position' => ['nullable', 'in:center,left,right'],
                    default => ['nullable', 'string', 'max:255'],
                };
            }
        }

        return $rules;
    }
}
