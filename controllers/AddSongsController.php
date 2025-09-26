<?php

/**
 * Contrôleur pour l'ajout de chansons
 */
class AddSongsController
{
    private $repository;
    private $errors = [];

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    /**
     * Afficher le formulaire d'ajout de chansons
     */
    public function index()
    {
        if (!isset($_SESSION['album_data'])) {
            header('Location: /add-album');
            exit();
        }

        $title = "Ajouter les chansons";
        $errors = $this->errors;
        $albumData = $_SESSION['album_data'];
        include 'views/layouts/header.php';
        include 'views/media/add-songs.php';
        include 'views/layouts/footer.php';
    }

    /**
     * Traiter l'ajout de chansons (étape 2)
     */
    public function store()
    {
        if (!isset($_SESSION['album_data'])) {
            header('Location: /add-album');
            exit();
        }

        $albumData = $_SESSION['album_data'];
        $nbTracks = $albumData['nb_tracks'];
        
        // Validation des chansons
        for ($i = 1; $i <= $nbTracks; $i++) {
            if (empty($_POST["song_title_$i"])) {
                $this->errors[] = "Le titre de la chanson $i est obligatoire.";
            }
            
            if (empty($_POST["song_note_$i"]) || !is_numeric($_POST["song_note_$i"]) || $_POST["song_note_$i"] < 1 || $_POST["song_note_$i"] > 5) {
                $this->errors[] = "La note de la chanson $i doit être entre 1 et 5.";
            }
            
            if (empty($_POST["song_duration_minutes_$i"]) && empty($_POST["song_duration_seconds_$i"])) {
                $this->errors[] = "La durée de la chanson $i est obligatoire.";
            }
            
            if (!empty($_POST["song_duration_minutes_$i"]) && (!is_numeric($_POST["song_duration_minutes_$i"]) || $_POST["song_duration_minutes_$i"] < 0)) {
                $this->errors[] = "Les minutes de la chanson $i doivent être un nombre positif.";
            }
            
            if (!empty($_POST["song_duration_seconds_$i"]) && (!is_numeric($_POST["song_duration_seconds_$i"]) || $_POST["song_duration_seconds_$i"] < 0 || $_POST["song_duration_seconds_$i"] >= 60)) {
                $this->errors[] = "Les secondes de la chanson $i doivent être un nombre entre 0 et 59.";
            }
        }

        if (empty($this->errors)) {
            // Préparer les données des chansons
            $songs = [];
            for ($i = 1; $i <= $nbTracks; $i++) {
                $durationSeconds = ((int)($_POST["song_duration_minutes_$i"] ?? 0)) * 60 + ((int)($_POST["song_duration_seconds_$i"] ?? 0));
                $songs[] = [
                    'title' => $_POST["song_title_$i"],
                    'note' => (int)$_POST["song_note_$i"],
                    'duration' => $durationSeconds
                ];
            }
            
            // Ajouter l'album et les chansons en base
            $success = $this->repository->addAlbumWithSongs(
                $albumData['titre'],
                $albumData['auteur'],
                $albumData['editeur'],
                $songs
            );
            
            if ($success) {
                unset($_SESSION['album_data']);
                header('Location: /albums?success=1');
                exit();
            } else {
                $this->errors[] = "Erreur lors de l'ajout de l'album.";
            }
        }

        // Réafficher le formulaire avec les erreurs
        $this->index();
    }
}