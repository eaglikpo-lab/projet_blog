<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l’article</title>
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">
</head>
<body>
    <div class="container">
        <h1>Modifier l’article</h1>

        <form action="{{ route('articles.update', $article->id, $page) }}" method="POST" class="create-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <label for="category">Catégorie :</label>
            <select name="category_id" id="category" required>
                <option value="">-- Sélectionnez --</option>

                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <label for="keywords">Mots-clés </label>
            <input type="text" name="keywords" id="keywords" placeholder="Laravel, PHP, MVC" value="{{ old('keywords', $article->keywords ?? '') }}">

            <label for="title">Titre </label>
            <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" required>

            <label for="content">Contenu </label>
            <textarea name="content" id="content" rows="5" required>{{ old('content', $article->content) }}</textarea>

            <label for="image">Image de l'article</label>
            <input type="file" name="image" id="image" accept="image/*">


            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>

        <br>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">⬅ Annuler</a>
    </div>
</body>
</html>
