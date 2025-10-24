<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Article;
use App\Models\Categorie;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_articles()
    {
        $categorie = Categorie::factory()->create();
        Article::factory(5)->create(['categorie_id' => $categorie->id]);

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'image',
                        'keywords',
                        'categorie',
                        'created_at',
                        'created_at_formatted',
                        'updated_at',
                        'updated_at_formatted'
                    ]
                ],
                "first_page_url",
                "from",
                "last_page",
                "last_page_url",
                "links" => [
                    [
                        "url",
                        "label",
                        "page",
                        "active"

                    ],
                    [
                        "url",
                        "label",
                        "page",
                        "active"
                    ],
                    [
                        "url",
                        "label",
                        "page",
                        "active"
                    ]
                ],
                "next_page_url",
                "path",
                "per_page" ,
                "prev_page_url",
                "to",
                "total",
            ]);
    }

    /** @test */
    public function it_can_create_an_article()
    {
        $categorie = Categorie::factory()->create();
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);

        // $data = [
        //     'titre' => 'Article de test',
        //     'contenu' => 'Ceci est un article de test.',
        //     'image' => 'test.jpg',
        //     'mots_cles' => 'laravel, api, test',
        //     'categorie_id' => $categorie->id,
        // ];
       // $response = $this->postJson('/api/articles', $data);

        $response = $this->post('/api/articles', [
            'titre' => 'Article de test',
            'contenu' => 'Ceci est un article de test.',
            'image' => $file, // vrai fichier simulé
            'mots_cles' => 'laravel, api, test',
            'categorie_id' => $categorie->id,
        ], ['Accept' => 'application/json']);

        $response->assertCreated()->assertJsonPath('title', 'Article de test');

        $response->assertStatus(201)->assertJsonFragment(['title' => 'Article de test']);


        $article = Article::firstOrFail();  // récupèration de l’article écrit en BDD

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'titre' => 'Article de test',
            'categorie_id' => $categorie->id,
        ]);

        // le chemin stocké (relatif) existe sur le disk 'public'
        //Storage::disk('public')->assertExists($article->image);
    }

    /** @test */
    public function it_can_update_an_article()
    {
        $categorie = Categorie::factory()->create();
        $article = Article::factory()->create(['categorie_id' => $categorie->id]);
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);

        $updateData = [
            'titre' => 'Article modifié',
            'contenu' => 'Contenu mis à jour',
            'image' => $file,
            'mots_cles' => 'update, test',
            'categorie_id' => $categorie->id,
        ];

        $response = $this->putJson("/api/articles/{$article->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Article modifié']);

        $this->assertDatabaseHas('articles', ['titre' => 'Article modifié']);
    }

    
    /**
     * Summary of it_can_filter_by_categorie
     * @test
     */
    public function it_can_filter_by_categorie()
    {
        $categorie1 = Categorie::factory()->create();
        $categorie2 = Categorie::factory()->create();

        Article::factory()->count(2)->create(['categorie_id' => $categorie1->id]);
        Article::factory()->count(3)->create(['categorie_id' => $categorie2->id]);

        $response = $this->getJson("/api/articles?categorie_id={$categorie1->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data'); // Vérifie qu'on a bien 2 articles
    }

    /** @test */
    public function it_can_delete_an_article()
    {
        $categorie = Categorie::factory()->create();
        $article = Article::factory()->create(['categorie_id' => $categorie->id]);

        $response = $this->deleteJson("/api/articles/{$article->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

}
