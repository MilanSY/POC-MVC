<?php

/**
 * Contrôleur pour les détail    private function handleAction()
    {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user_id'])) {s albums
 */
class AlbumDetailsController
{
    private $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * Afficher les détails d'un album
     */
    public function show()
    {
        $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
        $path = explode("/", $uri);
        
        if (!isset($path[2]) || empty($path[2]) || !is_numeric($path[2])) {
            $this->redirect404();
            return;
        }
        
        $albumId = (int)$path[2];
        $album = $this->repository->getMediaDetails($albumId);
        
        if (!$album || $album['type_media'] !== 'album') {
            $this->redirect404();
            return;
        }
        
        include 'views/layouts/header.php';
        include 'views/albums/details.php';
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
            header('Location: /album-details/' . ($_POST['media_id'] ?? ''));
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
                    header('Location: /albums');
                    exit();
                    
                case 'update':
                    if (isset($_POST['titre'], $_POST['auteur'], $_POST['track_number'], $_POST['editor'])) {
                        $this->repository->updateAlbum(
                            $mediaId,
                            trim($_POST['titre']),
                            trim($_POST['auteur']),
                            (int)$_POST['track_number'],
                            trim($_POST['editor'])
                        );
                    }
                    break;
            }
        } catch (Exception $e) {
            error_log("Erreur dans AlbumDetailsController: " . $e->getMessage());
        }

        header('Location: /album-details/' . $mediaId);
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