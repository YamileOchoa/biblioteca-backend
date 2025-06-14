<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="Loan",
 *     type="object",
 *     title="Loan",
 *     required={"id", "user_id", "book_id", "status"},
 *     @OA\Property(property="id", type="integer", example=5),
 *     @OA\Property(property="user_id", type="integer", example=3),
 *     @OA\Property(property="book_id", type="integer", example=11),
 *     @OA\Property(property="loan_date", type="string", format="date", nullable=true, example="2025-06-01"),
 *     @OA\Property(property="return_date", type="string", format="date", nullable=true, example="2025-06-15"),
 *     @OA\Property(property="status", type="string", example="aprobado"),
 * )
 */
class LoanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/loans",
     *     summary="List all loans (admin) or user's own loans",
     *     tags={"Loans"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Loan"))
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return Loan::with(['user', 'book'])->get();
        }

        return Loan::with('book')->where('user_id', $user->id)->get();
    }

    /**
     * @OA\Post(
     *     path="/api/loans",
     *     summary="Solicitar un préstamo (usuario autenticado)",
     *     tags={"Loans"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"book_id"},
     *             @OA\Property(property="book_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Préstamo aprobado automáticamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Préstamo aprobado automáticamente."),
     *             @OA\Property(property="loan", ref="#/components/schemas/Loan")
     *         )
     *     ),
     *     @OA\Response(response=403, description="No puedes solicitar el préstamo"),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        // Validar si ya tiene prestamo activo o pendiente sin devolver
        $tienePrestamoActivo = Loan::where('user_id', $user->id)
            ->whereIn('status', ['pendiente', 'aprobado'])
            ->whereNull('return_date')
            ->exists();

        if ($tienePrestamoActivo) {
            return response()->json([
                'message' => 'No puedes solicitar un nuevo préstamo hasta devolver el anterior.'
            ], 403);
        }

        $book = \App\Models\Book::find($validated['book_id']);

        try {
            $book->disminuirStock();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 403);
        }

        $loan = Loan::create([ 
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => now(),
            'due_date' => now()->addDays(15),
            'return_date' => null, 
            'status' => 'aprobado',
        ]);

        return response()->json([
            'message' => 'Préstamo aprobado automáticamente.',
            'loan' => $loan
        ], 201);
    }


    /**
     * @OA\Get(
     *     path="/api/loans/{loan}",
     *     summary="Get a specific loan",
     *     tags={"Loans"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="loan",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Loan found", @OA\JsonContent(ref="#/components/schemas/Loan")),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(Loan $loan)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $loan->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $loan->load(['user', 'book']);
    }

    /**
     * @OA\Put(
     *     path="/api/loans/{loan}",
     *     summary="Actualizar préstamo (solo admin). Si se establece return_date por primera vez, se devuelve el libro y se aumenta el stock.",
     *     tags={"Loans"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="loan",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=3),
     *             @OA\Property(property="book_id", type="integer", example=11),
     *             @OA\Property(property="loan_date", type="string", format="date", nullable=true, example="2025-06-01"),
     *             @OA\Property(property="return_date", type="string", format="date", nullable=true, example="2025-06-15"),
     *             @OA\Property(property="status", type="string", example="aprobado")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Préstamo actualizado", @OA\JsonContent(ref="#/components/schemas/Loan")),
     *     @OA\Response(response=403, description="No autorizado"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function update(Request $request, Loan $loan)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'book_id' => 'sometimes|exists:books,id',
            'loan_date' => 'sometimes|date',
            'due_date' => 'sometimes|date',
            'return_date' => 'sometimes|date',
            'status' => 'sometimes|string',
        ]);

        $wasReturned = $loan->return_date !== null;

        $loan->update($validated);

        if (!$wasReturned && isset($validated['return_date'])) {
            $loan->book->incrementarStock();
        }

        return $loan;
    }

    /**
     * @OA\Delete(
     *     path="/api/loans/{loan}",
     *     summary="Delete loan (admin only)",
     *     tags={"Loans"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="loan",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Loan deleted"),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(Loan $loan)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $loan->delete();
        return response()->noContent();
    }

    /**
     * @OA\Post(
     *     path="/api/loans/{id}/return",
     *     summary="Marcar préstamo como devuelto (solo admin)",
     *     tags={"Loans"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del préstamo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Préstamo marcado como devuelto",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Préstamo marcado como devuelto.")
     *         )
     *     ),
     *     @OA\Response(response=403, description="No autorizado"),
     *     @OA\Response(response=404, description="Préstamo no encontrado o ya devuelto")
     * )
     */
    public function markAsReturned($id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $loan = Loan::with('book')->find($id);

        if (!$loan || $loan->return_date) {
            return response()->json(['message' => 'Préstamo no encontrado o ya devuelto'], 404);
        }

        $loan->return_date = now();
        $loan->save();

        // Cuando devuelce se aumenta el stock
        $loan->book->incrementarStock();

        return response()->json(['message' => 'Préstamo marcado como devuelto.']);
    }
}
