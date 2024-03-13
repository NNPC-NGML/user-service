<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Designation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class DesignationControllerTest extends TestCase
{
    use RefreshDatabase;
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


    public function testUpdateDesignationSuccessfully()
    {
        $data_array = ['role' => 'role name', 'description' => "Description goes here"];

        $designation = Designation::create($data_array);

        $response = $this->patchJson(route('designations.update', $designation->id), [
            'role' => 'role updated',
            'description' => "updated description",
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('designations', [
            'id' => $designation->id,
            'role' => 'role updated',
            'description' => "updated description",
        ]);
    }

    /** @test */
    public function testUpdateDesignationValidationErrors()
    {
        $data_array = ['role' => 'role name', 'description' => "Description goes here"];
        $designation = Designation::create($data_array);
        $response = $this->patchJson(route('designations.update', $designation->id), [
            'role' => '',
        ]);
        $response->assertStatus(500);
    }
}
