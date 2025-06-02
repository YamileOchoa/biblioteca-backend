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
 *     required={"id", "user_id", "book_id", "loan_date"},
 *     @OA\Property(property="id", type="integer", example=5),
 *     @OA\Property(property="user_id", type="integer", example=3),
 *     @OA\Property(property="book_id", type="integer", example=11),
 *     @OA\Property(property="loan_date", type="string", format="date", example="2025-06-01"),
 *     @OA\Property(property="return_date", type="string", format="date", nullable=true, example="2025-06-15"),
 * )
 */
class LoanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/loans",
     *     summary="List all loans",
     *     tags={"Loans"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Loan"))
     *     )
     * )
     */
    public function index()
    {
        return Loan::with(['user', 'book'])->get();
    }

    /**
     * @OA\Post(
     *     path="/api/loans",
     *     summary="Create a new loan",
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
        ]);

        return Loan::create($validated);
    }

    /**
     * @OA\Get(
     *     path="/api/loans/{loan}",
     *     summary="Get a loan by ID",
     *     tags={"Loans"},
     *     @OA\Parameter(
     *         name="loan",
     *         in="path",
     *         description="Loan ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/Loan")),
     *     @OA\Response(response=404, description="Loan not found")
     * )
     */
    public function show(Loan $loan)
    {
        return $loan->load(['user', 'book']);
    }

    /**
     * @OA\Put(
     *     path="/api/loans/{loan}",
     *     summary="Update a loan",
     *     tags={"Loans"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="loan",
     *         in="path",
     *         description="Loan ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=3),
     *             @OA\Property(property="book_id", type="integer", example=11),
     *             @OA\Property(property="loan_date", type="string", format="date", example="2025-06-01"),
     *             @OA\Property(property="return_date", type="string", format="date", nullable=true, example="2025-06-15"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Loan updated", @OA\JsonContent(ref="#/components/schemas/Loan")),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=404, description="Loan not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, Loan $loan)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $loan->update($request->all());
        return $loan;
    }

    /**
     * @OA\Delete(
     *     path="/api/loans/{loan}",
     *     summary="Delete a loan",
     *     tags={"Loans"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="loan",
     *         in="path",
     *         description="Loan ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Loan deleted"),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=404, description="Loan not found")
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
