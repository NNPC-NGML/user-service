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
    public function test_if_user_is_created(): void
    {
        $userService = new UserService();
        $data_array = [
            'email' => 'test1@example.com',
            'name' => 'John Doe',
            'password' => 'password123',
        ];

        $data = new Request($data_array);
        $userCreatedUser = $userService->create($data);
        $this->assertTrue($userCreatedUser);
    }

    public function test_if_user_is_not_created(): void
    {
        $userService = new UserService();
        $data_array = [
            'email' => 'test1@example.com',
            'name' => 'John Doe',
            'password' => 'password',
        ];

        $data = new Request($data_array);
        $userCreatedUser = $userService->create($data);
        $this->assertTrue(!$userCreatedUser);
    }

    /**
     * Clean up after the test.
     */
    protected function tearDown(): void
    {
        if ($this->createdUserId) {
           //TODO: DELETE CREATED USER;
        }

        parent::tearDown();
    }
}
