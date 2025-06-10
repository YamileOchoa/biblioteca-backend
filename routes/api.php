<?php

use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\LoanController;

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Ruta pública para listar libros con stock disponible
Route::get('/books/available', [BookController::class, 'available']);

// Rutas protegidas por autenticacion Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    //Para que usuario pueda hacer el prestamos
    Route::post('/loans', [LoanController::class, 'store']);

    Route::post('/loans/{loan}/mark-returned', [LoanController::class, 'markAsReturned']);


    // Lectura permitida para todos los usuarios autenticados
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{book}', [BookController::class, 'show']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    Route::get('/authors', [AuthorController::class, 'index']);
    Route::get('/authors/{author}', [AuthorController::class, 'show']);

    Route::get('/loans', [LoanController::class, 'index']);
    Route::get('/loans/{loan}', [LoanController::class, 'show']);

    // Rutas para administrador
    Route::middleware(IsAdmin::class)->group(function () {
        // Books
        Route::post('/books', [BookController::class, 'store']);
        Route::put('/books/{book}', [BookController::class, 'update']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);

        // Ruta para decrementar stock de un libro
        Route::post('/books/{book}/decrement-stock', [BookController::class, 'decrementStock']);

        // Categories
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // Authors
        Route::post('/authors', [AuthorController::class, 'store']);
        Route::put('/authors/{author}', [AuthorController::class, 'update']);
        Route::delete('/authors/{author}', [AuthorController::class, 'destroy']);

        // Loans
        Route::put('/loans/{loan}', [LoanController::class, 'update']);
        Route::delete('/loans/{loan}', [LoanController::class, 'destroy']);
    });
});

Route::middleware(['auth:sanctum', 'is.admin'])->get('/admin-test', function () {
    return response()->json(['ok' => true]);
});
