<?php

namespace App\OpenApi;

/**
 * @OA\Schema(
 *   schema="LoginRequest",
 *   type="object",
 *   required={"email","password"},
 *   @OA\Property(property="email", type="string", format="email", example="validator@demo.com"),
 *   @OA\Property(property="password", type="string", format="password", example="secreta"),
 *   @OA\Property(property="device", type="string", nullable=true, example="android", description="Nombre del dispositivo/cliente para identificar el token")
 * )
 *
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   required={"id","email","role"},
 *   @OA\Property(property="id", type="integer", example=4),
 *   @OA\Property(property="name", type="string", nullable=true, example="Validador Demo"),
 *   @OA\Property(property="email", type="string", format="email", example="validator@demo.com"),
 *   @OA\Property(property="role", type="string", enum={"admin","validator","customer"}, example="validator")
 * )
 *
 * @OA\Schema(
 *   schema="LoginResponse",
 *   type="object",
 *   required={"token_type","token","user"},
 *   @OA\Property(property="token_type", type="string", example="Bearer"),
 *   @OA\Property(property="token", type="string", example="1|S1lZ7NMzSFP5D628ARIRqS..."),
 *   @OA\Property(property="user", ref="#/components/schemas/User")
 * )
 *
 * @OA\Schema(
 *   schema="MeResponse",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=4),
 *   @OA\Property(property="name", type="string", example="Validador Demo"),
 *   @OA\Property(property="email", type="string", example="validator@demo.com"),
 *   @OA\Property(property="role", type="string", example="validator")
 * )
 *
 * @OA\Schema(
 *   schema="PingResponse",
 *   type="object",
 *   @OA\Property(property="ok", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *   schema="Message",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="Sesión cerrada.")
 * )
 *
 * @OA\Schema(
 *   schema="Error",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="No autenticado.")
 * )
 *
 * @OA\Schema(
 *   schema="ValidationError",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="The email field must be a valid email address."),
 *   @OA\Property(
 *     property="errors",
 *     type="object",
 *     additionalProperties=@OA\Schema(type="array", @OA\Items(type="string")),
 *     example={"email": {"The email field must be a valid email address."}}
 *   )
 * )
 */
class Schemas {}
