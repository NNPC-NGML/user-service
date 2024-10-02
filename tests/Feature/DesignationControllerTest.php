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

        $response = $this->actingAsTestUser()->postJson(route('designations.create', $data));

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

        $response = $this->actingAsTestUser()->postJson(route('designations.create', $data));

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

        $response = $this->actingAsTestUser()->patchJson(route('designations.update', $designation->id), [
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
        $response = $this->actingAsTestUser()->patchJson(route('designations.update', $designation->id), [
            'role' => '',
        ]);
        $response->assertStatus(500);
    }


    // designations get all, signle, delete

    public function test_authenticated_returns_designations()
    {

        $data = Designation::factory()->count(10)->create();

        $response = $this->actingAsTestUser()->getJson(route('designations.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'role',
                        'description',
                        'level',
                        'status',
                        'created_at',
                        'updated_at'
                    ],
                ],
            ]);
    }

    /** @test */
    public function test_to_return_no_designation()
    {

        $response = $this->actingAsTestUser()->getJson(route('designations.index'));

        $response->assertStatus(200);
    }
    /** @test */
    public function test_it_can_delete_an_existing_designation()
    {

        $data = Designation::factory()->create();



        $response = $this->actingAsTestUser()->deleteJson(route('designations.destroy', ['id' => $data->id]));


        $response->assertStatus(204);
        $this->assertDatabaseMissing('designations', ['id' => $data->id]);
    }

    /** @test */
    public function test_to_returns_false_if_unit_does_not_exist()
    {

        $nonExistentUnit = mt_rand(1000000000, 9999999999);

        $response = $this->actingAsTestUser()->deleteJson(route('designations.destroy', ['id' => $nonExistentUnit]));

        $response->assertStatus(404);
    }



    /** @test */
    public function test_show_a_single_designation_exists()
    {

        $designation = Designation::factory()->create();

        $response = $this->actingAsTestUser()->getJson(route('designations.show', ['id' => $designation->id]));

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $designation->id,
                'role' => $designation->role,
                'description' => $designation->description,
                'level' => $designation->level,
                // 'status' => $designation->status,
            ]
        ]);
    }

    /** @test */
    public function test_to_show_designation_not_found()
    {
        $nonExistingUnitId = mt_rand(1000000000, 9999999999);


        $response = $this->actingAsTestUser()->getJson(route('designations.show', ['id' => $nonExistingUnitId]));

        $response->assertStatus(404);
    }
}
