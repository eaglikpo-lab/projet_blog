<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Http\Controllers\ArticleController;

class Categorie extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
    ];

    // Une catégorie peut avoir plusieurs articles
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
