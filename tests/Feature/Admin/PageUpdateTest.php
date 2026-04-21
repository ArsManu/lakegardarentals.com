<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PageUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_home_page_images(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $page = Page::factory()->create([
            'slug' => 'home',
            'title' => 'Home',
            'blocks' => [],
        ]);

        $response = $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'title' => 'Updated home',
            'blocks' => [
                'hero_slides' => [
                    [
                        'eyebrow' => 'Lake Garda',
                        'title' => 'Slide one',
                        'subtitle' => 'Slide subtitle',
                        'image_path' => '',
                        'image_alt' => 'Lake view terrace',
                        'primary_cta_label' => 'Book this stay',
                        'primary_cta_url' => '/contact#inquiry',
                        'secondary_cta_label' => 'Call host',
                        'secondary_cta_url' => 'tel:+390123456789',
                    ],
                ],
                'cta_title' => 'CTA',
                'cta_text' => 'CTA text',
            ],
            'hero_slide_image_file' => [
                0 => UploadedFile::fake()->image('hero.jpg'),
            ],
        ]);

        $response
            ->assertRedirect(route('admin.pages.edit', $page))
            ->assertSessionHas('success');

        $page->refresh();

        $this->assertSame('Updated home', $page->title);
        $this->assertNotEmpty($page->blocks['hero_slides'][0]['image_path'] ?? null);
        $this->assertArrayNotHasKey('fallback_hero_image_path', $page->blocks);
        $this->assertSame('Lake Garda', $page->blocks['hero_slides'][0]['eyebrow'] ?? null);
        $this->assertSame('Lake view terrace', $page->blocks['hero_slides'][0]['image_alt'] ?? null);
        $this->assertSame('Book this stay', $page->blocks['hero_slides'][0]['primary_cta_label'] ?? null);
        $this->assertSame('/contact#inquiry', $page->blocks['hero_slides'][0]['primary_cta_url'] ?? null);

        $this->assertTrue(Storage::disk('public')->exists($page->blocks['hero_slides'][0]['image_path']));
        $this->assertStringEndsWith('.webp', $page->blocks['hero_slides'][0]['image_path']);
    }

    public function test_admin_can_open_home_hero_slideshow_editor(): void
    {
        $admin = User::factory()->admin()->create();
        $page = Page::factory()->create([
            'slug' => 'home',
            'title' => 'Home',
            'blocks' => [],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.pages.hero-slideshow.edit', $page))
            ->assertOk()
            ->assertSee('Home hero slideshow', false);
    }

    public function test_admin_can_save_home_hero_slideshow_on_dedicated_page(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $page = Page::factory()->create([
            'slug' => 'home',
            'title' => 'Home',
            'blocks' => [
                'cta_title' => 'Hello CTA',
            ],
        ]);

        $response = $this->actingAs($admin)->put(route('admin.pages.hero-slideshow.update', $page), [
            'blocks' => [
                'hero_slides' => [
                    [
                        'eyebrow' => 'From editor',
                        'title' => 'Dedicated page',
                        'subtitle' => 'Subtitle',
                        'image_path' => '',
                        'image_alt' => 'Alt',
                        'primary_cta_label' => 'Book',
                        'primary_cta_url' => '/contact',
                        'secondary_cta_label' => '',
                        'secondary_cta_url' => '',
                    ],
                ],
            ],
            'hero_slide_image_file' => [
                0 => UploadedFile::fake()->image('slide.jpg'),
            ],
        ]);

        $response
            ->assertRedirect(route('admin.pages.hero-slideshow.edit', $page))
            ->assertSessionHas('success');

        $page->refresh();
        $this->assertSame('From editor', $page->blocks['hero_slides'][0]['eyebrow'] ?? null);
        $this->assertSame('Hello CTA', $page->blocks['cta_title'] ?? null);
        $this->assertNotEmpty($page->blocks['hero_slides'][0]['image_path'] ?? null);
        $this->assertTrue(Storage::disk('public')->exists($page->blocks['hero_slides'][0]['image_path']));
    }

    public function test_home_page_update_without_hero_slides_preserves_existing_slides(): void
    {
        $admin = User::factory()->admin()->create();
        $page = Page::factory()->create([
            'slug' => 'home',
            'title' => 'Home',
            'blocks' => [
                'hero_slides' => [
                    [
                        'eyebrow' => 'Keep me',
                        'title' => 'Slide',
                        'subtitle' => '',
                        'image_path' => 'pages/1/hero/keep.webp',
                        'image_alt' => 'Alt',
                        'primary_cta_label' => '',
                        'primary_cta_url' => '',
                        'secondary_cta_label' => '',
                        'secondary_cta_url' => '',
                    ],
                ],
                'cta_title' => 'Old CTA',
                'flex_blocks' => [],
            ],
        ]);

        $response = $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'title' => 'Home updated',
            'blocks' => [
                'cta_title' => 'New CTA',
                'why_points' => [
                    ['title' => '', 'text' => ''],
                    ['title' => '', 'text' => ''],
                    ['title' => '', 'text' => ''],
                    ['title' => '', 'text' => ''],
                ],
                'cta_text' => '',
                'flex_blocks' => [],
            ],
        ]);

        $response->assertRedirect(route('admin.pages.edit', $page));

        $page->refresh();
        $this->assertSame('Keep me', $page->blocks['hero_slides'][0]['eyebrow'] ?? null);
        $this->assertSame('New CTA', $page->blocks['cta_title'] ?? '');
    }

    public function test_admin_can_save_contact_phone_and_email(): void
    {
        $admin = User::factory()->admin()->create();
        $page = Page::factory()->create([
            'slug' => 'contact',
            'title' => 'Contact',
            'blocks' => [],
        ]);

        $response = $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'title' => 'Contact',
            'blocks' => [
                'hero_title' => 'Contact us',
                'hero_subtitle' => 'We reply quickly.',
                'reassurance' => 'CET hours.',
                'contact_phone' => '+39 111 222 3333',
                'contact_email' => 'bookings@example.com',
            ],
        ]);

        $response
            ->assertRedirect(route('admin.pages.edit', $page))
            ->assertSessionHas('success');

        $page->refresh();
        $this->assertSame('+39 111 222 3333', $page->blocks['contact_phone'] ?? null);
        $this->assertSame('bookings@example.com', $page->blocks['contact_email'] ?? null);
    }

    public function test_contact_blocks_override_phone_and_email_in_public_header_and_footer(): void
    {
        Page::factory()->create([
            'slug' => 'home',
            'title' => 'Home',
            'blocks' => [],
        ]);

        Page::factory()->create([
            'slug' => 'contact',
            'title' => 'Contact',
            'blocks' => [
                'contact_phone' => '+39 111 222 3333',
                'contact_email' => 'bookings@example.com',
            ],
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('+39 111 222 3333', false)
            ->assertSee('bookings@example.com', false)
            ->assertSee('tel:+391112223333', false);
    }

    public function test_admin_can_save_contact_flex_block_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $page = Page::factory()->create([
            'slug' => 'contact',
            'title' => 'Contact',
            'blocks' => [],
        ]);

        $response = $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'title' => 'Contact',
            'blocks' => [
                'hero_title' => 'Contact & booking requests',
                'hero_subtitle' => 'Send your dates',
                'reassurance' => 'We reply quickly',
                'contact_phone' => '+39 111 222 3333',
                'contact_email' => 'bookings@example.com',
                'flex_blocks' => [
                    [
                        'type' => 'split_media',
                        'layout' => 'image_left',
                        'image_path' => '',
                        'image_alt' => 'Lake Garda view',
                        'heading' => 'Flexible heading',
                        'body_html' => '<p>Flexible body</p>',
                    ],
                ],
            ],
            'flex_block_image_file' => [
                0 => UploadedFile::fake()->image('contact-flex.jpg'),
            ],
        ]);

        $response
            ->assertRedirect(route('admin.pages.edit', $page))
            ->assertSessionHas('success');

        $page->refresh();
        $saved = $page->blocks['flex_blocks'][0] ?? [];

        $this->assertSame('split_media', $saved['type'] ?? null);
        $this->assertNotEmpty($saved['image_path'] ?? null);
        $this->assertStringEndsWith('.webp', $saved['image_path']);
        $this->assertTrue(Storage::disk('public')->exists($saved['image_path']));
    }

    public function test_home_page_renders_modern_hero_slider_content(): void
    {
        Page::factory()->create([
            'slug' => 'home',
            'title' => 'Home',
            'blocks' => [
                'hero_slides' => [
                    [
                        'eyebrow' => 'Boutique stays',
                        'title' => 'A more refined Lake Garda stay',
                        'subtitle' => 'Quiet apartments with direct host support and an elegant base in Garda.',
                        'image_path' => 'pages/1/hero/example.jpg',
                        'image_alt' => 'Balcony view across Lake Garda',
                        'primary_cta_label' => 'Reserve your dates',
                        'primary_cta_url' => '/contact#inquiry',
                        'secondary_cta_label' => 'Call the host',
                        'secondary_cta_url' => 'tel:+390123456789',
                    ],
                ],
            ],
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('A more refined Lake Garda stay')
            ->assertSee('Boutique stays')
            ->assertSee('Reserve your dates')
            ->assertSee('data-home-hero-slider', false)
            ->assertSee('/storage/pages/1/hero/example.jpg');
    }

    public function test_home_hero_reuses_first_slide_copy_on_later_slides_when_empty(): void
    {
        Page::factory()->create([
            'slug' => 'home',
            'title' => 'Home',
            'blocks' => [
                'hero_slides' => [
                    [
                        'eyebrow' => 'EyebrowOne',
                        'title' => 'UniqueFirstSlideHeadline',
                        'subtitle' => '<p>First sub</p>',
                        'image_path' => 'pages/1/hero/a.jpg',
                        'image_alt' => 'A',
                        'primary_cta_label' => 'Book',
                        'primary_cta_url' => '/contact',
                        'secondary_cta_label' => '',
                        'secondary_cta_url' => '',
                    ],
                    [
                        'eyebrow' => '',
                        'title' => '',
                        'subtitle' => '',
                        'image_path' => 'pages/1/hero/b.jpg',
                        'image_alt' => 'B',
                        'primary_cta_label' => '',
                        'primary_cta_url' => '',
                        'secondary_cta_label' => '',
                        'secondary_cta_url' => '',
                    ],
                ],
            ],
        ]);

        $html = $this->get(route('home'))->assertOk()->content();

        $this->assertGreaterThanOrEqual(2, substr_count($html, 'UniqueFirstSlideHeadline'));
        $this->assertGreaterThanOrEqual(2, substr_count($html, 'EyebrowOne'));
        $this->assertGreaterThanOrEqual(2, substr_count($html, 'Book'));
    }
}
