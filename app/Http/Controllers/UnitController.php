<?php

namespace App\Http\Controllers;

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
     *     @OA\RequestBody(
     *         description="Data for creating a new unit",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UnitRequest")
     *     ),
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
            return response()->json(['success' => true, 'message' => 'Unit created successfully'], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
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
     *                 ref="#/components/schemas/Unit"
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
            return response()->json(['success' => true, 'data' => $result, 'message' => 'Unit updated successfully'], 200);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }
}
