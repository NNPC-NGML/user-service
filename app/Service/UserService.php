<?php

namespace App\Service;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

/**
 * Class UserService
 *
 * Service class responsible for user-related operations.
 *
 * @package App\Service
 */

class UserService
{

    /**
     * Create a new user.
     *
     * This method validates the provided user data and creates a new user in the database.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object containing user data.
     *
     * The $request parameter should contain the following keys:
     *   - 'email' (string, required): The email address of the new user.
     *   - 'name' (string, required): The name of the new user.
     *   - 'password' (string, required): The password for the new user. Should be at least 8 characters long.
     *
     * @throws \Illuminate\Validation\ValidationException Thrown if validation fails.
     *
     * @return \App\Models\User|string Returns the created user object if successful, otherwise a string with the error message.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }

        $user = new User($request->all());
        $user->save();

        return $user;
    }
    public function testUpdateUserCredentialsSuccess(): void
    {
        // Create a user for testing
        $user = User::factory()->create();

        $userData = [
            'name' => 'New Name',
            'password' => 'newpassword123',
        ];

        $userService = new UserService();
        $result = $userService->updateUserCredentials($user->id, $userData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($userData['name'], $result->name);
        $this->assertTrue(password_verify($userData['password'], $result->password));
    }

    public function testUpdateUserCredentialsFailure(): void
    {
        // Attempt to update a non-existent user
        $userId = mt_rand(1000000000, 9999999999);
        $userData = [
            'name' => 'New Name',
            'password' => 'newpassword123',
        ];

        $userService = new UserService();
        $result = $userService->updateUserCredentials($userId, $userData);

        $this->assertFalse($result);

        
        // Attempt to update with invalid data (e.g., short password)
        $user = User::factory()->create();
        $invalidData = [
            'password' => 'short',
        ];

        $result = $userService->updateUserCredentials($user->id, $invalidData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('password', $result);
        $this->assertEquals(['The password must be at least 8 characters.'], $result['password']);
    }
}
