<?php

/**
 * Contrôleur pour les livres
 */
class BookController
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

        $books = $this->repository->getAllBooks();
        
        $title = "Livres disponibles";
        $mediaType = "books";
        
        include 'views/layouts/header.php';
        include 'views/books/index.php';
        include 'views/layouts/footer.php';
    }

    private function handleAction()
    {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        if (!isset($_POST['action']) || !isset($_POST['media_id'])) {
            header('Location: /books');
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
        }

        header('Location: /books');
        exit();
    }
}