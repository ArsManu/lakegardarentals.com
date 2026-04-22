<?php

namespace Tests\Feature;

use App\Jobs\TranslateModelContentJob;
use App\Models\Amenity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TranslationIncrementalDispatchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.openai.key' => 'test-key-for-queued-dispatch']);
    }

    public function test_no_translation_job_when_only_non_translatable_fields_change(): void
    {
        $amenity = Amenity::factory()->create(['name' => 'WiFi', 'sort_order' => 1]);
        $amenity->refresh();
        Queue::fake();
        $amenity->update(['sort_order' => 5]);
        Queue::assertNothingPushed();
    }

    public function test_translatable_field_change_pushes_job_with_scoped_only_attributes(): void
    {
        $amenity = Amenity::factory()->create(['name' => 'Gym', 'sort_order' => 1]);
        $amenity->refresh();
        Queue::fake();
        $amenity->update(['name' => 'Fitness room']);
        Queue::assertPushed(TranslateModelContentJob::class, function (TranslateModelContentJob $job) use ($amenity) {
            return $job->modelType === Amenity::class
                && $job->id === (int) $amenity->getKey()
                && $job->onlyAttributes === ['name']
                && $job->blocksBefore === null;
        });
    }
}
