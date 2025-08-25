<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\ArticleRevision;
use Illuminate\Support\Facades\Auth;

class ArticleObserver
{
    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        //
       $revision =  ArticleRevision::create([
            'article_id'    => $article->id,
            'user_id'       => Auth::id(),
            'title'         => $article->getOriginal('title'),
            'description'   => $article->getOriginal('description'),
            'body'          => $article->getOriginal('body'),
        ]);

        // Attach tags from the current article
        $revision->tags()->sync($article->tags()->pluck('id')->toArray());
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "restored" event.
     */
    public function restored(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "force deleted" event.
     */
    public function forceDeleted(Article $article): void
    {
        //
    }
}
