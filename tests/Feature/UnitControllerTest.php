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
}
