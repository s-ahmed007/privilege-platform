<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;
use Tests\DatabaseSetup;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseSetup;

    protected function signIn($user = null)
    {
        if (! $user) {
            $user = create('App\Models\UserAccount');
            create('App\Models\UserProfile', ['user_id' => $user->id]);
        }

        $this->actingAs($user);

        return $this;
    }

    protected function signInAdmin($user = null, $role = null)
    {
        if (! $user) {
            $user = create('App\Models\Admin');
        }

        $role = Role::create(['name' => $role ?: 'super-admin', 'guard_name' => 'admin']);
        $user->assignRole($role);
        $this->actingAs($user, 'admin');

        return $this;
    }
}
