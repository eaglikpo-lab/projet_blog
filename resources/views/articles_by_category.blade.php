<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Articles</title>
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">

</head>
<body style="color: black;">
        <h1>Articles de la catégorie : {{ $category->name }}</h1>
        @if($articles->isEmpty())
            <p class="no-articles">Aucun article disponible pour le moment.</p>
        @else
            <div class="articles-grid">
                @foreach ($articles as $article)
                    <div class="article-card">
                        @if($article->image)
                            <div class="article-image">
                                <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}">
                            </div>
                        @endif

                        <h2>
                            <a href="{{ route('articles.show', $article->id) }}">
                                {{ $article->title }}
                            </a>
                        </h2>
                        <p>{{ Str::limit($article->content, 150) }}</p>
                        <a href="{{ route('articles.show', $article->id) }}" class="btn btn-primary">Lire plus</a>
                        <small>Publié le {{ $article->created_at->format('d/m/Y à H:i') }}</small>
                        <br>
                    </div>
                @endforeach
            </div>
        @endif
           
    {{ $articles->links() }}
</body>
</html>