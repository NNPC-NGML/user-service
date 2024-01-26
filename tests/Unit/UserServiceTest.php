<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use App\Service\UserService;
use Illuminate\Http\Request;

class UserServiceTest extends TestCase
{
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
     * Test if the getUser method returns a user.
     */
    public function testGetUser(): void
    {
        // Create a user for testing
        $user = User::factory()->create();

        $userService = new UserService();
        $retrievedUser = $userService->getUser($user->id);

        $this->assertInstanceOf(User::class, $retrievedUser);
        $this->assertEquals($user->id, $retrievedUser->id);
    }

    /**
     * Test when user does not exist.
     */
    public function testGetUserWhenIdNotFound(): void
    {
        $userId = mt_rand(1000000000, 9999999999);

        $userService = new UserService();
        $retrievedUser = $userService->getUser($userId);

        $this->assertNull($retrievedUser);
    }
}
