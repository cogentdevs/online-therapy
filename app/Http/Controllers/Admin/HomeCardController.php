<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHomeCardRequest;
use App\Http\Requests\Admin\UpdateHomeCardRequest;
use App\Models\HomeCard;
use App\Models\HomeCardTitlePosition;
use App\Models\Language;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class HomeCardController extends Controller
{
    private const IMAGE_DIRECTORY = 'images/backend-images/home-cards';

    public function index(): View
    {
        $homeCards = HomeCard::query()->with('creator.roles')->latest()->get();

        return view('admin.home-cards.view-home-card', [
            'homeCards' => $homeCards,
            'languageNames' => Language::query()->whereIn('code', $homeCards->pluck('language')->filter())->pluck('name', 'code'),
        ]);
    }

    public function create(): View
    {
        return view('admin.home-cards.add-home-card', ['languages' => $this->activeLanguages(), 'positions' => HomeCard::POSITIONS]);
    }

    public function cardTitlePosition(): View
    {
        $titlePositions = HomeCardTitlePosition::query()
            ->where('language', config('content_language.code'))
            ->whereIn('card_position', array_keys(HomeCard::POSITIONS))
            ->pluck('title_position', 'card_position');

        $resolveTitlePosition = static fn (mixed $position): string => in_array($position, ['center', 'left', 'right'], true)
            ? $position
            : 'right';

        return view('admin.home-cards.card-title-position', [
            'aboveFooterTitlePosition' => $resolveTitlePosition($titlePositions->get('above footer')),
            'belowSliderTitlePosition' => $resolveTitlePosition($titlePositions->get('below slider')),
        ]);
    }

    public function updateCardTitlePosition(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'below_slider_title_position' => ['required', 'in:center,left,right'],
            'above_footer_title_position' => ['required', 'in:center,left,right'],
        ]);

        DB::transaction(function () use ($validated): void {
            HomeCardTitlePosition::query()->updateOrCreate(
                ['language' => config('content_language.code'), 'card_position' => 'below slider'],
                ['title_position' => $validated['below_slider_title_position']],
            );
            HomeCardTitlePosition::query()->updateOrCreate(
                ['language' => config('content_language.code'), 'card_position' => 'above footer'],
                ['title_position' => $validated['above_footer_title_position']],
            );
        });

        return redirect()
            ->route('admin.home-cards.card-title-position')
            ->with('status', 'Card title positions updated successfully.');
    }

    public function store(StoreHomeCardRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $imagePath = null;

        try {
            if ($request->hasFile('image')) {
                $imagePath = $this->storeImage($request->file('image'));
            }

            DB::transaction(fn () => HomeCard::query()->create([...Arr::except($validated, ['image']), 'image' => $imagePath]));
        } catch (Throwable $exception) {
            $this->deleteManagedImage($imagePath);
            throw $exception;
        }

        return redirect()->route('admin.home-cards.index')->with('status', 'Home Card added successfully.');
    }

    public function show(int $id): View
    {
        $homeCard = HomeCard::query()->with(['creator.roles', 'updater.roles'])->findOrFail($id);

        return view('admin.home-cards.view-home-card-detail', [
            'homeCard' => $homeCard,
            'languageName' => Language::query()->where('code', $homeCard->language)->value('name'),
        ]);
    }

    public function edit(int $id): View
    {
        return view('admin.home-cards.edit-home-card', ['homeCard' => HomeCard::query()->findOrFail($id), 'languages' => $this->activeLanguages(), 'positions' => HomeCard::POSITIONS]);
    }

    public function update(UpdateHomeCardRequest $request, int $id): RedirectResponse
    {
        $homeCard = HomeCard::query()->findOrFail($id);
        $data = Arr::except($request->validated(), ['image']);
        $newImagePath = null;
        $previousImagePath = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $this->storeImage($request->file('image'));
                $data['image'] = $newImagePath;
                $previousImagePath = $homeCard->image;
            }

            DB::transaction(fn () => $homeCard->update($data));
        } catch (Throwable $exception) {
            $this->deleteManagedImage($newImagePath);
            throw $exception;
        }

        $this->deleteManagedImage($previousImagePath);

        return redirect()->route('admin.home-cards.index')->with('status', 'Home Card updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $homeCard = HomeCard::query()->findOrFail($id);
        $imagePath = $homeCard->image;
        DB::transaction(fn () => $homeCard->delete());
        $this->deleteManagedImage($imagePath);

        return redirect()->route('admin.home-cards.index')->with('status', 'Home Card deleted successfully.');
    }

    /** @return Collection<int, Language> */
    private function activeLanguages(): Collection
    {
        return Language::query()->where('is_active', true)->orderBy('name')->get(['name', 'code']);
    }

    private function storeImage(UploadedFile $image): string
    {
        $directory = public_path(self::IMAGE_DIRECTORY);
        File::ensureDirectoryExists($directory);
        $filename = Str::ulid().'.'.Str::lower($image->extension());
        $image->move($directory, $filename);

        return self::IMAGE_DIRECTORY.'/'.$filename;
    }

    private function deleteManagedImage(?string $path): void
    {
        if (! is_string($path)) {
            return;
        }

        $normalizedPath = str_replace('\\', '/', $path);
        $filename = basename($normalizedPath);

        if ($filename !== '' && $filename !== '.' && $filename !== '..' && $normalizedPath === self::IMAGE_DIRECTORY.'/'.$filename) {
            File::delete(public_path($normalizedPath));
        }
    }
}
