<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article; // ← à ne pas oublier

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        Article::factory()->create([
            'title' => 'Article 1',
            'content' => 'Contenu article 1',
        ]);

        Article::factory()->create([
            'title' => 'Article 2',
            'content' => 'Contenu article 2',
        ]);

        // Création automatique avec Faker
        Article::factory(5)->create();
    }
}
