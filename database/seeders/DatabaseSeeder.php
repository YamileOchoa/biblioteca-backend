<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{User, Author, Category, Book, Loan};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    $this->call(AdminUserSeeder::class);

    // autorres y caregorias
    $authors = Author::factory(5)->create();
    $categories = Category::factory(5)->create();

    // para poder crear libros y categorias
    $books = Book::factory(10)->create([
        'author_id' => $authors->random()->id,
        'category_id' => $categories->random()->id,
    ]);

    // crear prestamos
    Loan::factory(5)->create([
        'user_id' => 1,
        'book_id' => $books->random()->id,
    ]);
  }
}
