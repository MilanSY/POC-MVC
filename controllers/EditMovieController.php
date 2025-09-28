<?php

/**
 * Contrôleur pour la modification des films
 */
class EditMovieController
{
    private $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * Afficher le formulaire de modification d'un film
     */
    public function index()
    {
        // Vérifier l'authentification
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header('Location: /login');
            exit();
        }

        // Récupérer l'ID du film depuis l'URL
        $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
        $path = explode("/", $uri);
        
        if (!isset($path[2]) || empty($path[2]) || !is_numeric($path[2])) {
            header('Location: /movies');
            exit();
        }
        
        $movieId = (int)$path[2];
        $movie = $this->repository->getMediaDetails($movieId);
        
        if (!$movie || $movie['type_media'] !== 'movie') {
            header('Location: /movies');
            exit();
        }

        // Préparer les données pour le formulaire (comme pour l'ajout)
        // Parser la durée string (ex: "2h 30min") vers heures et minutes
        $durationString = $movie['movie_duration'] ?? '';
        $hours = 0;
        $minutes = 0;
        
        // Parser la durée (format: "2h 30min" ou "150min" ou "2h")
        if (preg_match('/(\d+)h/', $durationString, $hourMatches)) {
            $hours = (int)$hourMatches[1];
        }
        if (preg_match('/(\d+)min/', $durationString, $minuteMatches)) {
            $minutes = (int)$minuteMatches[1];
        }
        
        $oldInput = [
            'titre' => $movie['titre'],
            'auteur' => $movie['auteur'],
            'duration_hours' => $hours,
            'duration_minutes' => $minutes,
            'genre' => $movie['genre']
        ];
        
        // Genres disponibles (même liste que dans AddMovieController)
        $genres = [
            'Action' => 'Action',
            'Comédie' => 'Comédie',
            'Drame' => 'Drame',
            'Science-Fiction' => 'Science-Fiction',
            'Horreur' => 'Horreur',
            'Documentaire' => 'Documentaire'
        ];
        
        $errors = [];
        $isEdit = true; // Flag pour indiquer que c'est une modification
        
        // Créer un objet movie pour l'action du formulaire
        $movieObj = (object) $movie;

        include 'views/layouts/header.php';
        include 'views/media/add-movie.php'; // Réutiliser la vue d'ajout
        include 'views/layouts/footer.php';
    }
    
    /**
     * Traiter la modification du film
     */
    public function store()
    {
        // Vérifier l'authentification
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header('Location: /login');
            exit();
        }

        // Récupérer l'ID du film
        $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
        $path = explode("/", $uri);
        $movieId = (int)$path[2];

        $errors = [];
        $oldInput = $_POST;

        // Validation
        if (empty(trim($_POST['titre']))) {
            $errors['titre'] = 'Le titre est requis.';
        }

        if (empty(trim($_POST['auteur']))) {
            $errors['auteur'] = 'Le réalisateur est requis.';
        }

        $hours = (int)($_POST['duration_hours'] ?? 0);
        $minutes = (int)($_POST['duration_minutes'] ?? 0);
        
        if ($hours <= 0 && $minutes <= 0) {
            $errors['duration'] = 'La durée doit être supérieure à 0.';
        }
        
        // Construire la string de durée dans le format attendu
        $durationString = '';
        if ($hours > 0) {
            $durationString .= $hours . 'h';
        }
        if ($minutes > 0) {
            if ($hours > 0) $durationString .= ' ';
            $durationString .= $minutes . 'min';
        }

        if (empty($_POST['genre'])) {
            $errors['genre'] = 'Le genre est requis.';
        }

        // Si pas d'erreur, mettre à jour
        if (empty($errors)) {
            $success = $this->repository->updateMovie(
                $movieId,
                trim($_POST['titre']),
                trim($_POST['auteur']),
                $durationString,
                $_POST['genre']
            );

            if ($success) {
                header('Location: /movie-details/' . $movieId);
                exit();
            } else {
                $errors['general'] = 'Erreur lors de la modification du film.';
            }
        }

        // Si erreur, réafficher le formulaire avec les erreurs
        $movie = $this->repository->getMediaDetails($movieId);
        $isEdit = true;
        $movieObj = (object) $movie;
        
        // Repasser les genres disponibles
        $genres = [
            'Action' => 'Action',
            'Comédie' => 'Comédie',
            'Drame' => 'Drame',
            'Science-Fiction' => 'Science-Fiction',
            'Horreur' => 'Horreur',
            'Documentaire' => 'Documentaire'
        ];

        include 'views/layouts/header.php';
        include 'views/media/add-movie.php';
        include 'views/layouts/footer.php';
    }
}