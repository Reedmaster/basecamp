<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    protected function signIn($user = null)
    {
        // If no user is given then one is created
        $user = $user ?: User::factory()->create();

        // Created user is set as signed in user
        $this->actingAs($user);

        return $user;
    }
}
