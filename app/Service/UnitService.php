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
     * Get all units in a department.
     *
     * @param int $departmentId The ID of the department.
     *
     * @return \Illuminate\Database\Eloquent\Collection Returns a collection of units in the department.
     */
    public function getUnitsInDepartment(int $departmentId)
    {
        return Unit::where('department_id', $departmentId)->get();
    }
}
