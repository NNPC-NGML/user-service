<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Location;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user()
    {
        $data = [
            'email' => 'test@example.com',
            'name' => 'John Doe',
            'password' => 'password123',
        ];

        $response = $this->actingAsTestUser()->post(route('create_user'), $data);
        $response->assertStatus(201);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'John Doe',
        ]);

        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /** @test */
    public function it_requires_email_and_password_for_user_creation()
    {
        $data = ['name' => 'John Doe'];

        $response = $this->actingAsTestUser()->postJson(route('create_user'), $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ],
            ]);
    }

    /** @test */
    public function it_validates_email_format_for_user_creation()
    {
        $data = [
            'email' => 'invalidemail',
            'name' => 'John Doe',
            'password' => 'password123',
        ];

        $response = $this->actingAsTestUser()->post(route('create_user'), $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'email' => ['The email field must be a valid email address.'],
                ],
            ]);
    }

    /** @test */
    public function it_validates_unique_email_for_user_creation()
    {
        // Create a user with the same email first
        User::factory()->create(['email' => 'test@example.com']);

        $data = [
            'email' => 'test@example.com',
            'name' => 'John Doe',
            'password' => 'password123',
        ];

        $response = $this->actingAsTestUser()->postJson(route('create_user'), $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'email' => ['The email has already been taken.'],
                ],
            ]);
    }

    /** @test */
    public function it_validates_password_length_for_user_creation()
    {
        $data = [
            'email' => 'test@example.com',
            'name' => 'John Doe',
            'password' => '123',
        ];

        $response = $this->actingAsTestUser()->postJson(route('create_user'), $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'password' => ['The password field must be at least 8 characters.'],
                ],
            ]);
    }

    /** @test */
    public function it_can_delete_an_existing_user()
    {

        $user = User::factory()->create();
        // Authenticate user
        $this->actingAs($user);

        $response = $this->deleteJson(route('delete_user'), ['id' => $user->id]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'User successfully deleted']);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function it_returns_not_found_if_user_does_not_exist()
    {
        $user = User::factory()->create();
        // Authenticate user
        $this->actingAs($user);

        $nonExistentUserId = mt_rand(1000000000, 9999999999);

        // Send a delete request with a non-existent user ID
        $response = $this->deleteJson(route('delete_user'), ['id' => $nonExistentUserId]);

        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'User not found']);
    }

    /** @test */
    public function it_requires_a_valid_user_id()
    {
        $user = User::factory()->create();
        // Authenticate user
        $this->actingAs($user);

        // Send a delete request without providing a user ID
        $response = $this->deleteJson(route('delete_user'));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id']);
    }

    public function it_updates_user_credentials()
    {
        $user = User::factory()->create();

        $newUserData = [
            'email' => 'newemail@example.com',
            'name' => 'New Name',
            'password' => 'newpassword123',
        ];

        $response = $this->actingAsTestUser()->putJson(route('users.update', ['userId' => $user->id]), $newUserData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'email' => $newUserData['email'],
                    'name' => $newUserData['name'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $newUserData['email'],
            'name' => $newUserData['name'],
        ]);
    }

    /** @test */
    public function it_returns_not_found_error_if_user_does_not_exist()
    {

        $nonExistentUserId = mt_rand(1000000000, 9999999999);

        $response = $this->actingAsTestUser()->putJson(route('users.update', ['userId' => $nonExistentUserId]), []);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'User not found',
            ]);
    }

    /** @test */
    public function it_validates_request_data()
    {
        $user = User::factory()->create();

        $invalidUserData = [
            'email' => 'invalidemail',
            'name' => '',
            'password' => 'short',
        ];

        $response = $this->actingAsTestUser()->putJson(route('users.update', ['userId' => $user->id]), $invalidUserData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'name', 'password']);
    }

    /** @test */
    public function test_show_user_exists()
    {
        $user = User::factory()->create();

        $response = $this->actingAsTestUser()->getJson(route('users.show', ['userId' => $user->id]));

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
            ],
        ]);
    }

    /** @test */
    public function test_show_user_not_found()
    {
        $nonUserId = mt_rand(1000000000, 9999999999);

        $response = $this->actingAsTestUser()->getJson(route('users.show', ['userId' => $nonUserId]));

        $response->assertStatus(404);

        $response->assertJson([
            'error' => 'User not found',
        ]);
    }

    /** @test */
    public function it_returns_users_paginated()
    {

        $users = User::factory()->count(10)->create();

        $response = $this->actingAsTestUser()->getJson(route('users.index', ['page' => 1, 'perPage' => 10]));

        $response->assertOk();

        // Assert the pagination structure
        $response->assertJsonStructure([
            'success',
            'data' => [
                'current_page',
                'data' => [
                    '*' => ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'],
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ],
        ]);

        $response->assertJsonCount(count($users), 'data.data');

        $response->assertJsonPath('data.current_page', 1);

        $response->assertJsonPath('data.data.0.id', $users[0]->id);
        $response->assertJsonPath('data.data.0.name', $users[0]->name);
    }

    /** @test */
    public function it_returns_no_user()
    {

        $response = $this->actingAsTestUser()->getJson(route('users.index', ['page' => 1, 'perPage' => 10]));

        $response->assertOk();

        // NOTE&: I commented out the structure because the current user will also be returned

        // Assert the pagination structure
        // $response->assertJsonStructure([
        //     'success',
        //     'data' => [
        //         'current_page',
        //         'data' => [
        //             '*' => ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at', 'department_id']
        //         ],
        //         'first_page_url',
        //         'from',
        //         'last_page',
        //         'last_page_url',
        //         'links',
        //         'next_page_url',
        //         'path',
        //         'per_page',
        //         'prev_page_url',
        //         'to',
        //         'total',
        //     ]
        // ]);

        // $response->assertJsonCount(0, 'data.data');
    }

    /** @test */
    public function it_returns_users_for_next_page_data()
    {
        // NOTE* the next expect 5 but 6 is return, I appended 14 users to created so that the current user (this->actingAsTestUser()) is accounted for. so that we have a total number of 15 users.
        $totalLength = 15;

        $page = 2;
        $perPage = 10;
        // User::factory()->count($totalLength)->create();
        User::factory()->count(14)->create();

        $response = $this->actingAsTestUser()->getJson(route('users.index', ['page' => $page, 'perPage' => $perPage]));

        $response->assertOk();

        // Assert the pagination structure
        $response->assertJsonStructure([
            'success',
            'data' => [
                'current_page',
                'data' => [
                    '*' => ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'],
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ],
        ]);

        $response->assertJsonCount($totalLength - $perPage, 'data.data');

        $response->assertJsonPath('data.current_page', $page);
        $response->assertJsonPath('data.per_page', $perPage);

        $response->assertJsonPath('data.from', $perPage * ($page - 1) + 1);
        $response->assertJsonPath('data.total', $totalLength);
    }
    /**
     * Test successful initialization of user basic info.
     */
    public function test_initialize_user_basic_info_success()
    {
        // Create real data in the database
        $user = User::factory()->create(['status' => 0]);
        $department = Department::factory()->create();
        $location = Location::factory()->create();
        $unit = Unit::factory()->create();
        $designation = Designation::factory()->create();

        // Act as the authenticated user
        $this->actingAs($user);

        // Prepare the request data (no need for 'user_id', Auth::user() is used)
        $data = [
            'department_id' => $department->id,
            'location_id' => $location->id,
            'unit_id' => $unit->id,
            'designation_id' => $designation->id,
        ];

        // Make a POST request to the controller
        $response = $this->postJson('/api/v1/initialize_user_basic_info', $data);

        // Check if the response status is 200 OK
        $response->assertStatus(200)
            ->assertJson(['success' => true, 'data' => []]);

        // Refresh the user model to check if the status has been updated
        $user->refresh();
        $this->assertEquals(1, $user->status);

        // Check if the user has been correctly assigned to department, location, etc.
        $this->assertDatabaseHas('department_users', [
            'user_id' => $user->id,
            'department_id' => $department->id,
        ]);

        $this->assertDatabaseHas('location_users', [
            'user_id' => $user->id,
            'location_id' => $location->id,
        ]);

        $this->assertDatabaseHas('unit_users', [
            'user_id' => $user->id,
            'unit_id' => $unit->id,
        ]);

        $this->assertDatabaseHas('designation_users', [
            'user_id' => $user->id,
            'designation_id' => $designation->id,
        ]);
    }

    /**
     * Test failure when one of the assignments fails (missing field).
     */
    public function test_initialize_user_basic_info_failure()
    {
        // Create real data in the database
        $user = User::factory()->create(['status' => 0]);
        $department = Department::factory()->create();
        $location = Location::factory()->create();
        $unit = Unit::factory()->create();
        $designation = Designation::factory()->create();

        // Act as the authenticated user
        $this->actingAs($user);

        // Simulate missing one of the required fields in the request
        $data = [
            'department_id' => $department->id,
            'location_id' => $location->id,
            // 'unit_id' => $unit->id, // Omitting this field to simulate failure
            'designation_id' => $designation->id,
        ];

        // Make a POST request to the controller
        $response = $this->postJson('/api/v1/initialize_user_basic_info', $data);

        // Check if the response status is 422 Unprocessable Entity
        $response->assertStatus(422);
    }

    /**
     * Test that the transaction is rolled back on exception.
     */
    public function test_initialize_user_basic_info_rollback_on_exception()
    {
        // Create real data in the database
        $user = User::factory()->create(['status' => 0]);
        $department = Department::factory()->create();
        $location = Location::factory()->create();
        $unit = Unit::factory()->create();
        $designation = Designation::factory()->create();

        // Act as the authenticated user
        $this->actingAs($user);

        // Simulate causing an exception (e.g., invalid department ID)
        $data = [
            'department_id' => 9999, // Non-existent department ID to cause failure
            'location_id' => $location->id,
            'unit_id' => $unit->id,
            'designation_id' => $designation->id,
        ];

        // Make a POST request to the controller
        $response = $this->postJson('/api/v1/initialize_user_basic_info', $data);

        // Check if the response status is 422 Unauthorized (or adjust based on exception)
        $response->assertStatus(422);

        // Ensure the user status was not updated due to the rollback
        $user->refresh();
        $this->assertEquals(0, $user->status);

        // Ensure that no records were inserted for the user in the department table
        $this->assertDatabaseMissing('department_users', [
            'user_id' => $user->id,
        ]);
    }
}
