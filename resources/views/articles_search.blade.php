<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">
</head>
<body>

    <h1>TEST — Vue articles-search.blade.php</h1>


    @if(isset($query))
        <h2>Résultats pour : "{{ $query }}"</h2>
    @endif

    @if($articles->count() > 0)
        <div class="articles-grid">
            @foreach ($articles as $article)
                <div class="article-card">
                    @if($article->image)
                        <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}">
                    @endif
                    <h2>{{ $article->title }}</h2>
                    <p>{{ Str::limit($article->content, 100) }}</p>
                    <a href="{{ route('articles.show', $article->id) }}" class="btn btn-primary">Lire plus</a>
                </div>
            @endforeach
        </div>

        {{ $articles->links() }}
    @else
        <p>Aucun article trouvé.</p>
    @endif
</body>
</html>