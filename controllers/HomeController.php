<?php

/**
 * Contrôleur pour la page d'accueil
 */
class HomeController
{
    private $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAction();
            return;
        }

        $allMedias = $this->repository->getAllMedias();
        shuffle($allMedias);

        $totalCount = count($allMedias);
        
        $booksCount = count(array_filter($allMedias, fn($media) => $media['type_media'] === 'book'));
        $albumsCount = count(array_filter($allMedias, fn($media) => $media['type_media'] === 'album'));
        $moviesCount = count(array_filter($allMedias, fn($media) => $media['type_media'] === 'movie'));
        
        include 'views/layouts/header.php';
        include 'views/home/index.php';
        include 'views/layouts/footer.php';
    }

    private function handleAction()
    {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        if (!isset($_POST['action']) || !isset($_POST['media_id'])) {
            header('Location: /home');
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
            error_log("Erreur dans HomeController: " . $e->getMessage());
        }

        header('Location: /home');
        exit();
    }
}