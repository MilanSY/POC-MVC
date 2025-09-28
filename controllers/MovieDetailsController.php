<?php

/**
 * Contrôleur pour les détails des films
 */
class MovieDetailsController
{
    private $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * Afficher les détails d'un film
     */
    public function show()
    {
        // Récupérer l'ID du film depuis l'URL
        $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
        $path = explode("/", $uri);
        
        if (!isset($path[2]) || empty($path[2]) || !is_numeric($path[2])) {
            $this->redirect404();
            return;
        }
        
        $movieId = (int)$path[2];
        $movie = $this->repository->getMediaDetails($movieId);
        
        if (!$movie || $movie['type_media'] !== 'movie') {
            $this->redirect404();
            return;
        }
        
        include 'views/layouts/header.php';
        include 'views/movies/details.php';
        include 'views/layouts/footer.php';
    }
    
    /**
     * Traiter les actions POST (emprunter, rendre, modifier, supprimer)
     */
    public function handleAction()
    {
        // Vérifier l'authentification pour toutes les actions
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        if (!isset($_POST['action']) || !isset($_POST['media_id'])) {
            header('Location: /movie-details/' . ($_POST['media_id'] ?? ''));
            exit();
        }

        $action = $_POST['action'];
        $mediaId = (int)$_POST['media_id'];
        $userId = $_SESSION['user_id'];

        try {
            switch ($action) {
                case 'borrow':
                    if ($this->repository->canBorrowMedia($mediaId)) {
                        $this->repository->emprunterMedia($mediaId, $userId);
                    }
                    break;
                    
                case 'return':
                    $this->repository->rendreMedia($mediaId, $userId);
                    break;
                    
                case 'delete':
                    $this->repository->deleteMedia($mediaId);
                    // Rediriger vers la page des films après suppression
                    header('Location: /movies');
                    exit();
                    
                case 'update':
                    if (isset($_POST['titre'], $_POST['auteur'], $_POST['duration'], $_POST['genre'])) {
                        $this->repository->updateMovie(
                            $mediaId,
                            trim($_POST['titre']),
                            trim($_POST['auteur']),
                            trim($_POST['duration']),
                            trim($_POST['genre'])
                        );
                    }
                    break;
            }
        } catch (Exception $e) {
            // En cas d'erreur, on redirige simplement
            error_log("Erreur dans MovieDetailsController: " . $e->getMessage());
        }

        // Rediriger vers la même page
        header('Location: /movie-details/' . $mediaId);
        exit();
    }
    
    /**
     * Rediriger vers la page 404
     */
    private function redirect404()
    {
        http_response_code(404);
        include 'views/errors/404.php';
    }
}