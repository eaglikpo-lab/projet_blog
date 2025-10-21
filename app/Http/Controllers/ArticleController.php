<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;

use Illuminate\Http\Request;

class ArticleController extends Controller
{

    public function index()
    {
        // Pagination : 1 ligne par page
        // Ici, si chaque ligne contient 3 articles, on prend 3 par page
        $articlesPerRow = 3; 
        $articles = Article::paginate($articlesPerRow);

        return view('articles', compact('articles'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('articles_create', compact('categories'));
    }


    public function destroy(Article $article)
    {
        $article->delete(); // supprime l'article de la base
        return redirect()->route('articles.index')->with('success', 'Article supprimé avec succès !');
    }

    public function store(Request $request)
    {
        // validation du formulaire (fillable)
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'keywords' => 'nullable|string|max:255'
        ]);

        // Gestion du fichier image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public'); //storage/app/public/images
            $validated['image'] = $path;
        }

        // créer l'article
        Article::create($validated);

        // redirect back to the article list
        return redirect()->route('articles.index')->with('success', 'Article créé avec succès !');
    }

    public function show(Article $article, Request $request)
    {
        $page = $request->query('page', 1); // récupère la page actuelle (par défaut 1)
        // dd($page);
        return view('articles_show', compact('article', 'page'));
    }

    public function edit(Article $article, Request $request) {

        $page = $request->query('page', 1); // récupère la page actuelle (par défaut 1)
        //dd($page);
        
        $categories = Category::all();

        return view('articles_edit', compact('article', 'categories', 'page'));
    }

    public function update(Request $request, Article $article) {
        // Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // facultatif
            'category_id' => 'required|exists:categories,id',
            'keywords' => 'nullable|string|max:255',
        ]);

        // Si une nouvelle image est uploadée
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public'); // stocke dans /storage/app/public/images le .tmp 
            $validated['image'] = $path;
        }

        // Mise à jour de l'article
        $article->update($validated);

        // Redirection après modification
        return redirect()->route('articles.show', $article->id)->with('success', 'Article mis à jour avec succès !');
    }


    public function byCategory(Category $category) {
        // Récupère la catégorie et ses articles
        //$category = Category::findOrFail($id);
        //$articles = $category->articles()->latest()->paginate(3);
        
        $articles = Article::forCategory($category->id)->latest()->paginate(3);

        return view('articles_by_category', compact('category', 'articles'));
    }

    public function search(Request $request) {
        $query = $request->input('query');

        $articles = Article::where('title', 'LIKE', "%{$query}%")
            ->orWhere('content', 'LIKE', "%{$query}%")
            ->orWhere('keywords', 'LIKE', "%{$query}%") 
            ->paginate(3);

        return view('articles_search', compact('articles', 'query'));
    }


    public function back()
    {
        // Vérifie si une page précédente existe dans la session
        if (url()->previous()) {
            return redirect()->back();
        }

        // Si pas de page précédente (accès direct), on renvoie vers la liste
        return redirect()->route('articles.index');
    }


}


