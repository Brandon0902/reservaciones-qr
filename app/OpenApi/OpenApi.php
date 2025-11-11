<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *   title="Reservas QR — Auth API",
 *   version="1.0.0",
 *   description="API con Laravel Sanctum (tokens personales). Login exclusivo para rol `validator`."
 * )
 *
 * @OA\Server(
 *   url="http://127.0.0.1:8000/api",
 *   description="Local"
 * )
 * @OA\Server(
 *   url="https://tu-dominio-o-cloudflared/api",
 *   description="Staging/Producción"
 * )
 *
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="Bearer"
 * )
 */
class OpenApi {}
