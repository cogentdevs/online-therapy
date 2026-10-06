<?php

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function publicCourse(array $attributes = []): Course
{
    return Course::query()->forceCreate(array_replace([
        'language' => 'en',
        'title' => 'React Native Complete Course',
        'button_label' => 'View Course',
        'image' => 'backend-images/course/react-native.png',
        'short_description' => 'Build cross-platform mobile applications.',
        'description' => '<p>Complete Course description.</p>',
        'duration_type' => 'hours',
        'duration_value' => 8,
        'isActive' => true,
        'isFeatured' => false,
        'status' => Course::STATUS_PUBLISHED,
        'published_at' => now()->subMinute(),
    ], $attributes));
}

test('listing exposes only publicly eligible Courses with individual card labels', function (): void {
    $visible = publicCourse();
    $second = publicCourse(['title' => 'Laravel Course', 'button_label' => 'Explore Course']);
    publicCourse(['title' => 'Draft Course', 'status' => Course::STATUS_DRAFT]);
    publicCourse(['title' => 'Inactive Course', 'isActive' => false]);
    publicCourse(['title' => 'Future Course', 'published_at' => now()->addHour()]);

    $this->get(route('front.courses.index'))
        ->assertSuccessful()
        ->assertViewIs('frontend.courses.index')
        ->assertSee($visible->title)
        ->assertSee($visible->short_description)
        ->assertSee('8 Hours')
        ->assertSee('View Course')
        ->assertSee('Explore Course')
        ->assertSee(route('front.courses.show', $visible), false)
        ->assertSee(route('front.courses.show', $second), false)
        ->assertDontSee('Draft Course')
        ->assertDontSee('Inactive Course')
        ->assertDontSee('Future Course')
        ->assertDontSee('owner_admin_id', false);
});

test('detail protects unpublished Courses and filters directional related Courses', function (): void {
    $course = publicCourse(['title' => 'Course A', 'button_label' => 'A Label']);
    $eligible = publicCourse(['title' => 'Course B', 'button_label' => 'Explore B']);
    $draft = publicCourse(['title' => 'Course C', 'status' => Course::STATUS_DRAFT]);
    $inactive = publicCourse(['title' => 'Course D', 'isActive' => false]);
    $future = publicCourse(['title' => 'Course E', 'published_at' => now()->addHour()]);
    $reverse = publicCourse(['title' => 'Course F']);
    $course->relatedCourses()->attach([$eligible->id, $draft->id, $inactive->id, $future->id]);
    $reverse->relatedCourses()->attach($course->id);

    $this->get(route('front.courses.show', $course))
        ->assertSuccessful()
        ->assertSee('Course A')
        ->assertSee('Complete Course description.', false)
        ->assertDontSee('A Label')
        ->assertSee('Course B')
        ->assertSee('Explore B')
        ->assertDontSee('Course C')
        ->assertDontSee('Course D')
        ->assertDontSee('Course E')
        ->assertDontSee('Course F');

    $this->get(route('front.courses.show', $draft))->assertNotFound();
    $this->get(route('front.courses.show', $inactive))->assertNotFound();
    $this->get(route('front.courses.show', $future))->assertNotFound();
    $this->get(route('front.courses.show', 999999))->assertNotFound();
});

test('listing has a clean empty state when no Course is publicly available', function (): void {
    publicCourse(['status' => Course::STATUS_DRAFT]);

    $this->get(route('front.courses.index'))
        ->assertSuccessful()
        ->assertSee('No courses are currently available.');
});
