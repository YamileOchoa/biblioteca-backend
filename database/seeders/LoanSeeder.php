<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Loan;
use Carbon\Carbon;

class LoanSeeder extends Seeder
{
    public function run()
    {
        Loan::create([
            'user_id' => 1,
            'book_id' => 1,
            'status' => 'pendiente',
            'loan_date' => Carbon::now(),
        ]);
    }
}
