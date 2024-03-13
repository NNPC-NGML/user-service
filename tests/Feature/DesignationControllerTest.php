<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DesignationControllerTest extends TestCase
{
    public function testCreateDesignation()
    {
        $data = [
            "role" => "role name",
            "description" => "description",
            "level" => "level 2"
        ];

        $response = $this->postJson(route('designations.create', $data));

        $response->assertStatus(201);

        $this->assertDatabaseHas('designations', $data);
    }
    /** @test */
    public function testValidationErrors()
    {
        $data = [
            "role" => "",
            "description" => "",
        ];

        $response = $this->postJson(route('designations.create', $data));

        $response->assertStatus(422);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'role' => ['The role field is required.'],
                    'description' => ['The description field is required.'],
                ]
            ]);
    }
}
