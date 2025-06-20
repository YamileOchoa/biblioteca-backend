<?php

namespace App\Http\Service;

use App\Models\Loan;
use App\Models\Book;

class LoanService
{
    public function listLoansForUser($user)
    {
        return $user->role === 'admin'
            ? Loan::with(['user', 'book'])->get()
            : Loan::with('book')->where('user_id', $user->id)->get();
    }

    public function createLoan($user, array $data)
    {
        $tienePrestamo = Loan::where('user_id', $user->id)
            ->whereIn('status', ['pendiente', 'aprobado'])
            ->whereNull('return_date')
            ->exists();

        if ($tienePrestamo) {
            throw new \Exception('No puedes solicitar un nuevo préstamo hasta devolver el anterior.');
        }

        $book = Book::findOrFail($data['book_id']);
        $book->disminuirStock();

        return Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => now(),
            'due_date' => now()->addDays(15),
            'return_date' => null,
            'status' => 'aprobado',
        ]);
    }

    public function markAsReturned(Loan $loan)
    {
        if ($loan->return_date) {
            throw new \Exception('El préstamo ya ha sido devuelto.');
        }

        $loan->update(['return_date' => now()]);
        $loan->book->incrementarStock();

        return $loan;
    }
}
