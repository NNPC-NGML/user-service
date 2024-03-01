<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Service\UserService;
use Illuminate\Http\Request;

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
     *              @OA\Property(property="data", ref="#/components/schemas/User")
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
        $request->validate([
            'id' => 'required|integer'
        ]);

        $result = $this->userService->deleteUser($request->id);

        $response = $result
            ? ['success' => true, 'message' => 'User successfully deleted']
            : ['success' => false, 'message' => 'User not found'];

        return response()->json($response, $result ? 200 : 404);
    }
}
