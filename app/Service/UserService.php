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

    /**
     * Get all data of a particular user with all relationships.
     *
     * @param int $userId The ID of the user.
     *
     * @return \App\Models\User|null Returns the user with all relationships or null if the user is not found.
     */
    public function getUserWithAllRelationships(int $userId)
    {
        // Load the user with all relationships
        $user = User::with($this->getAllRelationships(User::class))->find($userId);

        return $user;
    }

    /**
     * Get all relationships for a given model.
     *
     * @param string $modelClass The fully qualified class name of the model.
     *
     * @return array An array containing all relationships for the model.
     */
    protected function getAllRelationships(string $modelClass)
    {
        $modelInstance = new $modelClass;
        $relationships = $modelInstance->getRelations();

        return $relationships;
    }
}
