<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Service\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Create a new user.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object containing user data.
     *
     * @return \Illuminate\Http\JsonResponse Returns a JSON response indicating the result of the operation.
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
}
