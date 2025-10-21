<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $article->title }}</title>
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">
</head>
<body>
    <div class="container">
        @if($article->category)
            <p class="category">Catégorie : {{ $article->category->name }}</p>
        @endif
        
        <h1>{{ $article->title }}</h1>
        <p>Mots clé: {{$article->keywords}}</p>

        @if ($article->image)
            <div class="show-article-image">
                <img src="{{ asset('storage/' . $article->image) }}" alt="Image de l’article">
            </div>
        @else
            <div class="article-image">
                <img src="{{ asset('images/default.jpg') }}" alt="Image par défaut" class="article-image">
            </div>
        @endif
        
        <p>{{ $article->content }}</p>
        <small>Publié le {{ $article->created_at->format('d/m/Y H:i') }}</small>

        <div class="actions">

            <a href="{{ route('articles.index', ['page' => $page]) }}" class="btn btn-secondary">⬅ Retour</a>

            <a href="{{ route('articles.edit', $article) }}" class="btn btn-warning"> Modifier</a>
        </div>
    </div>
</body>
</html>
