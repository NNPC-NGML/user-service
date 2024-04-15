<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json($user, 201);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'invalid credentials'], 401);
        }

        $user = Auth::user();

        $jwt = $user->createToken('token', [$request->input('scope')])->plainTextToken;

        return response()->json(compact('jwt'));
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
        return 'ok';
    }
}