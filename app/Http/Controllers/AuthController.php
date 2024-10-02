<?php

namespace App\Http\Controllers;

use App\Jobs\User\UserCreated;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;

class AuthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/auth/initialize",
     *     summary="Get Microsoft OAuth redirect URL",
     *     description="This endpoint returns the Microsoft OAuth login page URL for authentication.",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with redirect URL",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="url",
     *                 type="string",
     *                 example="https://login.microsoftonline.com/..."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function initialize(Request $request)
    {
        try {
            $redirect = Socialite::driver('azure')->stateless()->redirect();
            $url = $redirect->getTargetUrl();

            return response()->json([
                'status' => 'success',
                'url' => $url,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to generate the Microsoft OAuth URL.',
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/callback",
     *     summary="Handle Microsoft OAuth callback",
     *     description="Handles the callback from Microsoft after user authentication. Registers a new user or logs in an existing user based on their Microsoft account information.",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", description="Authorization code from Microsoft", example="ABC123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered or logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1, description="Unique identifier for the user"),
     *                 @OA\Property(property="name", type="string", example="John Doe", description="User's full name"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com", description="User's email address"),
     *                 @OA\Property(property="azure_id", type="string", example="abc1234", description="User's unique Microsoft Azure ID"),
     *                 @OA\Property(property="status", type="integer", example=1, description="Status of the user (1 for active)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function callback(Request $request)
    {
        try {
            $code = $request->input('code');

            $tokenResponse = Socialite::driver('azure')->stateless()->getAccessTokenResponse($code);
            $user = Socialite::driver('azure')->stateless()->userFromToken($tokenResponse['access_token']);

            $user = User::firstOrCreate([
                'email' => $user->getEmail(),
            ], [
                'name' => $user->name,
                'email' => $user->email,
                'azure_id' => $user->id,
                'password' => Hash::make($user->id),
                'status' => 2,
            ]);
            $access_token = $user->createToken('auth_token')->plainTextToken;
            $user->access_token = $access_token;

            if ($user) {
                $userCreatedQueues = config("nnpcreusable.USER_CREATED");
                if (is_array($userCreatedQueues) && !empty($userCreatedQueues)) {
                    foreach ($userCreatedQueues as $queue) {
                        $queue = trim($queue);
                        if (!empty($queue)) {
                            Log::info("Dispatching UserCreated event to queue: " . $queue);
                            UserCreated::dispatch($user->toArray())->onQueue($queue);
                        }
                    }
                }
            }
            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

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

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            if ($user) {
                $userCreatedQueues = config("nnpcreusable.USER_CREATED");
                if (is_array($userCreatedQueues) && !empty($userCreatedQueues)) {
                    foreach ($userCreatedQueues as $queue) {
                        $queue = trim($queue);
                        if (!empty($queue)) {
                            Log::info("Dispatching UserCreated event to queue: " . $queue);
                            UserCreated::dispatch($user->toArray())->onQueue($queue);
                        }
                    }
                }
            }
            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                // 'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            if ($e instanceof RabbitMQConnector || strpos($e->getMessage(), 'RabbitMQ') !== false) {
                // Handle RabbitMQ specific error
                Log::error('RabbitMQ Error: ' . $e->getMessage());
                // You might want to use a fallback queue or storage method here
            }

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
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
            'jwt' => $jwt,
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
