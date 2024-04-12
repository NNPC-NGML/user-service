<?php

namespace Tests\Feature\Package;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScopeAndPackageTest extends TestCase
{

    use RefreshDatabase;

    public function test_to_access_with_correct_scope()
    {
        $this->actingAsTestUser()->getJson("/api/scope/test")->assertOk();
    }

    public function test_to_access_with_incorrect_scope_returns_401()
    {
        $response = $this->getJson("/api/scope/test");
        $response->assertStatus(401);
    }
}