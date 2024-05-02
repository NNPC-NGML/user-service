<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\department;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DepartmentControllerTest extends TestCase
{
    use RefreshDatabase;


    public function test_route_to_view_all_departments()
    {
        department::factory(10)->create();
        $this->actingAsTestUser()->get('/api/v1/department')
            ->assertStatus(201);
    }

    public function test_to_create_a_new_department()
    {

        $data = [
            'name' => 'New Department',
            'description' => 'This is a new department',
            'status' => 1, // Active status
        ];

        $response = $this->actingAsTestUser()->postJson(route('create_department'), $data);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                ],
            ]);
    }

    public function test_to_show_an_existing_department()
    {
        $department = department::factory()->create();

        $response = $this->actingAsTestUser()->getJson(route('view_department', $department));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $department->id,
                    'name' => $department->name,
                    'description' => $department->description,
                ],
            ]);
    }

    public function test_to_update_an_existing_department_successfully()
    {
        $department = department::factory()->create();

        $data = [
            'name' => 'Updated Department',
            'description' => 'This is an updated department',
            'status' => 0,
        ];

        $response = $this->actingAsTestUser()->putJson(route('update_department', $department->id), $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $department->id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                ],
            ]);
    }

    public function test_to_delete_a_department_successfully()
    {
        $department = department::factory()->create();

        $response = $this->actingAsTestUser()->deleteJson(route('delete_department', $department->id));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('departments', [
            'id' => $department->id,
        ]);
    }
    public function test_unauthenticated_cannot_delete_a_department()
    {
        $department = department::factory()->create();

        $response = $this->deleteJson(route('delete_department', $department->id));

        $response->assertStatus(401);

        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
        ]);
    }
}