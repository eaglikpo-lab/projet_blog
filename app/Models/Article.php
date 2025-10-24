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
    ];

    // Un article appartient à une catégorie
    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    #[Scope]
    /**
     * Summary of forCategory
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $categoryId
     * @return void
     */
    protected function forCategory(Builder $query, $categoryId)
    {
        $query->where("categorie_id", "=", $categoryId);
    }
}
