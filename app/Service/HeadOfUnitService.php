<?php

namespace App\Service;

use App\Models\Unit;
use App\Models\HeadOfUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;



class HeadOfUnitService
{

    /**
     * Create a new head of unit .
     *
     * @param \Illuminate\Http\Request $request The HTTP request object .
     *
     * The $request parameter should contain the following keys:
     *   - 'user_id' (int, required): the user id.
     *   - 'unit_id' (int, required): The unit id.
     *   - 'location_id' (int, required): The location id.
     *   - 'status' (int): The status if the user is head 1 and 0 for inactive.
     *
     * @return \App\Models\HeadOfUnit|\Illuminate\Support\MessageBag Returns the created HeadOfUnit object if successful,
     * otherwise a validation error message bag.
     */
    public function create(Request $request): HeadOfUnit | \Illuminate\Support\MessageBag | bool
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'unit_id' => 'required',
            'location_id' => 'required',
            'status' => 'integer',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        if ($createHeadOfUnit = HeadOfUnit::create($request->all())) {
            return $createHeadOfUnit;
        }

        return false;
    }


    /**
     * Get a head of unit  by ID.
     *
     * @param int $houId The ID of the Head Of Unit.
     *
     * @return \App\Models\HeadOfUnit|null Returns the Head Of Unit or null if not found.
     */

    public function getHeadOfUnitById($houId): HeadOfUnit | null |bool
    {

        try {
            return HeadOfUnit::findOrFail($houId);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get a head of unit  by unit id and location id.
     *
     * @param int $unitId The ID of unit.
     * @param int $locationId The ID of the location.
     *
     * @return \App\Models\HeadOfUnit|null Returns the Head Of Unit or null if not found.
     */

    public function getHeadOfUnitByUnitAndLocaltion(int $unitId, int $locationId): HeadOfUnit | null
    {
        return HeadOfUnit::where(["unit_id" => $unitId, "location_id" => $locationId])->first();
    }



    /**
     * View all Head Of Unit.
     *
     * @return \Illuminate\Support\Collection Returns a collection of all units.
     */
    public function viewAllHeadOfUnits(): Collection
    {
        return HeadOfUnit::all();
    }
}
