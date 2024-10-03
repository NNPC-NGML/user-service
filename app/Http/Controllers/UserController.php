<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\UserService;
use Illuminate\Http\Request;
use App\Jobs\User\UserCreated;
use App\Jobs\User\UserDeleted;
use App\Jobs\User\UserUpdated;
use App\Http\Resources\UserResource;
use App\Jobs\DepartmentAssignment\DepartmentAssignmentCreated;
use App\Jobs\DesignationAssignment\DesignationAssignmentCreated;
use App\Jobs\LocationAssignment\LocationAssignmentCreated;
use App\Jobs\UnitAssignment\UnitAssignmentCreated;
use App\Models\DepartmentUser;
use App\Models\DesignationUser;
use App\Models\LocationUser;
use App\Models\UnitUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Controller(
 *     path="/api/v1",
 *     tags={"Users"}
 * )
 */
class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/create_user",
     *      operationId="createUser",
     *      tags={"Users"},
     *      summary="Create a new user",
     *      description="Create a new user with the provided data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="error", type="object")
     *          )
     *      )
     * )
     */
    public function create(Request $request)
    {
        $result = $this->userService->create($request);

        if ($result instanceof User) {
            foreach (config("nnpcreusable.USER_CREATED") as $queue) {
                UserCreated::dispatch($result->toArray())->onQueue($queue);
            }
            return response()->json(['success' => true, 'data' => new UserResource($result)], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/delete_user",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", example="123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User successfully deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"id": {"The id field is required."}})
     *         )
     *     )
     * )
     */



    public function delete(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer'
            ]);

            $result = $this->userService->deleteUser($validated['id']);

            if ($result) {

                foreach (config("nnpcreusable.USER_DELETED") as $queue) {
                    UserDeleted::dispatch($validated['id'])->onQueue($queue);
                }
                return response()->json(['success' => true, 'message' => 'User successfully deleted'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }
    }

    /**
     * @OA\Put(
     *     path="/users/{userId}",
     *     summary="Update user credentials",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="The id of the user to update",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         description="User credentials to update",
     *         @OA\JsonContent(
     *             required={"email", "name", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/UserResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $userId)
    {

        $result = $this->userService->updateUserCredentials($userId, $request->all());

        if ($result === false) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        } elseif (is_array($result)) {
            return response()->json(['success' => false, 'errors' => $result], 422);
        } else {

            foreach (config("nnpcreusable.USER_UPDATED") as $queue) {
                UserUpdated::dispatch($result->toArray())->onQueue($queue);
            }
            return response()->json(['success' => true, 'data' => new UserResource($result)], 200);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{userId}",
     *     summary="Get a specific user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID of the user to retrieve",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/UserResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="User not found"
     *             )
     *         )
     *     )
     * )
     */
    public function show($userId)
    {

        $user = $this->userService->getUser($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['success' => true, 'data' => new UserResource($user)], 200);
    }
    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get a list of users",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of users per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/UserResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
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
     *         response=404,
     *         description="Not Found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function index(Request $request)
    {

        $page = $request->query('page', 1);
        $perPage = $request->query('perPage', 10);

        $users = $this->userService->getUsersForPage($page, $perPage);

        return response()->json(['success' => true, 'data' => $users], 200);
    }

    /**
     * @OA\Post(
     *     path="/initialize_user_basic_info",
     *     summary="Initialize user basic information",
     *     tags={"Users"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"department_id", "location_id", "unit_id", "designation_id"},
     *             @OA\Property(property="department_id", type="integer", example=2),
     *             @OA\Property(property="location_id", type="integer", example=3),
     *             @OA\Property(property="unit_id", type="integer", example=4),
     *             @OA\Property(property="designation_id", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User basic information initialized successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User basic information initialized.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User or related entity not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User or related entity not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function initialize_user_basic_info(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'department_id' => 'required|exists:departments,id',
                'location_id' => 'required|exists:locations,id',
                'unit_id' => 'required|exists:units,id',
                'designation_id' => 'required|exists:designations,id',
            ]);

            $userId = Auth::user()->id;
            $assignDeptStatus = $this->userService->assignUserToDepartment($userId, $validated['department_id']);
            $assignLocationstatus = $this->userService->assignUserToLocation($userId, $validated['location_id']);
            $assignUnitstatus = $this->userService->assignUserToUnit($userId, $validated['unit_id']);
            $assignDesignationstatus = $this->userService->assignUserToDesignation($userId, $validated['designation_id']);

            if ($assignDeptStatus && $assignLocationstatus && $assignUnitstatus && $assignDesignationstatus) {
                $user = $this->userService->getUser($userId);
                if($user->status != 1) {
                    $user->status = 1;
                    $user->save();
                }

                DB::commit();

                $locationUser = LocationUser::where('user_id', $userId)->first();
                $unitUser = UnitUser::where('user_id', $userId)->first();
                $departmentUser = DepartmentUser::where('user_id', $userId)->first();
                $designationUser = DesignationUser::where('user_id', $userId)->first();

                $locationQueues = config("nnpcreusable.LOCATION_Assignment_CREATED");
                if (is_array($locationQueues) && !empty($locationQueues)) {
                    foreach ($locationQueues as $queue) {
                        $queue = trim($queue);
                        if (!empty($queue)) {
                            Log::info("Dispatching location assignment created event to queue: " . $queue);
                            LocationAssignmentCreated::dispatch($locationUser->toArray())->onQueue($queue);
                        }
                    }
                }

                $unitQueues = config("nnpcreusable.UNIT_Assignment_CREATED");
                if (is_array($unitQueues) && !empty($unitQueues)) {
                    foreach ($unitQueues as $queue) {
                        $queue = trim($queue);
                        if (!empty($queue)) {
                            Log::info("Dispatching unit assignment created event to queue: " . $queue);
                            UnitAssignmentCreated::dispatch($unitUser->toArray())->onQueue($queue);
                        }
                    }
                }

                $departmentQueues = config("nnpcreusable.DEPARTMENT_Assignment_CREATED");
                if (is_array($departmentQueues) && !empty($departmentQueues)) {
                    foreach ($departmentQueues as $queue) {
                        $queue = trim($queue);
                        if (!empty($queue)) {
                            Log::info("Dispatching department assignment created event to queue: " . $queue);
                            DepartmentAssignmentCreated::dispatch($departmentUser->toArray())->onQueue($queue);
                        }
                    }
                }

                $designationQueues = config("nnpcreusable.DESIGNATION_Assignment_CREATED");
                if (is_array($designationQueues) && !empty($designationQueues)) {
                    foreach ($designationQueues as $queue) {
                        $queue = trim($queue);
                        if (!empty($queue)) {
                            Log::info("Dispatching designation assignment created event to queue: " . $queue);
                            DesignationAssignmentCreated::dispatch($designationUser->toArray())->onQueue($queue);
                        }
                    }
                }

                return response()->json(['success' => true, 'data' => []], 200);
            }

            DB::rollBack();
            return response()->json(['error' => 'invalid request sent'], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 422);
        }
    }
}
