<!DOCTYPE html>
<html>
<head>
    <title>Créer un article</title>
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">
</head>
<body>
    <h1>Créer un nouvel article</h1>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="create-form" action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label for="category">Catégorie :</label>
        <select name="category_id" id="category" required>
            <option value="">-- Sélectionnez --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>

        <label for="keywords">Mots-clés</label>
        <input type="text" name="keywords" id="keywords" placeholder="King, empire, story" value="{{ old('keywords', $article->keywords ?? '') }}">

        <label for="title">Titre</label>
        <input type="text" name="title" id="title" required placeholder="Ex: Beauty in the best">

        <label for="content">Contenu</label>
        <textarea name="content" id="content" rows="5" required placeholder="In a long far away..."></textarea>

        <label for="image">Image de l'article</label>
        <input type="file" name="image" id="image" accept="image/*">

        <button type="submit">Enregistrer</button>
    </form>

    <a href="{{ url('/') }}">Retour à la liste</a>
</body>
</html>
