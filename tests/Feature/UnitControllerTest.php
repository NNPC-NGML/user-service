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

        $response = $this->actingAsTestUser()->post(route('create_unit'), $data);

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

        $response = $this->actingAsTestUser()->post(route('create_unit'), $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'description' => ['The description field is required.'],
                ]
            ]);
    }

    /** @test */
    public function it_returns_units()
    {

        $users = Unit::factory()->count(10)->create();

        $response = $this->actingAsTestUser()->getJson(route('units.index'));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'department_id',
                    'created_at',
                    'updated_at'
                ],
            ],
        ]);

        $response->assertJsonCount(count($users), 'data');
        $response->assertJsonPath('data.0.id', $users[0]->id);
        $response->assertJsonPath('data.0.name', $users[0]->name);
    }

    /** @test */
    public function it_returns_no_units()
    {

        $response = $this->actingAsTestUser()->getJson(route('units.index'));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'department_id',
                    'created_at',
                    'updated_at'
                ],
            ],
        ]);

        $response->assertJsonCount(0, 'data');
    }
    /** @test */
    public function it_can_delete_an_existing_unit()
    {

        $department = department::factory()->create();

        $unit = Unit::create([
            'name' => 'Test Unit',
            'description' => 'Test Description',
            'department_id' => $department->id,
        ]);

        $response = $this->actingAsTestUser()->deleteJson(route('delete_unit', ['id' => $unit->id]));


        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Unit successfully deleted']);

        $this->assertDatabaseMissing('units', ['id' => $unit->id]);
    }

    /** @test */
    public function it_returns_false_if_unit_does_not_exist()
    {

        $nonExistentUnit = mt_rand(1000000000, 9999999999);

        $response = $this->actingAsTestUser()->deleteJson(route('delete_unit', ['id' => $nonExistentUnit]));

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Unit not found'
            ]);
    }

    /** @test */
    public function test_show_unit_exists()
    {

        $department = department::factory()->create();

        $unit = Unit::create([
            'name' => 'Test Unit',
            'description' => 'Test Description',
            'department_id' => $department->id,
        ]);

        $response = $this->actingAsTestUser()->getJson(route('units.show', ['id' => $unit->id]));

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'data' => [
                'name' => $unit->name,
                'description' => $unit->description,
                'department_id' => $unit->department_id,
            ]
        ]);
    }

    /** @test */
    public function test_show_unit_not_found()
    {
        $nonExistingUnitId = mt_rand(1000000000, 9999999999);


        $response = $this->actingAsTestUser()->getJson(route('units.show', ['id' => $nonExistingUnitId]));

        $response->assertStatus(404);

        $response->assertJson([
            'error' => 'Unit not found'
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

        $response = $this->actingAsTestUser()->get(route('show_units_in_department', ['departmentId' => $department->id]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('units', [
            'name' => $unit->name,
            'description' => $unit->description,
            'department_id' => $department->id,
        ]);
    }

    /** @test */
    public function get_units_in_invalid_department_rtn_no_data(): void
    {

        $nonExistentDepartmentId = mt_rand(1000000000, 9999999999);

        $response = $this->actingAsTestUser()->get(route('show_units_in_department', ['departmentId' => $nonExistentDepartmentId]));

        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'success',
                'data' => [],
            ]
        );

        $response->assertJsonCount(0, 'data');
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

        $response = $this->actingAsTestUser()->putJson(route('units.update', ['id' => $unit1->id]), $updatedUnit);

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
    public function invalid_unit_returns_no_record()
    {

        $nonExistentUnitId = mt_rand(1000000000, 9999999999);

        $response = $this->actingAsTestUser()->putJson(route('units.update', ['id' => $nonExistentUnitId]), []);

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

        $response = $this->actingAsTestUser()->putJson(route('units.update', ['id' => $unit->id]), $updatedUnit);

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