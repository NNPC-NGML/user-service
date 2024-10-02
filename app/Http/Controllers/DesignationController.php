<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;
use App\Service\DesignationService;
use App\Http\Resources\DesignationResource;
use App\Jobs\Designation\DesignationCreated;
use App\Jobs\Designation\DesignationDeleted;
use App\Jobs\Designation\DesignationUpdated;

class DesignationController extends Controller
{
    protected $designationService;

    public function __construct(DesignationService $designationService)
    {
        $this->designationService = $designationService;
    }

    /**
     * @OA\Get(
     *      path="/designations",
     *      operationId="getAllDesignations",
     *      tags={"Designations"},
     *      summary="Get all designations",
     *      description="Returns all designations",
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
     *                  description="designation resource",
     *                  ref="#/components/schemas/DesignationResource"
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
     * )*/
    public function index()
    {
        $result = $this->designationService->viewAllDesignations();
        //if ($result instanceof Collection) {
        return response()->json(['success' => true, 'data' => new DesignationResource($result)], 200);
        // }
    }

    /**
     * @OA\Get(
     *     path="/designations/{designationId}",
     *     summary="Get a designation by ID",
     *     description="Retrieve information about a designation by its ID.",
     *     tags={"Designations"},
     *     @OA\Parameter(
     *         name="designationId",
     *         in="path",
     *         description="ID of the designation to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/DesignationResource"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Designation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Designation not found"),
     *         ),
     *     ),
     * )
     */

    public function show($id)
    {
        $result = $this->designationService->getDesignation($id);
        if ($result instanceof Designation) {
            return response()->json(['success' => true, 'data' => new DesignationResource($result)], 200);
        } else {
            return response()->json(['success' => false, 'error' => $result], 404);
        }
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
     *                 ref="#/components/schemas/DesignationResource"
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
            foreach (config("nnpcreusable.DESIGNATION_CREATED") as $queue) {
                DesignationCreated::dispatch($result->toArray())->onQueue($queue);
            }
            return response()->json(['success' => true, 'data' => $result], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }


    public function updateDesignation(Request $request, int $id,)
    {
        $result = $this->designationService->updateDesignation($id, $request);

        if ($result instanceof Designation) {
            foreach (config("nnpcreusable.DESIGNATION_UPDATED") as $queue) {
                DesignationUpdated::dispatch($result->toArray())->onQueue($queue);
            }
            return response()->json(['success' => true, 'data' => $result], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/designations/{id}",
     *     summary="Deletes a specific Designation",
     *     operationId="designationId",
     *     tags={"Designations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the designation to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Designation deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Designation deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Designation not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Designation not found")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, $id)
    {
        $result = $this->designationService->deleteDesignation($id);
        if ($result) {
            foreach (config("nnpcreusable.DESIGNATION_DELETED") as $queue) {
                DesignationDeleted::dispatch($id)->onQueue($queue);
            }
            return response()->json(['success' => true], 204);
        } else {
            return response()->json(['success' => false], 404);
        }
    }
}
