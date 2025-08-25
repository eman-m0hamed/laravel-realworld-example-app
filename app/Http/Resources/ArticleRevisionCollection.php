<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleRevisionCollection extends ResourceCollection
{
    public static $wrap = '';

    public function toArray($request): array
    {
        return $this->collection->map(function ($revision) {
            return [
                'id'          => $revision->id,
                'title'       => $revision->title,
                'description' => $revision->description,
                'tags' => $revision->tags->pluck('name'),
                'createdAt' => $revision->created_at,
                'author' => [
                    'username' => $revision->user->username,
                ]
            ];
        })->all();
    }
}
