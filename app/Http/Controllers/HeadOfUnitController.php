<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Service\HeadOfUnitService;
use Illuminate\Support\Facades\DB;
use App\Jobs\HeadOfUnit\HeadOfUnitCreated;
use App\Http\Resources\HeadOfUnitsResource;

class HeadOfUnitController extends Controller
{

    protected $headOfUnitService;
    public function __construct(HeadOfUnitService $headOfUnitService)
    {
        $this->headOfUnitService = $headOfUnitService;
    }

    /**
     * @OA\Get(
     *     path="/headofunit",
     *     tags={"HeadOfunit"},
     *     summary="Get all head of unit in the system",
     *     description="Returns all available head of units ",
     *     @OA\Response(
     *         response=200,
     *         description="Head Of Unit found",
     *         @OA\JsonContent(ref="#/components/schemas/HeadOfUnitsResource")
     *     ),
     * @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     * @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * @OA\Response(
     *          response=404,
     *          description="Not Found",
     *      ),
     * @OA\Response(
     *          response=500,
     *          description="Server Error",
     *      ),
     *  security={
     *         {"BearerAuth": {}}
     *     }
     * )
     */
    public function index()
    {
        $result = $this->headOfUnitService->viewAllHeadOfUnits();
        return ["data" => $result];
        if ($result) {
            return HeadOfUnitsResource::collection($result);;
        }
    }



    /**
     * @OA\Post(
     *     path="/headofunit/create",
     *     summary="Create head of unit",
     *     tags={"HeadOfunit"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="unit_id",
     *         in="query",
     *         required=true,
     *         description="ID of the unit",
     *         @OA\Schema(type="integer")
     *     ),
     * 
     *     @OA\Parameter(
     *         name="location_id",
     *         in="query",
     *         required=true,
     *         description="ID of the location",
     *         @OA\Schema(type="integer")
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Process Flow created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/HeadOfUnitsResource")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", description="Error message"),
     *         ),
     *     ),
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $result = $this->headOfUnitService->create($request);
            if ($result) {
                DB::commit();
                foreach (config("nnpcreusable.HEADOFUNIT_CREATED") as $queue) {
                    HeadOfUnitCreated::dispatch($result->toArray())->onQueue($queue);
                }
                return new HeadOfUnitsResource($result);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.'], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/headofunit/view/{id}",
     *     summary="View a specific head of unit",
     *     tags={"HeadOfunit"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the process flow step to view",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/HeadOfUnitsResource"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something went wrong")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="id is invalid")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $getHeadOfUnit = $this->headOfUnitService->getHeadOfUnitById($id);

        if ($getHeadOfUnit) {
            return (new HeadOfUnitsResource($getHeadOfUnit))->response()->setStatusCode(200);;
        }
        return response()->json(
            [
                "status" => "error",
                "message" => "could not fetch head of unit"
            ],
            400
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $headOfUnit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($headOfUnit)
    {
        //
    }
}
