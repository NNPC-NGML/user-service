<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Service\UserService;
use Illuminate\Http\Request;

class UserServiceTest extends TestCase
{
    private $createdUserId;

    /**
     * A basic unit test example.
     */
    public function testIfUserIsCreated(): void
    {
        $userService = new UserService();
        $dataArray = [
            'email' => 'test1@example.com',
            'name' => 'John Doe',
            'password' => 'password123',
        ];

        $data = new Request($dataArray);
        $userCreatedUser = $userService->create($data);
        $this->assertTrue($userCreatedUser);

        // Check if the user record exists in the database
        $this->assertDatabaseHas('users', [
            'email' => 'test1@example.com',
            'name' => 'John Doe',
        ]);
    }

    public function testIfUserIsNotCreated(): void
    {
        $userService = new UserService();
        $data_array = [
            'email' => 'test02@example.com',
            'name' => 'John Doe',
            'password' => 'password',
        ];

        $data = new Request($data_array);
        $userCreatedUser = $userService->create($data);

        $this->assertFalse($userNotCreated);

        // Assert the user record does not exist in the database
        $this->assertDatabaseMissing('users', [
            'email' => 'test02@example.com',
            'name' => 'John Doe',
        ]);
    }
}
