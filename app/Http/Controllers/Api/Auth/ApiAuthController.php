<?php

// app/Http/Controllers/Api/Auth/ApiAuthController.php
namespace App\Http\Controllers\Api\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequestApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ApiAuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/auth/login",
     *   tags={"Auth"},
     *   summary="Login (solo rol validator)",
     *   description="Devuelve un token Bearer si el usuario existe, password válido y role=validator.",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Login correcto",
     *     @OA\JsonContent(ref="#/components/schemas/LoginResponse")
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Credenciales inválidas",
     *     @OA\JsonContent(ref="#/components/schemas/Error")
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Rol no permitido",
     *     @OA\JsonContent(ref="#/components/schemas/Error")
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validación fallida",
     *     @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *   )
     * )
     */
    public function login(LoginRequestApi $request)
    {
        /** @var User|null $user */
        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        // Acepta enum o string (compatibilidad)
        $role = $user->role instanceof UserRole ? $user->role->value : $user->role;

        // Solo permitir VALIDATOR
        if ($role !== 'validator') {
            return response()->json(['message' => 'Acceso restringido a validadores.'], 403);
        }

        // (Opcional) más chequeos de estado del usuario
        // if (!$user->is_active) return response()->json(['message'=>'Cuenta inactiva.'], 403);

        // Crear token con abilities reducidas
        $deviceName = $request->input('device') ?: 'android';
        $token = $user->createToken($deviceName, ['validator'])->plainTextToken;

        return response()->json([
            'token_type' => 'Bearer',
            'token'      => $token,
            'user'       => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $role,
            ],
        ], 200);
    }

    /**
     * @OA\Get(
     *   path="/auth/me",
     *   tags={"Auth"},
     *   summary="Perfil del usuario autenticado",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/MeResponse")
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="No autenticado",
     *     @OA\JsonContent(ref="#/components/schemas/Error")
     *   )
     * )
     */
    public function me(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role instanceof UserRole ? $user->role->value : $user->role,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/auth/logout",
     *   tags={"Auth"},
     *   summary="Cerrar sesión (revoca token actual)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/Message")
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="No autenticado",
     *     @OA\JsonContent(ref="#/components/schemas/Error")
     *   )
     * )
     */
    public function logout(Request $request)
    {
        // Revoca SOLO el token actual
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada.']);
    }
}
