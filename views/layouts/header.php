<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Médiathèque Moderne</title>
    <link rel="icon" type="image/png" href="data:image/png;base64,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">
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="header-content">
                <h1>Médiathèque</h1>
            </div>
            
            <div class="auth-section">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <div class="user-menu">
                        <span class="welcome-text">Bonjour, <?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                        <a href="/logout" class="auth-link logout">Déconnexion</a>
                    </div>
                <?php else: ?>
                    <div class="auth-links">
                        <a href="/login" class="auth-link">Connexion</a>
                        <a href="/register" class="auth-link register">Inscription</a>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <nav class="nav-container">
            <div class="nav-tabs">
                <a href="/home" class="nav-tab <?= $page == 'home' ? 'active' : '' ?>">
                    Accueil
                </a>
                <a href="/books" class="nav-tab <?= $page == 'books' ? 'active' : '' ?>">
                    Livres
                </a>
                <a href="/albums" class="nav-tab <?= $page == 'albums' ? 'active' : '' ?>">
                    Albums
                </a>
                <a href="/movies" class="nav-tab <?= $page == 'movies' ? 'active' : '' ?>">
                    Films
                </a>
            </div>
        </nav>

        <main class="main-content">