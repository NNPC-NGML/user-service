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
     * Retrieve all units.
     *
     * @OA\Get(
     *      path="/units",
     *      operationId="getAllUnits",
     *      tags={"Units"},
     *      summary="Get all units",
     *      description="Retrieves all units stored in the system.",
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
     *                  @OA\Items(ref="#/components/schemas/Unit"),
     *                  description="Array of units"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Unit successfully retrieved.",
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $result = $this->unitService->viewAllUnits();

        if ($result) {
            return response()->json(['success' => true, 'data' => $result, 'message' => 'Unit successfully retrieved.'], 200);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }
}
