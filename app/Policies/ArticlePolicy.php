<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    /**
     * Can the user view this article (and its revisions list)?
     */
    public function view(User $user, Article $article): bool
    {
        return $user->id === $article->user_id || $user->is_admin;
    }

    /**
     * Can the user update the article?
     */
    public function update(User $user, Article $article): bool
    {
        return $user->id === $article->user_id || $user->is_admin;
    }

    /**
     * Can the user delete this article?
     */
    public function delete(User $user, Article $article): bool
    {
        return $user->id === $article->user_id || $user->is_admin;
    }


    //   Can the user view revisions of the article?
    public function viewRevisions(User $user, Article $article): bool
    {
        return $user->id === $article->user_id || $user->is_admin;
    }

    //  Can the user update or revert the article?
    public function revertRevisions(User $user, Article $article): bool
    {
        return $user->id === $article->user_id || $user->is_admin;
    }
}
