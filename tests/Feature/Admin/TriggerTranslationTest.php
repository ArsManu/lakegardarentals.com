<?php

namespace Tests\Feature\Admin;

use App\Jobs\TranslateModelContentJob;
use App\Models\Amenity;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TriggerTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_post_translate(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $page = Page::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.translate'), [
                'type' => 'page',
                'id' => (int) $page->getKey(),
            ])
            ->assertStatus(403);
    }

    public function test_admin_without_openai_sees_warning(): void
    {
        $admin = User::factory()->admin()->create();
        $page = Page::factory()->create();
        config(['services.openai.key' => '']);

        $this->actingAs($admin)
            ->from(route('admin.pages.edit', $page))
            ->post(route('admin.translate'), [
                'type' => 'page',
                'id' => (int) $page->getKey(),
            ])
            ->assertRedirect(route('admin.pages.edit', $page))
            ->assertSessionHas('warning');
    }

    public function test_admin_post_translate_dispatches_queued_full_job(): void
    {
        config(['services.openai.key' => 'sk-test-fake']);
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $amenity = Amenity::factory()->create(['name' => 'Rooftop lounge']);

        $this->actingAs($admin)
            ->from(route('admin.amenities.edit', $amenity))
            ->post(route('admin.translate'), [
                'type' => 'amenity',
                'id' => (int) $amenity->getKey(),
            ])
            ->assertRedirect(route('admin.amenities.edit', $amenity))
            ->assertSessionHas('success');

        Queue::assertPushed(TranslateModelContentJob::class, function (TranslateModelContentJob $job) use ($amenity) {
            return $job->modelType === Amenity::class
                && $job->id === (int) $amenity->getKey()
                && $job->onlyAttributes === null
                && $job->blocksBefore === null;
        });
    }
}
