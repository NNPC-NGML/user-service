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
    public function it_returns_units()
    {

        $users = Unit::factory()->count(10)->create();

        $response = $this->getJson(route('units.index'));

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

         $response = $this->getJson(route('units.index'));

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

        $response = $this->deleteJson(route('delete_unit', ['id' => $unit->id]));


        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Unit successfully deleted']);

        $this->assertDatabaseMissing('users', ['id' => $unit->id]);
    }

    /** @test */
    public function it_returns_false_if_unit_does_not_exist()
    {

        $nonExistentUnit = mt_rand(1000000000, 9999999999);

        $response = $this->deleteJson(route('delete_unit', ['id' => $nonExistentUnit]));

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

        $response = $this->getJson(route('units.show', ['id' => $unit->id]));

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


        $response = $this->getJson(route('units.show', ['id' => $nonExistingUnitId]));

        $response->assertStatus(404);

        $response->assertJson([
            'error' => 'Unit not found'
        ]);
    }

}
