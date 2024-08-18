<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful registration",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="id", type="integer", format="int64", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"email": {"The email has already been taken."}}),
     *         )
     *     )
     * )
     */

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create a new user with the validated data
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
        return response()->json($user, 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="John Doe"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     example="john@example.com"
     *                 )
     *             ),
     *             @OA\Property(property="jwt", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="invalid credentials")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'invalid credentials'], 401);
        }

        $user = Auth::user();

        $jwt = $user->createToken('token', [$request->input('scope')])->plainTextToken;

        // return response()->json(compact('jwt'));
        return response()->json([
            'user' => $user,
            'jwt' => $jwt
        ]);
    }



    // public function register(Request $request)
    // {
    //     $user = User::create(
    //         $request->only('first_name', 'last_name', 'email', 'is_admin')
    //         + ['password' => \Hash::make($request->input('password'))]
    //     );

    //     return response($user, \Response::HTTP_CREATED);
    // }

    // public function login(Request $request)
    // {
    //     if (!\Auth::attempt($request->only('email', 'password'))) {
    //         return response([
    //             'error' => 'invalid credentials'
    //         ], \Response::HTTP_UNAUTHORIZED);
    //     }

    //     $user = \Auth::user();

    //     $jwt = $user->createToken('token', [$request->input('scope')])->plainTextToken;

    //     return compact('jwt');
    // }

    // public function user(Request $request)
    // {
    //     return $request->user();
    // }

    // public function logout(Request $request)
    // {
    //     $request->user()->tokens()->delete();

    //     return response([
    //         'message' => 'success'
    //     ]);
    // }

    // public function updateInfo(Request $request)
    // {
    //     $user = $request->user();

    //     $user->update($request->only('first_name', 'last_name', 'email'));

    //     return response($user, Response::HTTP_ACCEPTED);
    // }

    // public function updatePassword(Request $request)
    // {
    //     $user = $request->user();

    //     $user->update([
    //         'password' => \Hash::make($request->input('password'))
    //     ]);

    //     return response($user, Response::HTTP_ACCEPTED);
    // }





    /**
     * @OA\Get(
     *     path="/scope/{scope}",
     *     summary="Allow or Abort Access",
     *     tags={"Authenticate"},
     *     @OA\Parameter(
     *         name="scope",
     *         in="path",
     *         required=true,
     *         description="ID of the user to retrieve",
     *         @OA\Schema(
     *             type="string",
     *
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
     *
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Abort Process ",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Abort Process"
     *             )
     *         )
     *     )
     * )
     */


    /**
     * Get authenticated has the specified scope.
     * It talks to package middleware allows access to the current endpoint and abort if the user has no access
     *
     * @param \Illuminate\Http\Request $request
     * @param string $scope
     * @return string 'ok' if the user has the scope, otherwise aborts with a 401 Unauthorized response.
     */

    public function scopeCan(Request $request, $scope)
    {
        if (!$request->user()->tokenCan($scope)) {
            abort(401, 'unauthorized');
        }
        return $request->user();
    }
}
