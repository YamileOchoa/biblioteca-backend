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
 *     schema="Book",
 *     type="object",
 *     title="Book",
 *     required={"id", "title", "isbn"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="El Señor de los Anillos"),
 *     @OA\Property(property="isbn", type="string", example="978-3-16-148410-0"),
 *     @OA\Property(property="year", type="integer", example=1954),
 *     @OA\Property(property="author_id", type="integer", example=1),
 *     @OA\Property(property="category_id", type="integer", example=2),
 *     @OA\Property(property="cover_image", type="string", nullable=true, example="covers/lotr.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
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
