<?php

namespace App\Policies\Concerns;

use App\Models\User;

/**
 * Admin users are allowed in before(); other users are denied.
 *
 * Laravel only registers the policy callback if concrete ability methods exist
 * (e.g. viewAny), so we define stubs that return false — before() runs first
 * and grants admins without calling them.
 */
trait HandlesAdminAccess
{
    public function before(?User $user, string $ability, mixed ...$args): ?bool
    {
        return $user?->isAdmin() === true ? true : false;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, mixed $model): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, mixed $model): bool
    {
        return false;
    }

    public function delete(User $user, mixed $model): bool
    {
        return false;
    }
}
