<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleRevisionCollection;
use App\Http\Resources\ArticleRevisionResource;
use App\Models\Article;
use App\Models\ArticleRevision;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;


/**
 * Controller for managing article revisions.
 *
 * Provides endpoints to list, show, and revert article revisions.
 * Uses route model binding for cleaner code and better maintainability.
 */
class ArticleRevisionController extends Controller
{
    use ApiResponseTrait;

    /**
     * List all revisions for an article by article id.
     *
     * @param  Article  $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Article $article)
    {
        $this->authorize('viewRevisions', $article);
        $revisions = $article->revisions->load('user', 'tags');
        return $this->successResponse(new ArticleRevisionCollection($revisions));
    }

    /**
     * Show a specific revision of an article by article id and revision id.
     *
     * @param  Article  $article
     * @param  ArticleRevision  $revision
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * Show a specific revision of an article by article id and revision id.
     *
     * @param  int  $articleId
     * @param  int  $revisionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Article $article, ArticleRevision $revision)
    {
        $this->authorize('viewRevisions', $article);
        if ($revision->article_id !== $article->id) {
            abort(404, 'Revision not found for this article.');
        }
        return $this->successResponse(ArticleRevisionResource::make($revision->load('user', 'tags')));
    }

    /**
     * Revert an article to a specific revision by article id and revision id.
     *
     * @param  Article  $article
     * @param  ArticleRevision  $revision
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Revert an article to a specific revision by article id and revision id.
     *
     * @param  Article  $article
     * @param  ArticleRevision  $revision
     * @return \Illuminate\Http\JsonResponse
     */
    public function revert(Article $article, ArticleRevision $revision)
    {
        $this->authorize('revertRevisions', $article);
        if ($revision->article_id !== $article->id) {
            abort(404, 'Revision not found for this article.');
        }
        $article->update([
            'title' => $revision->title,
            'description' => $revision->description,
            'body' => $revision->body,
        ]);

        $article->tags()->sync($revision->tags()->pluck('id')->toArray());

        return $this->successResponse($article, 'Article reverted successfully');
    }
}
