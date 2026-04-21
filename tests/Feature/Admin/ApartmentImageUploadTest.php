<?php

namespace Tests\Feature\Admin;

use App\Models\Apartment;
use App\Models\ApartmentImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApartmentImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_one_gallery_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $apartment = Apartment::factory()->create();

        $file = UploadedFile::fake()->image('room.jpg', 800, 600);

        $response = $this->actingAs($admin)->post(
            route('admin.apartments.images.store', $apartment),
            [
                'images' => [$file],
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertSame(1, ApartmentImage::query()->where('apartment_id', $apartment->id)->count());

        $img = ApartmentImage::query()->where('apartment_id', $apartment->id)->first();
        $this->assertNotNull($img);
        $this->assertStringEndsWith('.webp', $img->path);
        Storage::disk('public')->assertExists($img->path);
    }

    public function test_admin_can_upload_single_file_as_sole_uploadedfile_not_array(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $apartment = Apartment::factory()->create();
        $file = UploadedFile::fake()->image('one.jpg', 640, 480);

        // Same shape some PHP/browser combos use for a single chosen file (not wrapped in array).
        $response = $this->actingAs($admin)->post(
            route('admin.apartments.images.store', $apartment),
            [
                'images' => $file,
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertSame(1, ApartmentImage::query()->where('apartment_id', $apartment->id)->count());
    }

    public function test_admin_can_set_gallery_image_sort_priority(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $apartment = Apartment::factory()->create();
        $image = ApartmentImage::factory()->create([
            'apartment_id' => $apartment->id,
            'sort_order' => 10,
        ]);

        $response = $this->actingAs($admin)->patch(
            route('admin.apartments.images.update', [$apartment, $image]),
            [
                'alt_text' => 'Living room',
                'sort_order' => 3,
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $image->refresh();
        $this->assertSame(3, $image->sort_order);
        $this->assertSame('Living room', $image->alt_text);
    }
}
