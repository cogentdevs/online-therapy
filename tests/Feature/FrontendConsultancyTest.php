<?php

use App\Models\Consultancy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function publicConsultancy(array $attributes = []): Consultancy
{
    return Consultancy::query()->forceCreate(array_replace([
        'language' => 'en',
        'title' => 'Business Consultancy',
        'button_label' => 'Book Consultancy',
        'image' => 'backend-images/consultancy/business.png',
        'short_description' => 'Practical advice for growing teams.',
        'description' => '<p>Detailed consultancy guidance.</p>',
        'duration_type' => 'hours',
        'duration_value' => 2,
        'consultancy_medium' => 'video_call',
        'isActive' => true,
        'isFeatured' => false,
        'status' => Consultancy::STATUS_PUBLISHED,
        'published_at' => now()->subMinute(),
    ], $attributes));
}

test('listing shows only publicly available Consultancies with their own presentation values', function (): void {
    $visible = publicConsultancy();
    publicConsultancy(['title' => 'Draft Consultancy', 'status' => Consultancy::STATUS_DRAFT]);
    publicConsultancy(['title' => 'Inactive Consultancy', 'isActive' => false]);
    publicConsultancy(['title' => 'Future Consultancy', 'published_at' => now()->addHour()]);

    $this->get(route('front.consultancies.index'))
        ->assertSuccessful()
        ->assertViewIs('frontend.consultancies.index')
        ->assertSee($visible->title)
        ->assertSee($visible->short_description)
        ->assertSee('2 Hours')
        ->assertSee('Video Call')
        ->assertSee('Book Consultancy')
        ->assertSee(route('front.consultancies.show', $visible), false)
        ->assertDontSee('Draft Consultancy')
        ->assertDontSee('Inactive Consultancy')
        ->assertDontSee('Future Consultancy')
        ->assertDontSee('created_by', false)
        ->assertDontSee('owner_admin_id', false);
});

test('detail blocks ineligible Consultancies and shows eligible directional related Consultancies', function (): void {
    $consultancy = publicConsultancy(['title' => 'Consultancy A']);
    $eligibleRelated = publicConsultancy(['title' => 'Consultancy B', 'button_label' => 'Get Advice']);
    $draftRelated = publicConsultancy(['title' => 'Consultancy C', 'status' => Consultancy::STATUS_DRAFT]);
    $inactiveRelated = publicConsultancy(['title' => 'Consultancy D', 'isActive' => false]);
    $reverseOnly = publicConsultancy(['title' => 'Consultancy E']);

    $consultancy->relatedConsultancies()->attach([$eligibleRelated->id, $draftRelated->id, $inactiveRelated->id]);
    $reverseOnly->relatedConsultancies()->attach($consultancy->id);

    $this->get(route('front.consultancies.show', $consultancy))
        ->assertSuccessful()
        ->assertSee('Consultancy A')
        ->assertSee('Detailed consultancy guidance.', false)
        ->assertDontSee('Book Consultancy')
        ->assertSee('Consultancy B')
        ->assertSee('Get Advice')
        ->assertDontSee('Consultancy C')
        ->assertDontSee('Consultancy D')
        ->assertDontSee('Consultancy E');

    $this->get(route('front.consultancies.show', $draftRelated))->assertNotFound();
    $this->get(route('front.consultancies.show', $inactiveRelated))->assertNotFound();
    $this->get(route('front.consultancies.show', publicConsultancy(['published_at' => now()->addHour()])))->assertNotFound();
    $this->get(route('front.consultancies.show', 999999))->assertNotFound();
});

test('listing renders an empty state when no Consultancy is publicly available', function (): void {
    publicConsultancy(['status' => Consultancy::STATUS_DRAFT]);

    $this->get(route('front.consultancies.index'))
        ->assertSuccessful()
        ->assertSee('No consultancies are currently available.');
});
