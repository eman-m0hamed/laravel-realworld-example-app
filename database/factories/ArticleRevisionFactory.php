<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleRevision;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleRevisionFactory extends Factory
{
    protected $model = ArticleRevision::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'article_id' => Article::inRandomOrder()->first()->id,
            'title'        => $this->faker->sentence(),
            'description'  => $this->faker->paragraph(),
            'body'         => $this->faker->paragraphs(3, true),
        ];
    }
}
