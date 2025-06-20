<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddFavoriteRequest;
use App\Http\Service\FavoriteService;

/**
 * @OA\Tag(
 *     name="Favorite",
 *     description="Operations to manage users' favorite books"
 * )
 */
class FavoriteController extends Controller
{
    protected $service;

    public function __construct(FavoriteService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/favorites",
     *     summary="List the authenticated user's favorite books",
     *     tags={"Favorite"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of favorite books",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Book"))
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index()
    {
        $user = Auth::user();
        return $this->service->listFavorites($user);
    }

    /**
     * @OA\Post(
     *     path="/api/favorites",
     *     summary="Add a book to favorites",
     *     tags={"Favorite"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"book_id"},
     *             @OA\Property(property="book_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book added to favorites",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Book added to favorites")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Invalid data")
     * )
     */
    public function store(AddFavoriteRequest $request)
    {
        $user = Auth::user();
        $this->service->addToFavorites($user, $request->book_id);
        return response()->json(['message' => 'Book added to favorites']);
    }

    /**
     * @OA\Delete(
     *     path="/api/favorites/{book_id}",
     *     summary="Remove a book from favorites",
     *     tags={"Favorite"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="book_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book removed from favorites",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Book removed from favorites")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Book not found")
     * )
     */
    public function destroy($book_id)
    {
        $user = Auth::user();
        $this->service->removeFromFavorites($user, $book_id);
        return response()->json(['message' => 'Book removed from favorites']);
    }
}
