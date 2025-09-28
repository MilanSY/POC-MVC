<?php

/**
 * Contrôleur pour la modification des albums
 */
class EditAlbumController
{
    private $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * Afficher le formulaire de modification d'un album
     */
    public function index()
    {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header('Location: /login');
            exit();
        }

        $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
        $path = explode("/", $uri);
        
        if (!isset($path[2]) || empty($path[2]) || !is_numeric($path[2])) {
            header('Location: /albums');
            exit();
        }
        
        $albumId = (int)$path[2];
        $album = $this->repository->getMediaDetails($albumId);
        
        if (!$album || $album['type_media'] !== 'album') {
            header('Location: /albums');
            exit();
        }

        $oldInput = [
            'titre' => $album['titre'],
            'auteur' => $album['auteur'],
            'nb_tracks' => $album['track_number'],
            'editeur' => $album['editor']
        ];
        $errors = [];
        $isEdit = true;
        
        $albumObj = (object) $album;

        include 'views/layouts/header.php';
        include 'views/media/add-album.php';
        include 'views/layouts/footer.php';
    }
    
    /**
     * Traiter la modification de l'album
     */
    public function store()
    {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header('Location: /login');
            exit();
        }

        $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
        $path = explode("/", $uri);
        $albumId = (int)$path[2];

        $errors = [];
        $oldInput = $_POST;

        if (empty(trim($_POST['titre']))) {
            $errors['titre'] = 'Le titre est requis.';
        }

        if (empty(trim($_POST['auteur']))) {
            $errors['auteur'] = 'L\'artiste est requis.';
        }

        if (empty($_POST['nb_tracks']) || !is_numeric($_POST['nb_tracks']) || $_POST['nb_tracks'] < 1) {
            $errors['nb_tracks'] = 'Le nombre de pistes doit être un nombre positif.';
        }

        if (empty(trim($_POST['editeur']))) {
            $errors['editeur'] = 'L\'éditeur est requis.';
        }

        if (empty($errors)) {
            $success = $this->repository->updateAlbum(
                $albumId,
                trim($_POST['titre']),
                trim($_POST['auteur']),
                (int)$_POST['nb_tracks'],
                trim($_POST['editeur'])
            );

            if ($success) {
                header('Location: /edit-songs/' . $albumId);
                exit();
            } else {
                $errors['general'] = 'Erreur lors de la modification de l\'album.';
            }
        }

        $album = $this->repository->getMediaDetails($albumId);
        $isEdit = true;
        $albumObj = (object) $album;

        include 'views/layouts/header.php';
        include 'views/media/add-album.php';
        include 'views/layouts/footer.php';
    }
}