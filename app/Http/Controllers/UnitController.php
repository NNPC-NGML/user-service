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

        $response = $result
            ? ['success' => true, 'message' => 'Unit successfully deleted']
            : ['success' => false, 'message' => 'Unit not found'];

        return response()->json($response, $result ? 200 : 404);
    }
}
