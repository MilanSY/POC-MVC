<?php

/**
 * Contrôleur pour les détails des livres
 */
class BookDetailsController
{
    private $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * Afficher les détails d'un livre
     */
    public function show()
    {
        // Récupérer l'ID du livre depuis l'URL
        $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
        $path = explode("/", $uri);
        
        if (!isset($path[2]) || empty($path[2]) || !is_numeric($path[2])) {
            $this->redirect404();
            return;
        }
        
        $bookId = (int)$path[2];
        $book = $this->repository->getMediaDetails($bookId);
        
        if (!$book || $book['type_media'] !== 'book') {
            $this->redirect404();
            return;
        }
        
        include 'views/layouts/header.php';
        include 'views/books/details.php';
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
            header('Location: /book-details/' . ($_POST['media_id'] ?? ''));
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
                    // Rediriger vers la page des livres après suppression
                    header('Location: /books');
                    exit();
                    
                case 'update':
                    if (isset($_POST['titre'], $_POST['auteur'], $_POST['page_number'])) {
                        $this->repository->updateBook(
                            $mediaId,
                            trim($_POST['titre']),
                            trim($_POST['auteur']),
                            (int)$_POST['page_number']
                        );
                    }
                    break;
            }
        } catch (Exception $e) {
            // En cas d'erreur, on redirige simplement
            error_log("Erreur dans BookDetailsController: " . $e->getMessage());
        }

        // Rediriger vers la même page
        header('Location: /book-details/' . $mediaId);
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