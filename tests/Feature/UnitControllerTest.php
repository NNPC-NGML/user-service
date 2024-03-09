<?php

namespace Tests\Feature;

use App\Models\department;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitControllerTest extends TestCase
{
    use RefreshDatabase;
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
    public function it_updates_unit_record()
    {
        $department = department::factory()->create();

        $unit1 = Unit::create([
            'name' => 'Unit 2',
            'description' => 'Description 2',
            'department_id' => $department->id,
        ]);

        $updatedUnit = [
            'name' => 'Unit 1',
            'description' => 'Description 1',
            'department_id' => $department->id,
        ];

        $response = $this->putJson(route('units.update', ['id' => $unit1->id]), $updatedUnit);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $updatedUnit['name'],
                    'description' => $updatedUnit['description'],
                ]
            ]);

        $this->assertDatabaseHas('units', [
            'id' => $unit1->id,
            'description' => $updatedUnit['description'],
            'name' => $updatedUnit['name'],
        ]);
    }

    /** @test */
    public function it_returns_unit_record_not_found()
    {

        $nonExistentUnitId = mt_rand(1000000000, 9999999999);

        $response = $this->putJson(route('units.update', ['id' => $nonExistentUnitId]), []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => false
            ]);
    }

    /** @test */
    public function it_validates_unit_update_request_data()
    {
        $department = department::factory()->create();

        $unit = Unit::create([
            'name' => 'Unit 2',
            'description' => 'Description 2',
            'department_id' => $department->id,
        ]);

        $updatedUnit = [
            'name' => '',
            'description' => '',
            'department_id' => $department->id,
        ];

        $response = $this->putJson(route('units.update', ['id' => $unit->id]), $updatedUnit);

        $response->assertStatus(422);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'description' => ['The description field is required.'],
                    'name' => ['The name field is required.']
                ]
            ]);
    }
}
