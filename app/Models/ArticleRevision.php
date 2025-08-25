<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleRevision extends Model
{

    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
        'title',
        'description',
        'body',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'revision_tag' , 'article_revision_id' );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
