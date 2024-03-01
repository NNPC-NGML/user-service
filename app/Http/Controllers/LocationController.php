<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
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

    /**
     * @OA\Get(
     *      path="/locations",
     *      operationId="getAllLocations",
     *      tags={"Locations"},
     *      summary="Get all locations",
     *      description="Returns all locations",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean",
     *                  example=true
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  description="Location resource",
     *                  ref="#/components/schemas/LocationResource"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean",
     *                  example=false
     *              ),
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  description="Error message"
     *              )
     *          )
     *      )
     * )
     */
    public function index()
    {
        $locations = $this->locationService->viewAllLocations();

        if ($locations) {
            return response()->json(['success' => true, 'data' => new LocationResource($locations)], 200);
        } else {
            return response()->json(['success' => false, 'error' => $locations], 422);
        }
    }
}
