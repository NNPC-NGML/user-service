<?php

namespace App\Http\Controllers;

use App\Http\Resources\UnitResource;
use App\Jobs\Unit\UnitCreated;
use App\Jobs\Unit\UnitDeleted;
use App\Jobs\Unit\UnitUpdated;
use App\Models\Unit;
use App\Service\UnitService;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    /**
     * @OA\Post(
     *     path="/unit",
     *     summary="Create a new unit",
     *     tags={"Unit"},
     *     operationId="createUnit",
     *     @OA\Response(
     *         response=201,
     *         description="Unit created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unit created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Error details")
     *         )
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     */
    public function create(Request $request)
    {
        $result = $this->unitService->create($request);

        if ($result instanceof Unit) {

            UnitCreated::dispatch($result->toArray());
            return response()->json(['success' => true, 'message' => 'Unit created successfully'], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/units",
     *     operationId="viewAllUnits",
     *     tags={"Units"},
     *     summary="Get list of all units",
     *     description="Returns a list of all units.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Unit 1"),
     *
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function index()
    {

        $units = $this->unitService->viewAllUnits();

        return response()->json(['success' => true, 'data' => new UnitResource($units)], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/unit/{id}",
     *     summary="Delete a unit by ID",
     *     tags={"Units"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the unit to delete",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unit successfully deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unit not found")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {

        $result = $this->unitService->deleteUnit($id);


        if ($result) {
            UnitDeleted::dispatch($id);
        }
        $response = $result
            ? ['success' => true, 'message' => 'Unit successfully deleted']
            : ['success' => false, 'message' => 'Unit not found'];


        return response()->json($response, $result ? 200 : 404);
    }

    /**
     * @OA\Get(
     *     path="/units/{id}",
     *     operationId="getUnitById",
     *     tags={"Units"},
     *     summary="Get unit information",
     *     description="Returns unit data",
     *     @OA\Parameter(
     *         name="id",
     *         description="Unit id",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/UnitResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Unit not found"
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {

        $unit = $this->unitService->getUnit($id);

        if (!$unit) {
            return response()->json(['success' => false, 'error' => 'Unit not found'], 404);
        }

        return response()->json(['success' => true, 'data' => new UnitResource($unit)], 200);
    }
    /**
     * Retrieve units in a department.
     *
     * @OA\Get(
     *      path="/departments/{departmentId}/units",
     *      operationId="getUnitsInDepartment",
     *      tags={"Units"},
     *      summary="Get units in a department",
     *      description="Retrieves units belonging to a specific department.",
     *      @OA\Parameter(
     *          name="departmentId",
     *          in="path",
     *          required=true,
     *          description="ID of the department",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean",
     *                  example=true,
     *                  description="Indicates whether the request was successful."
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/UnitResource"),
     *                  description="Array of units belonging to the department"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Units successfully retrieved.",
     *                  description="Success message"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean",
     *                  example=false,
     *                  description="Indicates whether the request was successful."
     *              ),
     *              @OA\Property(
     *                  property="error",
     *                  type="mixed",
     *                  description="Error message or data"
     *              )
     *          )
     *      )
     * )
     *
     * @param int $departmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnitsInDepartment($departmentId)
    {
        $units = $this->unitService->getUnitsInDepartment($departmentId);

        if ($units)
            return response()->json(['success' => true, 'data' => new UnitResource($units), 'message' => 'Units successfully retrieved.'], 200);

        return response()->json(['success' => false, 'error' => $units], 422);
    }
    /**
     * Update a unit.
     *
     * @OA\Put(
     *     path="/api/v1/units/{id}",
     *     summary="Update a unit",
     *     tags={"Units"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the unit to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="The name of the unit (optional)"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="The description of the unit (optional)"
     *                 ),
     *                 @OA\Property(
     *                     property="department_id",
     *                     type="integer",
     *                     description="The ID of the department the unit belongs to (optional)"
     *                 ),
     *                 example={"name": "New Name", "description": "New Description", "department_id": 1}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Unit updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Updated unit data",
     *                 ref="#/components/schemas/UnitResource"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Success message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 description="Validation errors",
     *                 @OA\Property(
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {

        $result = $this->unitService->updateUnit($id, $request->all());

        if ($result instanceof unit) {
            UnitUpdated::dispatch($result->toArray());
            return response()->json(['success' => true, 'data' => $result, 'message' => 'Unit updated successfully'], 200);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }
}
