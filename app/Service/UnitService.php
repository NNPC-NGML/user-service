<?php

namespace App\Service;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class UnitService
{

    /**
     * Create a new unit with a foreign key referencing the department.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object containing unit data and departmentId.
     *
     * The $request parameter should contain the following keys:
     *   - 'name' (string, required): The name of the new unit.
     *   - 'description' (string, required): The description of the new unit.
     *   - 'departmentId' (int, required): The ID of the department to associate with the unit.
     *
     * @return \App\Models\Unit|\Illuminate\Support\MessageBag Returns the created unit object if successful,
     * otherwise a validation error message bag.
     */
    public function create(Request $request): Unit | \Illuminate\Support\MessageBag | bool
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'required',
            'departmentId' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $unitData = $request->except('departmentId');

        $unit = new Unit($unitData);
        $unit->department_id = $request->input('departmentId');

        if ($unit->save()) {
            return $unit;
        }

        return false;
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

    /**
     * Update a unit.
     *
     * @param int $unitId The ID of the unit to update.
     * @param array $unitData The data to update the unit with.
     *
     * @return \App\Models\Unit|array|bool Returns the updated unit object if successful, otherwise an array of validation errors or false on failure.
     */
    public function updateUnit(int $unitId, array $unitData): Unit|array|bool
    {
        // Validate the provided unit data
        $validator = Validator::make($unitData, [
            'name' => 'sometimes|required|unique:units|max:255',
            'description' => 'sometimes|required',
            'department_id' => 'sometimes|required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }

        $unit = Unit::find($unitId);

        if (!$unit) {
            return false;
        }

        $unit->update($unitData);

        return $unit;
    }
    /**
     * Get a unit by ID.
     *
     * @param int $unitId The ID of the unit.
     *
     * @return \App\Models\Unit|null Returns the unit or null if not found.
     */
    public function getUnit(int $unitId): Unit | null
    {
        return Unit::find($unitId);
    }

    /* *
     * Get all units in a department.
     *
     * @param int $departmentId The ID of the department.
     *
     * @return \Illuminate\Database\Eloquent\Collection Returns a collection of units in the department.
     */
    public function getUnitsInDepartment(int $departmentId): \Illuminate\Database\Eloquent\Collection
    {
        return Unit::where('department_id', $departmentId)->get();
    }
}
