<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\department;
use App\Models\Unit;
use App\Service\UserService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function testIfUserIsCreated(): void
    {
        $userService = new UserService();
        $dataArray = [
            'email' => 'test12222@example.com',
            'name' => 'John Doe',
            'password' => 'password123',
        ];

        $data = new Request($dataArray);
        $userCreatedUser = $userService->create($data);
        $this->assertInstanceOf(\App\Models\User::class, $userCreatedUser);

        // Check if the user record exists in the database
        $this->assertDatabaseHas('users', [
            'email' => 'test12222@example.com',
            'name' => 'John Doe',
        ]);
    }

    public function testIfUserIsNotCreated(): void
    {
        $userService = new UserService();
        $data_array = [
            'email' => 'test01111@example.com',
            'name' => 'John Doe',
            'password' => 'pass',
        ];

        $data = new Request($data_array);
        $userNotCreated = $userService->create($data);

        $this->assertIsArray($userNotCreated);
        $this->assertArrayHasKey('password', $userNotCreated);
        $this->assertEquals(['The password field must be at least 8 characters.'], $userNotCreated['password']);

        // Assert the user record does not exist in the database
        $this->assertDatabaseMissing('users', [
            'email' => 'test01111@example.com',
            'name' => 'John Doe',
        ]);
    }

    /**
     * Test updating user credentials successfully.
     */
    public function testUpdateUserCredentialsSuccess(): void
    {
        // Create a user for testing
        $user = User::factory()->create();

        $newUserData = [
            'email' => 'newemail@example.com',
            'name' => 'New Name',
            'password' => 'newpassword123',
        ];

        $userService = new UserService();
        $updateSuccessful = $userService->updateUserCredentials($user->id, $newUserData);

        $this->assertInstanceOf(user::class, $updateSuccessful);
        //$this->assertTrue($updateSuccessful);

        // Check if the user record is updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'newemail@example.com',
            'name' => 'New Name',
        ]);
    }

    /**
     * Test updating user credentials unsuccessfully.
     */
    public function testUpdateUserCredentialsFailure(): void
    {
        // Attempt to update a non-existent user
        $nonExistentUserId = mt_rand(1000000000, 9999999999);
        $data = [
            'email' => 'newemail@example.com',
            'name' => 'New Name',
            'password' => 'newpassword123',
        ];

        $userService = new UserService();
        $updateFailed = $userService->updateUserCredentials($nonExistentUserId, $data);

        $this->assertFalse($updateFailed);


        // Attempt to update user with invalid data (e.g., invalid email)
        $user = User::factory()->create();
        $invalidUserData = [
            'email' => 'invalidemail',
            'name' => 'New Name',
            'password' => 'newpassword123',
        ];

        $userService = new UserService();
        $updateFailed = $userService->updateUserCredentials($user->id, $invalidUserData);

        $this->assertIsArray($updateFailed);
        $this->assertArrayHasKey('email', $updateFailed);
        $this->assertEquals(['The email field must be a valid email address.'], $updateFailed['email']);

        // Check if the user record is not updated in the database
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'email' => 'invalidemail',
            'name' => 'New Name',
        ]);


        // Attempt to update with invalid data (e.g., short password)
        $testUser = User::factory()->create();
        $invalidData = [
            'password' => 'short',
        ];

        $result = $userService->updateUserCredentials($testUser->id, $invalidData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('password', $result);
        $this->assertEquals(['The password field must be at least 8 characters.'], $result['password']);
    }

    public function testDeleteUserSuccessfully(): void
    {
        // Create a user for testing
        $user = User::factory()->create();

        $userService = new UserService();
        $deleted = $userService->deleteUser($user->id);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function testDeleteNonexistentUser(): void
    {
        $nonExistentUserId = mt_rand(1000000000, 9999999999);

        $userService = new UserService();
        $deleted = $userService->deleteUser($nonExistentUserId);

        $this->assertFalse($deleted);

        $this->assertDatabaseMissing('users', ['id' => $nonExistentUserId]);
    }
    public function testGetUsersForPageWithData(): void
    {
        // Seed the database with 15 users
        User::factory(15)->create();

        $userService = new UserService();
        $page = 2;
        $perPage = 10;

        $users = $userService->getUsersForPage($page, $perPage);

        // Assert that the returned value is a LengthAwarePaginator
        $this->assertInstanceOf(LengthAwarePaginator::class, $users);

        // Assert that the paginator contains the expected number of items
        $this->assertCount(5, $users->items());

        // Assert that the current page and per page values are as expected
        $this->assertEquals(2, $users->currentPage());
        $this->assertEquals(10, $users->perPage());
    }

    public function testGetUsersForPageWithoutData(): void
    {
        $userService = new UserService();
        $page = 1;
        $perPage = 10;

        $users = $userService->getUsersForPage($page, $perPage);

        // Assert that the returned value is a LengthAwarePaginator
        $this->assertInstanceOf(LengthAwarePaginator::class, $users);

        // Assert that the paginator contains no items
        $this->assertCount(0, $users->items());
    }

    /**
     * Test assigning a user to a department for the first time.
     */
    public function testAssignUserToDepartmentFirstTime(): void
    {
        // Create a user and department for testing
        $user = User::factory()->create();
        $department = Department::factory()->create();

        $userService = new UserService();
        $assignmentSuccess = $userService->assignUserToDepartment($user->id, $department->id);

        $this->assertTrue($assignmentSuccess);

        // Check if the user is assigned to the correct department
        $this->assertEquals($department->id, $user->fresh()->department->id);
    }

    /**
     * Test assigning a user to an existing department.
     */
    public function testAssignUserToExistingDepartment(): void
    {
        // Create a user and two departments for testing
        $user = User::factory()->create();
        $department1 = Department::factory()->create();
        $department2 = Department::factory()->create();

        $user->department()->associate($department1->id)->save();


        $userService = new UserService();
        $assignmentSuccess = $userService->assignUserToDepartment($user->id, $department2->id);

        $this->assertTrue($assignmentSuccess);

        $this->assertEquals($department2->id, $user->fresh()->department->id);
    }

    /**
     * Test assigning a user to a department with invalid user ID.
     */
    public function testAssignUserToDepartmentWithInvalidUserId(): void
    {
        // Create a department for testing
        $department = Department::factory()->create();

        $nonExistentUserId = mt_rand(1000000000, 9999999999);
        $userService = new UserService();
        $assignmentFailed = $userService->assignUserToDepartment($nonExistentUserId, $department->id);

        $this->assertFalse($assignmentFailed);

        // Ensure that the user and department are not associated in the database
        $user = User::find($nonExistentUserId);
        $this->assertNull($user);
    }

    /**
     * Test assigning a user to a department with invalid department ID.
     */
    public function testAssignUserToDepartmentWithInvalidDepartmentId(): void
    {
        // Create a user for testing
        $user = User::factory()->create();

        $userService = new UserService();

        $nonExistentDepartmentId = mt_rand(1000000000, 9999999999);
        $assignmentFailed = $userService->assignUserToDepartment($user->id, $nonExistentDepartmentId);

        $this->assertFalse($assignmentFailed);

        // Ensure that the user's department remains unchanged in the database
        $this->assertNull(User::find($user->id)->department);
    }
    
    /**
     * Test assigning a user to a unit.
     */
    public function testAssignUserToUnit(): void
    {
        $department = Department::create([
            'name' => 'Test Department',
            'description' => 'Test Description',
        ]);

        $unit = Unit::create([
            'name' => 'Test Unit',
            'description' => 'Unit Description',
            'department_id' => $department->id,
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $user->department()->associate($department->id);

        $user->save();

        $userService = new UserService();
        $assigned = $userService->assignUserToUnit($user->id, $unit->id);

        $this->assertTrue($assigned);

        // Assert that the user now belongs to the unit
        $this->assertTrue($user->units->contains($unit));
    }

    /**
     * Test assigning a user to a non-existent unit.
     */
    public function testAssignUserToNonExistentUnit(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $nonExistentUnitId = mt_rand(1000000000, 9999999999);
        $userService = new UserService();
        $assigned = $userService->assignUserToUnit($user->id, $nonExistentUnitId);

        $this->assertFalse($assigned);
    }
}
