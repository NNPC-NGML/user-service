<?php

namespace App\Http\Controllers;
use App\Models\Designation;
use App\Service\DesignationService;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    protected $designationService;

    public function __construct(DesignationService $designationService)
    {
        $this->designationService = $designationService;
    }

    /**
     * @OA\Post(
     *     path="/create_designations",
     *     summary="Create a new designation",
     *     tags={"Designation"},
     *     @OA\RequestBody(
     *         description="Data for creating a new designation",
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role", "description"},
     *             @OA\Property(property="role", type="string", example="Admin"),
     *             @OA\Property(property="description", type="string", example="Administrative designation"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Designation created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Designation"
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
     *                     property="role",
     *                     type="array",
     *                     @OA\Items(type="string", example="The role field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="array",
     *                     @OA\Items(type="string", example="The description field is required.")
     *                 ),
     *                 
     *             ),
     *         ),
     *     ),
     * )
     */

    public function create(Request $request)
    {
        $result = $this->designationService->create($request);

        if ($result instanceof Designation) {
            return response()->json(['success' => true, 'data' => $result], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }


    public function updateDesignation(Request $request, int $id,)
    {
        $result = $this->designationService->updateDesignation($id, $request);

        if ($result instanceof Designation) {
            return response()->json(['success' => true, 'data' => $result], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }
}
