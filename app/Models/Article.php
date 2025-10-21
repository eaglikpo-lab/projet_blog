<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Article extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content','image','category_id','keywords',]; 

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    #[Scope]
    protected function forCategory(Builder $query, int $categoryId): void {
        $query->where("category_id", "=", $categoryId);
    }
}
