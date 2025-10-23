<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
