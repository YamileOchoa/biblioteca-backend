<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Services\AuthService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Service\AuthService as ServiceAuthService;
use Auth;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints para registro, login y logout"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Registrar un nuevo usuario",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Juan Perez"),
     *             @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret123"),
     *             @OA\Property(property="role", type="string", enum={"admin","lector"}, example="lector")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Registro exitoso, token devuelto",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="1|abcdefg1234567890")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Errores de validación")
     * )
     */
    public function register(RegisterRequest $request, ServiceAuthService $service)
    {
        $token = $service->register($request->validated());
        return response()->json(['token' => $token]);
    }


    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Iniciar sesión",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login exitoso, token devuelto",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="1|abcdefg1234567890")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Credenciales inválidas")
     * )
     */
    public function login(LoginRequest $request, ServiceAuthService $service)
    {
        $token = $service->login($request->validated());

        if (!$token) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json(['token' => $token]);
    }
    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Cerrar sesión",
     *     tags={"Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Logout exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout successful")
     *         )
     *     )
     * )
     */
    public function logout(Request $request, ServiceAuthService $service)
    {
        $service->logout($request->user());
        return response()->json(['message' => 'Logout successful']);
    }
}
