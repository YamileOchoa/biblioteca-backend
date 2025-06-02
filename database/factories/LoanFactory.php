<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'user_id' => \App\Models\User::factory(),
           'book_id' => \App\Models\Book::factory(),
           'loan_date' => $this->faker->date(),
           'return_date' => null,
           'status' => 'pendiente',
        ];
    }
}
