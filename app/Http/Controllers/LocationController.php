<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
use App\Service\LocationService;
use Illuminate\Http\Request;
use App\Models\Location;

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
    /**
     * @OA\Get(
     *     path="/locations/{locationId}",
     *     summary="Get a location by ID",
     *     description="Retrieve information about a location by its ID.",
     *     tags={"Locations"},
     *     @OA\Parameter(
     *         name="locationId",
     *         in="path",
     *         description="ID of the location to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Location"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Location not found"),
     *         ),
     *     ),
     * )
     */
    public function show($locationId)
    {

        $location = $this->locationService->getLocation($locationId);

        if (!$location) {
            return response()->json(['error' => 'Location not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $location], 200);
    }
    /**
     * @OA\Post(
     *     path="/create_locations",
     *     summary="Create a new location",
     *     tags={"Locations"},
     *     @OA\RequestBody(
     *         description="Data for creating a new location",
     *         required=true,
     *         @OA\JsonContent(
     *             required={"location", "zone", "state"},
     *             @OA\Property(property="location", type="string", example="Downtown"),
     *             @OA\Property(property="zone", type="string", example="Commercial"),
     *             @OA\Property(property="state", type="string", example="Active"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Location created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Location"
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(
     *                     property="location",
     *                     type="array",
     *                     @OA\Items(type="string", example="The location field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="zone",
     *                     type="array",
     *                     @OA\Items(type="string", example="The zone field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="state",
     *                     type="array",
     *                     @OA\Items(type="string", example="The state field is required.")
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function create(Request $request)
    {
        $result = $this->locationService->create($request);

        if ($result instanceof Location) {
            return response()->json(['success' => true, 'data' => $result], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }
    /**
     * @OA\Patch(
     *     path="/locations/{id}",
     *     summary="Update an existing location",
     *     tags={"Locations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the location to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="Location data to update",
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description"}, // Adjust based on the fields that can be updated
     *             @OA\Property(property="name", type="string", example="Central Park"),
     *             @OA\Property(property="description", type="string", example="A large public park in New York City")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Location updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Location" // Adjust with actual reference to your Location schema
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or update failure",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 additionalProperties={
     *                     @OA\Property(type="array", @OA\Items(type="string"))
     *                 }
     *             )
     *         )
     *     ),
     *     security={{ "apiAuth":{ }}}
     * )
     */
    public function updateLocation(Request $request, int $id,)
    {
        $result = $this->locationService->updateLocation($id, $request);

        if ($result instanceof Location) {
            return response()->json(['success' => true, 'data' => $result], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }
}
