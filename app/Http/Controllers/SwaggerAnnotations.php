<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Biblioteca API",
 *     description="Documentación de la API de la biblioteca",
 *     @OA\Contact(email="soporte@bibliotecaapi.com")
 * )
 *
 * @OA\Schema(
 *     schema="Author",
 *     type="object",
 *     title="Author",
 *     required={"id", "name"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="J.R.R. Tolkien"),
 *     @OA\Property(property="country", type="string", nullable=true, example="United Kingdom"),
 *     @OA\Property(property="bio", type="string", nullable=true, example="British writer, poet, philologist"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     required={"id", "name", "email", "role"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Juan Pérez"),
 *     @OA\Property(property="email", type="string", example="juan@email.com"),
 *     @OA\Property(property="role", type="string", example="admin"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 */
class SwaggerAnnotations extends Controller
{
    // Este archivo es solo para anotaciones OpenAPI
}
