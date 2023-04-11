<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Requests\Auth\AuthRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Register user
     * @param AuthRegisterRequest $request
     * @return JsonResponse
     */
    public function register(AuthRegisterRequest $request): JsonResponse
    {
        // create user
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);

        // create token
        $response['token'] = $user->createToken('MyApp')->plainTextToken;
        $response['name'] = $user->name;
        $response['email'] = $user->email;

        return $this->success($response);
    }

    /**
     * Login
     * @param AuthLoginRequest $request
     * @return JsonResponse
     */
    public function login(AuthLoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        if (Auth::attempt($validated)) {
            $user = Auth::user();
            $response['token'] = $user->createToken('MyApp')->plainTextToken;
            $response['name'] = $user->name;
            $response['email'] = $user->email;

            return $this->success($response);
        } else {
            return $this->error('Unauthorised', Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Logout
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        return $this->success();
    }
}
