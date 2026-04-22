<?php

namespace Tests\Feature;

use App\Models\Apartment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_german_home_shows_200_and_sets_app_locale_german(): void
    {
        $this->seed();
        $response = $this->get('/de');
        $response->assertOk();
        $this->assertSame('de', (string) app()->getLocale());
    }

    public function test_italian_lake_garda_shows_200(): void
    {
        $this->seed();
        $this->get('/it/lake-garda')->assertOk();
        $this->assertSame('it', (string) app()->getLocale());
    }

    public function test_german_apartment_detail_ok(): void
    {
        $this->seed();
        $a = Apartment::query()->orderBy('id')->first();
        $this->assertNotNull($a);
        $this->get('/de/apartments/'.$a->slug)->assertOk();
        $this->assertSame('de', (string) app()->getLocale());
    }
}
