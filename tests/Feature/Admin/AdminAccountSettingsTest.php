<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccountSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_account_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee(__('Settings'), false);
    }

    public function test_non_admin_cannot_view_account_settings(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.settings.edit'))
            ->assertForbidden();
    }

    public function test_admin_can_update_name_and_email(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), [
                'name' => 'New Name',
                'email' => 'new@example.com',
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $admin->refresh();
        $this->assertSame('New Name', $admin->name);
        $this->assertSame('new@example.com', $admin->email);
    }
}
