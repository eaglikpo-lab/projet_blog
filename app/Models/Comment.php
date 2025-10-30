<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    protected $fillable = [
        'user_id',
        'article_id',
        'parent_id',
        'contenu',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    // 🔹 Relation vers le commentaire parent (si c’est une réponse)
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // 🔹 Relation vers les réponses du commentaire
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
