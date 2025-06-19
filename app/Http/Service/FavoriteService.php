<?php

namespace App\Services;

use App\Models\User;

class FavoriteService
{
    public function listFavorites(User $user)
    {
        return $user->favoriteBooks()->get();
    }

    public function addToFavorites(User $user, int $bookId): void
    {
        $user->favoriteBooks()->syncWithoutDetaching([$bookId]);
    }

    public function removeFromFavorites(User $user, int $bookId): void
    {
        $user->favoriteBooks()->detach($bookId);
    }
}
