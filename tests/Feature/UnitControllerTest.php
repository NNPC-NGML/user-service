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
    public function test_can_get_all_units()
    {
        $units = Unit::factory(1)->create();


        $response = $this->get(route('units.index'));


        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description'
                ]
            ]
        ]);

        $response->assertJsonCount(count($units), 'data');
    }

    public function test_get_all_units_returns_no_data()
    {
        $response = $this->get(route('units.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'data'
        ]);

        $response->assertJson([
            'success' => true,
            'data' => []
        ]);
    }
}
