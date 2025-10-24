<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Categorie;
use App\Models\Article;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Article::class;
    public function definition(): array
    {
        return [
            //
            'titre' => $this->faker->sentence(),
            'contenu' => $this->faker->paragraph(),
            'image' => $this->faker->imageUrl(640, 480, 'articles', true),
            'mots_cles' => 'laravel, api, ' . $this->faker->word(),
            'categorie_id' => Categorie::factory(),
        ];
    }
}
