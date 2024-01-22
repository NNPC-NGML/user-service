<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Service\UserService\UserHandler;

class UserControllerTest extends TestCase
{
    private $createdUserId;

    /**
     * A basic unit test example.
     */
    public function test_if_user_is_created(): void
    {
        $userHandler = new UserHandler();
        $data_array = [
            'email' => 'test@example.com',
            'name' => 'John Doe',
            'password' => 'password123',
        ];


        $this->createdUserId = $userHandler->create(...array_values($data_array));

        $this->assertTrue($this->createdUserId !== false);
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
