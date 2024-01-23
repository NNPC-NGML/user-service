<?php

namespace App\Service;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;

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
     * @return bool Returns true if the user is successfully created, otherwise false.
     */
    public function create(Request $request): bool
    {

        try {
            $request->validate([
                'email' => 'required|email|unique:users',
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:8',
            ]);
        } catch (ValidationException $e) {
            return false;
        }

        $user = new User($request->all());

        if ($user->save()) {
            return true;
        } else {
            return false;
        };
    }
}
