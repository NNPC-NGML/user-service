<?php

namespace App\Service;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return false;
        }

        $user = new User($request->all());

        try {
            DB::beginTransaction();

            $user = new User($request->all());
            $user->save();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
