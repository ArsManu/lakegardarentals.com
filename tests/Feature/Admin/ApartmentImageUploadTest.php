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
}
