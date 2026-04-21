<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_policy_protected_admin_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.apartments.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.amenities.index'))
            ->assertOk();
    }

    public function test_non_admin_cannot_access_admin_area(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.apartments.index'))
            ->assertForbidden();
    }
}
