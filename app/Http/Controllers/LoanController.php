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
 *     @OA\Property(property="status", type="string", example="pendiente"),
 *     @OA\Property(property="admin_response_at", type="string", format="date-time", nullable=true, example="2025-06-04T12:00:00Z"),
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
        } else {
            return Loan::with('book')->where('user_id', $user->id)->get();
        }
    }

    /**
     * @OA\Post(
     *     path="/api/loans",
     *     summary="Create a new loan (admin only)",
     *     tags={"Loans"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "book_id", "loan_date"},
     *             @OA\Property(property="user_id", type="integer", example=3),
     *             @OA\Property(property="book_id", type="integer", example=11),
     *             @OA\Property(property="loan_date", type="string", format="date", example="2025-06-01"),
     *             @OA\Property(property="return_date", type="string", format="date", nullable=true, example="2025-06-15"),
     *             @OA\Property(property="status", type="string", example="pendiente")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Loan created", @OA\JsonContent(ref="#/components/schemas/Loan")),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'loan_date' => 'required|date',
            'return_date' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        return Loan::create($validated);
    }

    /**
     * @OA\Post(
     *     path="/api/loans/request",
     *     summary="Request a loan (user)",
     *     tags={"Loans"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"book_id"},
     *             @OA\Property(property="book_id", type="integer", example=11)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Solicitud enviada", @OA\JsonContent(ref="#/components/schemas/Loan")),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function requestLoan(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $loan = Loan::create([
            'user_id' => $user->id,
            'book_id' => $validated['book_id'],
            'status' => 'pendiente',
            'loan_date' => now(),
        ]);

        return response()->json([
            'message' => 'Solicitud enviada',
            'loan' => $loan
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/loans/{loan}/respond",
     *     summary="Admin approves or rejects loan request",
     *     tags={"Loans"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="loan",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"aprobado", "rechazado"}, example="aprobado"),
     *             @OA\Property(property="loan_date", type="string", format="date", nullable=true, example="2025-06-04"),
     *             @OA\Property(property="return_date", type="string", format="date", nullable=true, example="2025-06-20")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Respuesta registrada", @OA\JsonContent(ref="#/components/schemas/Loan")),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function respondToLoan(Request $request, Loan $loan)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:aprobado,rechazado',
            'loan_date' => 'nullable|date',
            'return_date' => 'nullable|date',
        ]);

        $loan->status = $validated['status'];
        $loan->admin_response_at = now();

        if ($validated['status'] === 'aprobado') {
            $loan->loan_date = $validated['loan_date'] ?? now();
            $loan->return_date = $validated['return_date'] ?? now()->addDays(15);
        }

        $loan->save();

        return response()->json([
            'message' => 'Respuesta registrada',
            'loan' => $loan
        ]);
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
     *     @OA\Response(
     *         response=200,
     *         description="Loan found",
     *         @OA\JsonContent(ref="#/components/schemas/Loan")
     *     ),
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
     *     summary="Update loan (admin only)",
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
     *             @OA\Property(property="status", type="string", example="pendiente")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Loan updated", @OA\JsonContent(ref="#/components/schemas/Loan")),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
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
            'return_date' => 'sometimes|date',
            'status' => 'sometimes|string',
        ]);

        $loan->update($validated);

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
}
