<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Service\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function testIfUserIsCreated(): void
    {
        $userService = new UserService();
        $dataArray = [
            'email' => 'test1222@example.com',
            'name' => 'John Doe',
            'password' => 'password123',
        ];

        $data = new Request($dataArray);
        $userCreatedUser = $userService->create($data);
        $this->assertInstanceOf(\App\Models\User::class, $userCreatedUser);

        // Check if the user record exists in the database
        $this->assertDatabaseHas('users', [
            'email' => 'test1222@example.com',
            'name' => 'John Doe',
        ]);
    }

    public function testIfUserIsNotCreated(): void
    {
        $userService = new UserService();
        $data_array = [
            'email' => 'test0111@example.com',
            'name' => 'John Doe',
            'password' => 'pass',
        ];

        $data = new Request($data_array);
        $userNotCreated = $userService->create($data);

        $this->assertFalse($userNotCreated);

        // Assert the user record does not exist in the database
        $this->assertDatabaseMissing('users', [
            'email' => 'test0111@example.com',
            'name' => 'John Doe',
        ]);
    }
}
