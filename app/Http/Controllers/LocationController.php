<?php

namespace App\Http\Controllers;

use App\Service\LocationService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * @OA\Delete(
     *     path="/locations/{id}",
     *     summary="Deletes a specific location",
     *     operationId="deleteLocation",
     *     tags={"Locations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the location to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Location deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Location not found")
     *         )
     *     )
     * )
     */
    public function delete(int $id)
    {
        $result = $this->locationService->deleteLocation($id);

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Location deleted successfully'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Location not found'], 404);
        }
    }
    public function show($locationId)
    {

        $location = $this->locationService->getLocation($locationId);

        if (!$location) {
            return response()->json(['error' => 'Location not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $location], 200);
    }
}
