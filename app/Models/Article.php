<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;



class Article extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'titre',
        'contenu',
        'image',
        'mots_cles',
        'categorie_id',
        'admin_id',
    ];

    /**
     * Un article appartient à une catégorie
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Categorie, Article>
     */
    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }


    /**
     * Un article appartient à un utilisateur (admin)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Article>
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    
    /**
     * un article a plusieurs commentaires
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Comment, Article>
     */
    public function comments()
    {
        return $this->hasMany(Comment::class); 
    }


    #[Scope]
    /**
     * Summary of forCategory
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $categoryId
     * @return void
     */
    // protected function forCategory(Builder $query, $categoryId)
    // {
    //     $query->where("categorie_id", "=", $categoryId);
    // }

    public function scopeForCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('categorie_id', $categoryId);
    }
}
