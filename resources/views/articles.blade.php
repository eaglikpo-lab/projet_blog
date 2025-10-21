<!-- resources/views/articles.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Articles</title>
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">

</head>
<body>
    <h1>📰 Liste des Articles</h1>

    <form action="{{ route('articles.search') }}" method="GET" class="search-form">
        <input type="text" name="query" placeholder="Rechercher un article..." value="{{ request('query') }}">
        <button type="submit" class="btn btn-primary">Rechercher</button>
    </form>


    <div class="categories-filter">
        <div class="display">Catégories: </div>
        <a href="{{ route('articles.index') }}">Toutes les catégories</a>
        @foreach(\App\Models\Category::all() as $cat)
            <a href="{{ route('articles.byCategory', $cat->id) }}">{{ $cat->name }}</a>
        @endforeach
    </div>


    <div class="CRUD">
        <a href="{{ route('articles.create') }}" class="btn btn-primary">Créer un article</a>
    </div>
   

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif


    <div class="articles-grid">
        @if($articles->isEmpty())
            <p class="no-articles">Aucun article disponible pour le moment.</p>
        @else
            @foreach($articles as $article)
                <div class="article-card">
                     {{-- Image de l'article --}}
                    @if ($article->image)
                        <div class="article-image">
                            <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="article-image">
                        </div>
                    @else
                        <div class="article-image">
                            <img src="{{ asset('images/default.jpg') }}" alt="Image par défaut" class="article-image">
                        </div>
                    @endif

                    <div class="article">
                        <h2>
                            <a href="{{ route('articles.show', ['article' => $article->id, 'page' => $articles->currentPage()]) }}">
                            {{ $article->title }}
                            </a>
                        </h2>
                        <p>Mots clé: {{$article->keywords}}</p>
                        <p>{{ Str::limit($article->content, 68, '...') }}</p>
                        <small>Publié le {{ $article->created_at->format('d/m/Y à H:i') }}</small>
                        <br>
                        <!-- <a href="{{ route('articles.show', ['article' => $article->id, 'page' => $articles->currentPage()]) }}">
                           {{ $article->title }}
                        </a> -->

                    </div>

                        <!-- <a href="{{ route('articles.edit', ['article' => $article->id, 'page' => $articles->currentPage()]) }}">
                           Modifier
                        </a> -->

                    <div class="actions">
                        <form action="{{ route('articles.edit', ['article' => $article->id, 'page' => $articles->currentPage()]) }}" method="GET" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Modifier
                            </button>
                        </form>

                        <form action="{{ route('articles.destroy', $article) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer cet article ?')">
                                Supprimer
                            </button>
                        </form>
                    </div>
                    
                </div>

            @endforeach
        @endif

    </div>


    <div class="pagination">
        {{ $articles->links() }}
    </div>


    <footer>
        <p>&copy; 2025 Mon Blog</p>
    </footer>
  

</body>
</html>
