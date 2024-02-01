<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
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
}
