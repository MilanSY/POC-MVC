<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Médiathèque Moderne</title>
    <link rel="stylesheet" href="assets/style.css">
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
                <h1>Médiathèque Moderne</h1>
                <p>Découvrez notre collection de livres, films et albums</p>
            </div>
            
            <div class="auth-section">
                <?php 
                require_once 'controllers/AuthController.php';
                $loggedUser = AuthController::getLoggedUser(); 
                ?>
                <?php if ($loggedUser): ?>
                    <div class="user-menu">
                        <span class="welcome-text">Bonjour, <?= htmlspecialchars($loggedUser['username']) ?></span>
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