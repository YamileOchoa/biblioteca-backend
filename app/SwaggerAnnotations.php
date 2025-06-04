<?php

namespace App;

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
 */
class SwaggerAnnotations {}
