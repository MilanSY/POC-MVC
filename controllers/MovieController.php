<?php

/**
 * Contrôleur pour les films
 */
class MovieController
{
    private $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    public function index()
    {
        // Traiter les actions POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAction();
            return;
        }

        $movies = $this->repository->getAllMovies();
        
        $title = "Films disponibles";
        $mediaType = "movies";
        
        include 'views/layouts/header.php';
        include 'views/movies/index.php';
        include 'views/layouts/footer.php';
    }

    private function handleAction()
    {
        // Vérifier l'authentification
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        if (!isset($_POST['action']) || !isset($_POST['media_id'])) {
            header('Location: /movies');
            exit();
        }

        $action = $_POST['action'];
        $mediaId = (int)$_POST['media_id'];
        $userId = $_SESSION['user_id'];

        try {
            if ($action === 'borrow') {
                if ($this->repository->canBorrowMedia($mediaId)) {
                    $this->repository->emprunterMedia($mediaId, $userId);
                }
            } elseif ($action === 'return') {
                $this->repository->rendreMedia($mediaId, $userId);
            }
        } catch (Exception $e) {
            // En cas d'erreur, on redirige simplement
        }

        // Rediriger vers la même page
        header('Location: /movies');
        exit();
    }
}