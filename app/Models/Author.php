<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Book;

/**
 * @mixin IdeHelperAuthor
 */
class Author extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'country', 'bio'];

    public function books() {
        return $this->hasMany(Book::class);
    }
}