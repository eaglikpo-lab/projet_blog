<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Article;
use App\Models\Categorie;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
   
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Création d’un admin et d’un user normal
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user  = User::factory()->create(['role' => 'user']);
    }

    /** @test */
    public function an_admin_can_add_an_article()
    {
        // Créer une catégorie
        $categorie = Categorie::factory()->create();
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);

        // Authentifier comme admin
        Sanctum::actingAs($this->admin);

        // Envoi de la requête POST
        $response = $this->postJson('/api/admin/articles', [
            'titre' => 'Nouvel Article',
            'contenu' => 'Contenu de test',
            'categorie_id' => $categorie->id,
            'mots_cles' => 'laravel, api, test',
            'image' => $file, // vrai fichier simulé
        ]);

        // Vérifications
        $response->assertStatus(201)->assertJsonFragment(['title' => 'Nouvel Article']);

        $article = Article::firstOrFail();  // récupèration de l’article écrit en BDD

        $this->assertDatabaseHas('articles', ['titre' => 'Nouvel Article',
            'id' => $article->id,
            'categorie_id' => $categorie->id,]);
    }

    /** @test */
    public function a_normal_user_cannot_add_article()
    {
        $categorie = Categorie::factory()->create();

        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/admin/articles', [
            'titre' => 'Test non autorisé',
            'contenu' => 'Contenu',
            'categorie_id' => $categorie->id,
        ]);

        $response->assertStatus(403);
    }


    /** @test */
    public function an_unauthenticated_user_cannot_add_article()
    {
        $categorie = Categorie::factory()->create();

        $response = $this->postJson('/api/admin/articles/', [
            'titre' => 'Sans Auth',
            'contenu' => 'Pas de token',
            'categorie_id' => $categorie->id,
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function an_admin_can_update_article()
    {
        $categorie = Categorie::factory()->create();
        $article = Article::factory()->create(['categorie_id' => $categorie->id]);
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);

        Sanctum::actingAs($this->admin);

        $updateData = [
            'titre' => 'Titre modifié',
            'contenu' => 'Contenu mis à jour',
            'image' => $file,
            'mots_cles' => 'update, test',
            'categorie_id' => $categorie->id,
        ];

        $response = $this->putJson("/api/admin/articles/{$article->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Titre modifié']);

        $this->assertDatabaseHas('articles', ['titre' => 'Titre modifié']);
    }

    /** @test  */
    public function a_normal_user_cannot_update_article()
    {
        $categorie = Categorie::factory()->create();
        $article = Article::factory()->create(['categorie_id' => $categorie->id]);
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);

        Sanctum::actingAs($this->user);

        $updateData = [
            'titre' => 'Titre modifié',
            'contenu' => 'Contenu mis à jour',
            'image' => $file,
            'mots_cles' => 'update, test',
            'categorie_id' => $categorie->id,
        ];

        $response = $this->putJson("/api/admin/articles/{$article->id}", $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_update_article()
    {
        $categorie = Categorie::factory()->create();
        $article = Article::factory()->create(['categorie_id' => $categorie->id]);
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);


        $updateData = [
            'titre' => 'Titre modifié',
            'contenu' => 'Contenu mis à jour',
            'image' => $file,
            'mots_cles' => 'update, test',
            'categorie_id' => $categorie->id,
        ];

        $response = $this->putJson("/api/admin/articles/{$article->id}", $updateData);

        $response->assertStatus(401);
    }

   
    /** @test */
    public function an_admin_can_delete_article()
    {
        $categorie = Categorie::factory()->create();
        $article = Article::factory()->create(['categorie_id' => $categorie->id]);

        Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/admin/articles/{$article->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    /** @test  */
    public function a_normal_user_cannot_delete_article()
    {
        $categorie = Categorie::factory()->create();
        $article = Article::factory()->create(['categorie_id' => $categorie->id]);

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson("/api/admin/articles/{$article->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_delete_article()
    {
        $categorie = Categorie::factory()->create();
        $article = Article::factory()->create(['categorie_id' => $categorie->id]);

        $response = $this->deleteJson("/api/admin/articles/{$article->id}");
        $response->assertStatus(401);
    }


    /** @test */
    public function an_unauthenticated_cannot_list_article()
    {
        $categorie = Categorie::factory()->create();
        Article::factory(5)->create(['categorie_id' => $categorie->id]);

        $response = $this->getJson('/api/articles');
            
        $response->assertStatus(401);
    }


    /** @test */
    public function an_unauthenticated_cannot_list_categorie()
    {
        $categorie = Categorie::factory()->create();
        Article::factory(5)->create(['categorie_id' => $categorie->id]);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(401);
    }

    /** @test */
    public function an_admin_can_access_admin_dashboard()
    {
        $response = $this->actingAs($this->admin, 'sanctum')->get('/api/admin/dashboard');
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Bienvenue sur le tableau de bord admin ✅']);
    }

    /** @test */
    public function an_normal_user_cannot_access_admin_dashboard()
    {
        $response = $this->actingAs($this->user, 'sanctum')->get('/api/admin/dashboard');
        $response->assertStatus(403);
    }

    /** @test */
    public function an_normal_user_can_access_dashboard()
    {
        $response = $this->actingAs($this->user, 'sanctum')->get('/api/dashboard');
        $response->assertStatus(200);
    }

    /** @test */
    public function an_unauthenficated_cannot_access_dashboard()
    {
        $response = $this->getJson('/api/dashboard');
        $response->assertStatus(401);
    }
}
