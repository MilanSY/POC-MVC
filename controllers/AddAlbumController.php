<?php

/**
 * Contrôleur pour l'ajout d'albums
 */
class AddAlbumController
{
    private $repository;
    private $errors = [];
    private $oldInput = [];

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    /**
     * Afficher le formulaire d'ajout d'album
     */
    public function index()
    {
        $title = "Ajouter un album";
        $errors = $this->errors;
        $oldInput = $this->oldInput;
        include 'views/layouts/header.php';
        include 'views/media/add-album.php';
        include 'views/layouts/footer.php';
    }

    /**
     * Traiter l'ajout d'album (étape 1)
     */
    public function store()
    {
        $this->oldInput = $_POST;
        
        if (empty($_POST['titre'])) {
            $this->errors[] = "Le titre est obligatoire.";
        }
        
        if (empty($_POST['auteur'])) {
            $this->errors[] = "L'auteur est obligatoire.";
        }
        
        if (empty($_POST['editeur'])) {
            $this->errors[] = "L'éditeur est obligatoire.";
        }
        
        if (empty($_POST['nb_tracks']) || !is_numeric($_POST['nb_tracks']) || $_POST['nb_tracks'] <= 0) {
            $this->errors[] = "Le nombre de pistes doit être un nombre positif.";
        }

        if (empty($this->errors)) {
            $_SESSION['album_data'] = [
                'titre' => $_POST['titre'],
                'auteur' => $_POST['auteur'],
                'editeur' => $_POST['editeur'],
                'nb_tracks' => (int)$_POST['nb_tracks']
            ];
            
            header('Location: /add-songs');
            exit();
        }

        $this->index();
    }
}