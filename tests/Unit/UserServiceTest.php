<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Service\UserService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
}
