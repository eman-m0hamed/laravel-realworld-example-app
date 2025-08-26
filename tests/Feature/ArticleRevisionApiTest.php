<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleRevision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleRevisionApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper method to authenticate a user and return auth headers.
     *
     * @param  User|null  $user
     * @return array
     */


    protected function authenticate(User $user = null): array
    {
        $user = $user ?: User::factory()->create();
        $token = auth()->login($user);

        return ['Authorization' => 'Bearer ' . $token];
    }


    /**
     * Test that revision record is created on updating article.
     *
     * @return void
     */
    public function test_revision_is_created_on_article_update()
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->create();

        $headers = $this->authenticate($user);

        $response = $this->withHeaders($headers)
            ->putJson("/api/articles/{$article->slug}", [
                'article' => [
                    'title' => 'Updated Title',
                    'body'  => 'Updated body text',
                ]
            ])->assertStatus(200);

        $this->assertDatabaseHas('article_revisions', [
            'article_id' => $article->id,
            'title' => $article->title, // old title
        ]);
    }

    /**
     * Test that an authenticated user can list all revisions of their article.
     *
     * @return void
     */
    public function test_list_article_revisions()
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->create();
        ArticleRevision::factory()->count(2)->for($article)->create();

        $headers = $this->authenticate($user);

        $response = $this->withHeaders($headers)
            ->getJson("/api/articles/{$article->slug}/revisions");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'status',
                'message',
                'data' => [[
                    'id',
                    'title',
                    'description',
                    'createdAt',
                    'tags',
                    'author'
                ]]
            ]);
    }

    /**
     * Test that an authenticated user can view a single revision of their article.
     *
     * @return void
     */
    public function test_show_single_revision()
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->create();
        $revision = ArticleRevision::factory()->for($article)->create();

        $headers = $this->authenticate($user);

        $response = $this->withHeaders($headers)
            ->getJson("/api/articles/{$article->slug}/revisions/{$revision->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $revision->id]);
    }

    /**
     * Test that an authenticated user can revert an article
     * to the content of a given revision.
     *
     * @return void
     */
    public function test_revert_to_revision()
    {
        $user = User::factory()->create();

        $article = Article::factory()->for($user)->create([
            'title' => 'Original',
            'body' => 'Original body',
        ]);

        $revision = ArticleRevision::factory()->for($article)->create([
            'title' => 'Revision Title',
            'body' => 'Revision body',
        ]);

        $headers = $this->authenticate($user);

        $response = $this->withHeaders($headers)
            ->putJson("/api/articles/{$article->slug}/revisions/{$revision->id}/revert");

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Revision Title', 'body' => 'Revision body']);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Revision Title',
            'body' => 'Revision body',
        ]);

        // Check if previous version was saved as a new revision

        $this->assertDatabaseHas('article_revisions', [
            'article_id' => $article->id,
            'title' => 'Original',
            'body' => 'Original body',
        ]);
    }

    /**
     * Test that a user cannot access another user's article revisions.
     *
     * @return void
     */
    public function test_unauthorized_user_cannot_access_revisions()
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->create();
        $otherUser = User::factory()->create();
        ArticleRevision::factory()->for($article)->create();

        $headers = $this->authenticate($otherUser);

        $response = $this->withHeaders($headers)
            ->getJson("/api/articles/{$article->slug}/revisions");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Forbidden',
                "errors" => [],
                "status" => 403
            ]);
    }

    /**
     * Test that a guest (not logged in) cannot access revisions.
     *
     * @return void
     */
    public function test_guest_cannot_access_revisions()
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->create();

        $response = $this->getJson("/api/articles/{$article->slug}/revisions");

        $response->assertStatus(401);
    }

    /**
     * Test that accessing a non-existing revision returns a 404 response.
     *
     * @return void
     */
    public function test_show_non_existing_revision_returns_404()
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->create();

        $headers = $this->authenticate($user);

        $response = $this->withHeaders($headers)
            ->getJson("/api/articles/{$article->slug}/revisions/999999");

        $response->assertStatus(status: 404);
    }
}
