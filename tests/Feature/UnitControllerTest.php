<?php

namespace Tests\Feature;

use App\Models\department;
use App\Models\Unit;
use Tests\TestCase;

class UnitControllerTest extends TestCase
{
    /** @test */
    public function it_can_create_a_unit(): void
    {
        // Create a department for testing
        $department = department::factory()->create();

        $data = [
            'name' => 'Test Unit',
            'description' => 'Description of the test unit.',
            'departmentId' => $department->id,
        ];

        $response = $this->post(route('create_unit'), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('units', [
            'name' => 'Test Unit',
            'description' => 'Description of the test unit.',
        ]);
    }

    /** @test */
    public function it_cannot_create_a_unit(): void
    {
        // Create a department for testing
        $department = department::factory()->create();

        $data = [
            'name' => 'Test Unit',
            'departmentId' => $department->id,
        ];

        $response = $this->post(route('create_unit'), $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'description' => ['The description field is required.'],
                ]
            ]);
    }

    /** @test */
    public function it_can_get_units_in_department(): void
    {
        $department = department::factory()->create();

        $unit = Unit::create([
            'name' => 'Unit 1',
            'description' => 'Description 1',
            'department_id' => $department->id,
        ]);

        Unit::create([
            'name' => 'Unit 2',
            'description' => 'Description 2',
            'department_id' => $department->id,
        ]);

        $response = $this->get(route('show_units_in_department', ['departmentId' => $department->id]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('units', [
            'name' => $unit->name,
            'description' => $unit->description,
            'department_id' => $department->id,
        ]);
    }

    /** @test */
    public function get_units_in_department_rtn_no_data(): void
    {

        $nonExistentDepartmentId = mt_rand(1000000000, 9999999999);

        $response = $this->get(route('show_units_in_department', ['departmentId' => $nonExistentDepartmentId]));


        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'success',
                'data' => [],
            ]
        );

        $response->assertJsonCount(0, 'data');
    }
}
