<?php

namespace App\Http\Service;

use App\Models\Book;
use Illuminate\Support\Facades\Storage;

class BookService
{
    public function list(): \Illuminate\Support\Collection
    {
        return Book::with(['author', 'category'])->get()->map(function ($book) {
            if ($book->cover_image) {
                $book->cover_image_url = asset('storage/' . $book->cover_image);
            }
            return $book;
        });
    }

    public function available(): \Illuminate\Support\Collection
    {
        return Book::where('stock', '>', 0)->with(['author', 'category'])->get()->map(function ($book) {
            if ($book->cover_image) {
                $book->cover_image_url = asset('storage/' . $book->cover_image);
            }
            return $book;
        });
    }

    public function create(array $data, $coverImageFile = null): Book
    {
        if ($coverImageFile) {
            $data['cover_image'] = $coverImageFile->store('covers', 'public');
        }

        return Book::create($data);
    }

    public function update(Book $book, array $data, $coverImageFile = null): Book
    {
        if ($coverImageFile) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }

            $data['cover_image'] = $coverImageFile->store('covers', 'public');
        }

        $book->update($data);
        return $book;
    }

    public function delete(Book $book): void
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();
    }

    public function get(Book $book): Book
    {
        $book->load(['author', 'category']);
        if ($book->cover_image) {
            $book->cover_image_url = asset('storage/' . $book->cover_image);
        }
        return $book;
    }
}
