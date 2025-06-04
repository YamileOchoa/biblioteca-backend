<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *     schema="Book",
 *     type="object",
 *     title="Book",
 *     required={"id", "title", "isbn", "year", "author_id", "category_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="El señor de los anillos"),
 *     @OA\Property(property="isbn", type="string", example="9788478884453"),
 *     @OA\Property(property="year", type="integer", example=1954),
 *     @OA\Property(property="author_id", type="integer", example=2),
 *     @OA\Property(property="category_id", type="integer", example=3),
 *     @OA\Property(property="cover_image", type="string", nullable=true, example="covers/imagen.jpg"),
 *     @OA\Property(property="cover_image_url", type="string", nullable=true, example="http://tu-dominio/storage/covers/imagen.jpg"),
 *     @OA\Property(property="synopsis", type="string", nullable=true, example="Una historia fantástica..."),
 *     @OA\Property(property="pages", type="integer", nullable=true, example=423),
 *     @OA\Property(property="publisher", type="string", nullable=true, example="Editorial XYZ"),
 *     @OA\Property(property="stock", type="integer", nullable=true, example=10)
 * )
 */
class BookController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/books",
     *     summary="List all books",
     *     tags={"Books"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Book"))
     *     )
     * )
     */
    public function index()
    {
        return Book::with(['author', 'category'])->get()->map(function ($book) {
            if ($book->cover_image) {
                $book->cover_image_url = asset('storage/' . $book->cover_image);
            }
            return $book;
        });
    }

    /**
     * @OA\Post(
     *     path="/api/books",
     *     summary="Create a new book",
     *     tags={"Books"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "isbn", "year", "author_id", "category_id"},
     *             @OA\Property(property="title", type="string", example="El señor de los anillos"),
     *             @OA\Property(property="isbn", type="string", example="9788478884453"),
     *             @OA\Property(property="year", type="integer", example=1954),
     *             @OA\Property(property="author_id", type="integer", example=2),
     *             @OA\Property(property="category_id", type="integer", example=3),
     *             @OA\Property(property="cover_image", type="string", format="binary", description="Cover image file"),
     *             @OA\Property(property="synopsis", type="string", example="Una historia fantástica..."),
     *             @OA\Property(property="pages", type="integer", example=423),
     *             @OA\Property(property="publisher", type="string", example="Editorial XYZ"),
     *             @OA\Property(property="stock", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Book created", @OA\JsonContent(ref="#/components/schemas/Book")),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string',
            'isbn' => 'required|string|unique:books',
            'year' => 'required|integer',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'nullable|image|max:2048',
            'synopsis' => 'nullable|string',
            'pages' => 'nullable|integer',
            'publisher' => 'nullable|string',
            'stock' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['title', 'isbn', 'year', 'author_id', 'category_id', 'synopsis', 'pages', 'publisher', 'stock']);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $data['cover_image'] = $path;
        }

        $book = Book::create($data);
        return response()->json($book, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/books/{book}",
     *     summary="Get a book by ID",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="book",
     *         in="path",
     *         description="Book ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/Book")),
     *     @OA\Response(response=404, description="Book not found")
     * )
     */
    public function show(Book $book)
    {
        $book->load(['author', 'category']);
        if ($book->cover_image) {
            $book->cover_image_url = asset('storage/' . $book->cover_image);
        }
        return $book;
    }

    /**
     * @OA\Put(
     *     path="/api/books/{book}",
     *     summary="Update a book",
     *     tags={"Books"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="book",
     *         in="path",
     *         description="Book ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="El señor de los anillos"),
     *             @OA\Property(property="isbn", type="string", example="9788478884453"),
     *             @OA\Property(property="year", type="integer", example=1954),
     *             @OA\Property(property="author_id", type="integer", example=2),
     *             @OA\Property(property="category_id", type="integer", example=3),
     *             @OA\Property(property="cover_image", type="string", format="binary", description="Cover image file"),
     *             @OA\Property(property="synopsis", type="string", example="Una historia fantástica..."),
     *             @OA\Property(property="pages", type="integer", example=423),
     *             @OA\Property(property="publisher", type="string", example="Editorial XYZ"),
     *             @OA\Property(property="stock", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Book updated", @OA\JsonContent(ref="#/components/schemas/Book")),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=404, description="Book not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, Book $book)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string',
            'isbn' => 'sometimes|string|unique:books,isbn,' . $book->id,
            'year' => 'sometimes|integer',
            'author_id' => 'sometimes|exists:authors,id',
            'category_id' => 'sometimes|exists:categories,id',
            'cover_image' => 'nullable|image|max:2048',
            'synopsis' => 'nullable|string',
            'pages' => 'nullable|integer',
            'publisher' => 'nullable|string',
            'stock' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['title', 'isbn', 'year', 'author_id', 'category_id', 'synopsis', 'pages', 'publisher', 'stock']);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $path = $request->file('cover_image')->store('covers', 'public');
            $data['cover_image'] = $path;
        }

        $book->update($data);
        return response()->json($book);
    }

    /**
     * @OA\Delete(
     *     path="/api/books/{book}",
     *     summary="Delete a book",
     *     tags={"Books"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="book",
     *         in="path",
     *         description="Book ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Book deleted"),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=404, description="Book not found")
     * )
     */
    public function destroy(Book $book)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();
        return response()->json(null, 204);
    }

    /**
 * @OA\Post(
 *     path="/api/books/{book}/decrement-stock",
 *     summary="Decrement stock of a book",
 *     tags={"Books"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="book",
 *         in="path",
 *         description="Book ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"quantity"},
 *             @OA\Property(property="quantity", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(response=200, description="Stock decremented", @OA\JsonContent(ref="#/components/schemas/Book")),
 *     @OA\Response(response=400, description="Not enough stock"),
 *     @OA\Response(response=404, description="Book not found"),
 *     @OA\Response(response=403, description="Unauthorized")
 * )
 */
public function decrementStock(Request $request, Book $book)
{
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $request->validate([
        'quantity' => 'required|integer|min:1'
    ]);

    $quantity = $request->input('quantity');

    if ($book->stock === null || $book->stock < $quantity) {
        return response()->json(['message' => 'Not enough stock'], 400);
    }

    $book->stock -= $quantity;
    $book->save();

    if ($book->cover_image) {
        $book->cover_image_url = asset('storage/' . $book->cover_image);
    }

    return response()->json($book);
}

/**
 * @OA\Get(
 *     path="/api/books/available",
 *     summary="List books with stock available",
 *     tags={"Books"},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Book"))
 *     )
 * )
 */
public function available()
{
    $books = Book::where('stock', '>', 0)->with(['author', 'category'])->get()->map(function ($book) {
        if ($book->cover_image) {
            $book->cover_image_url = asset('storage/' . $book->cover_image);
        }
        return $book;
    });

    return response()->json($books);
}

}
