<?php

namespace App\Service;

use App\Models\Unit;
use App\Models\User;
use App\Models\Location;
use App\Models\UnitUser;
use App\Models\Designation;
use App\Models\LocationUser;
use Illuminate\Http\Request;
use App\Models\DesignationUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $data = $request->only(['email', 'name', 'password']);
        $data['password'] = Hash::make($data['password']);
        $user = new User($data);
        $user->save();

        return $user;
    }

    /**
     * Update user credentials in the database.
     *
     * This method allows updating the user's email, name, and password based on the provided data.
     *
     * @param int $userId The ID of the user whose credentials are to be updated.
     * @param array $userData The array containing the updated user data.
     *
     * The $userData parameter may contain the following keys:
     *   - 'email' (string, optional): The new email address for the user. Should be a valid email format.
     *   - 'name' (string, optional): The new name for the user. Should be a string with a maximum length of 255 characters.
     *   - 'password' (string, optional): The new password for the user. Should be at least 8 characters long.
     *
     * @return bool|array|\App\Models\User Returns true if the update is successful.
     *                   If validation fails, it returns an array of validation errors.
     *                   If the specified user ID is not found or if the new email already exists for another user, it returns false.
     *                   If the update is successful, it returns the updated user object.
     */
    public function updateUserCredentials(int $userId, array $userData): bool|array|User
    {
        // Validate the provided user data
        $validator = Validator::make($userData, [
            'email' => 'sometimes|email|unique:users',
            'name' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:8',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }

        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        $user->update($userData);

        return $user;
    }

    /**
     *      * Delete a user using its id.
     *
     * @param int $userId The id of the user to be deleted.
     *
     * @return bool Return true if user is deleted and false if not deleted
     */

    public function deleteUser(int $userId): bool
    {
        $fetchUser = User::find($userId);
        if (!$fetchUser) {
            return false;
        }

        $fetchUser->delete();
        return true;
    }

    /**     * Get a paginated list of users for a specific page.
     *
     * @param int $page The page number.
     * @param int $perPage The number of users to display per page.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUsersForPage(int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        return User::paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Assign a user to a particular department.
     *
     * @param int $userId       The ID of the user.
     * @param int $departmentId The ID of the department.
     *
     * @return bool Returns true on success, false on failure.
     */
    public function assignUserToDepartmen(int $userId, int $departmentId): bool
    {
        // Validate user and department IDs
        $validator = Validator::make([
            'user_id' => $userId,
            'department_id' => $departmentId,
        ], [
            'user_id' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return false;
        }

        $user = User::find($userId);

        $user->department()->associate($departmentId);

        $user->save();

        return true;
    }

    /**
     * Assign a user to a unit.
     *
     * @param int $userId The ID of the user.
     * @param int $unitId The ID of the unit.
     *
     * @return bool Returns true on success, false on failure.
     */
    public function assignUserToUnit(int $userId, int $unitId): bool
    {
        $user = User::find($userId);
        $unit = Unit::find($unitId);

        if (!$user || !$unit) {
            return false;
        }


        // Check if the user already belongs to the unit
        if ($user->unit && $user->unit->id == $unit->id) {
            return false;
        }

        $unitUser = new UnitUser();
        $unitUser->unit_id = $unit->id;
        $unitUser->user_id = $user->id;
        $unitUser->save();

        return true;
    }


    /**
     * The function assigns a user to a location if they are not already assigned.
     *
     * @param int userId The `userId` parameter is an integer that represents the unique identifier of
     * the user to be assigned to a location.
     * @param int locationId The `locationId` parameter in the `assignUserToLocation` function
     * represents the unique identifier of the location to which you want to assign a user. This
     * parameter is used to retrieve the specific location from the database based on its ID so that
     * the user can be assigned to that location.
     *
     * @return bool The function `assignUserToLocation` returns a boolean value. It returns `true` if
     * the user is successfully assigned to the location, and `false` in the following cases:
     * 1. If the user or location is not found (if `` or `` is null).
     * 2. If the user already belongs to the location (if the user's locations collection contains the
     * specified location
     */
    public function assignUserToLocation(int $userId, int $locationId): bool
    {
        $user = User::find($userId);
        $location = Location::find($locationId);
        if (!$user || !$location) {
            return false;
        }
        if ($user->location && $user->location->id == $location->id) {
            return false;
        }

        $locationUser = new LocationUser();
        $locationUser->location_id = $location->id;
        $locationUser->user_id = $user->id;
        $locationUser->save();

        return true;
    }


    /**
     * The function assigns a user to a designation if they are not already assigned.
     *
     * @param int userId The `userId` parameter is an integer that represents the unique identifier of
     * the user to be assigned to a designation.
     * @param int designationId The `designationId` parameter in the `assignUserToDesignation` function
     * represents the unique identifier of the designation to which you want to assign a user. This
     * parameter is used to retrieve the specific designation from the database based on its ID so that
     * the user can be assigned to that designation.
     *
     * @return bool The function `assignUserToDesignation` returns a boolean value. It returns `true` if
     * the user is successfully assigned to the designation, and `false` in the following cases:
     * 1. If the user or designation is not found (if `` or `` is null).
     * 2. If the user already belongs to the designation (if the user's designations collection contains the
     * specified designation
     */

    public function assignUserToDesignation(int $userId, int $designationId): bool
    {
        $user = User::find($userId);
        $designation = Designation::find($designationId);
        if (!$user || !$designation) {
            return false;
        }
        if ($user->$designation && $user->designation->id == $designation->id) {
            return false;
        }

        $designationUser = new DesignationUser();
        $designationUser->designation_id = $designation->id;
        $designationUser->user_id = $user->id;
        $designationUser->save();

        return true;
    }

    /**
     * Get all data of a particular user.
     *
     * @param int $userId The ID of the user.
     *
     * @return \App\Models\User|null Returns the user or null if the user is not found.
     */
    public function getUser(int $userId)
    {
        return User::find($userId);
    }
}
