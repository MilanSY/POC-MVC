<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - Médiathèque Moderne</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
    </style>
</head>
<body>
    <div class="container">
        <div class="empty-state" style="margin-top: 10vh; text-align: center;">
            <div class="empty-state-icon" style="font-size: 8rem;">🔍</div>
            <h1 style="font-size: 3rem; margin-bottom: 1rem; color: var(--color-text-primary);">
                404 - Page non trouvée
            </h1>
            <p style="font-size: 1.2rem; color: var(--color-text-secondary); margin-bottom: 2rem;">
                Oops ! La page que vous cherchez n'existe pas ou a été déplacée.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="/home" class="btn btn-primary">
                    Retour à l'accueil
                </a>
                <a href="/books" class="btn btn-secondary">
                    Voir les livres
                </a>
                <a href="/albums" class="btn btn-secondary">
                    Voir les albums
                </a>
                <a href="/movies" class="btn btn-secondary">
                    Voir les films
                </a>
            </div>
        </div>
    </div>
</body>
</html>