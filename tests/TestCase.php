<?php

namespace Tests;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function actingAsTestUser()
    {
        $user = User::factory()->create();
        return $this->actingAs($user);
    }
    protected function actingAsSanctum()
    {
        return Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        // $user = User::factory()->create();
        // return $this->actingAs($user);
    }


    // public function userCreate()
    // {
    //     Sanctum::actingAs(
    //         User::factory()->create(),
    //         ['*']
    //     );

    // }
}