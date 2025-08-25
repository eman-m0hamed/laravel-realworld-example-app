<?php

use App\Models\ArticleRevision;
use App\Models\Tag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('revision_tag', function (Blueprint $table) {
            $table->foreignIdFor(ArticleRevision::class, 'article_revision_id')->constrained()->onDelete('cascade');

            $table->foreignIdFor(Tag::class)->constrained()->onDelete('cascade');

            $table->index(['article_revision_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('revision_tag');
    }
};
