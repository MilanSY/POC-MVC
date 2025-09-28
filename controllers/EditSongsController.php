<?php

/**
 * Contrôleur pour la modification de chansons d'un album
 */
class EditSongsController
{
    private $repository;
    private $errors = [];

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    /**
     * Afficher le formulaire de modification de chansons
     */
    public function index()
    {
        // Vérifier l'authentification
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header('Location: /login');
            exit();
        }

        // Récupérer l'ID de l'album depuis l'URL
        $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
        $path = explode("/", $uri);
        
        if (!isset($path[2]) || empty($path[2]) || !is_numeric($path[2])) {
            header('Location: /albums');
            exit();
        }
        
        $albumId = (int)$path[2];
        
        // Récupérer les données de l'album
        $album = $this->repository->getMediaDetails($albumId);
        
        if (!$album || $album['type_media'] !== 'album') {
            header('Location: /albums');
            exit();
        }
        
        // Récupérer les chansons existantes
        $realAlbumId = $album['album_id']; // ID dans la table albums
        $songs = $this->repository->getAlbumSongs($realAlbumId);
        
        // Debug pour voir les données récupérées
        error_log("EditSongsController - Media ID: " . $albumId);
        error_log("EditSongsController - Real Album ID: " . $realAlbumId);
        error_log("EditSongsController - Songs count: " . count($songs));
        if (!empty($songs)) {
            error_log("EditSongsController - First song: " . json_encode($songs[0]));
        }
        
        $title = "Modifier les chansons";
        $errors = $this->errors;
        $albumData = [
            'titre' => $album['titre'],
            'auteur' => $album['auteur'],
            'nb_tracks' => $album['track_number']
        ];
        
        // Préparer les données des chansons pour le formulaire
        $songsData = [];
        foreach ($songs as $song) {
            // Parser la durée (format: "3min 45s" ou "45s")
            $durationString = $song['duration'];
            $minutes = 0;
            $seconds = 0;
            
            if (preg_match('/(\d+)min/', $durationString, $minMatches)) {
                $minutes = (int)$minMatches[1];
            }
            if (preg_match('/(\d+)s/', $durationString, $secMatches)) {
                $seconds = (int)$secMatches[1];
            }
            
            $songsData[] = [
                'id' => $song['id'],
                'title' => $song['title'],
                'duration_minutes' => $minutes,
                'duration_seconds' => $seconds,
                'note' => $song['note']
            ];
        }
        
        // Compléter avec des chansons vides si nécessaire
        while (count($songsData) < $album['track_number']) {
            $songsData[] = [
                'id' => null,
                'title' => '',
                'duration_minutes' => 0,
                'duration_seconds' => 0,
                'note' => 0
            ];
        }
        
        include 'views/layouts/header.php';
        include 'views/media/add-songs.php'; // Réutiliser la vue d'ajout
        include 'views/layouts/footer.php';
    }

    /**
     * Traiter la modification de chansons
     */
    public function store()
    {
        // Vérifier l'authentification
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header('Location: /login');
            exit();
        }

        // Récupérer l'ID de l'album
        $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
        $path = explode("/", $uri);
        $albumId = (int)$path[2];
        
        // Récupérer les données de l'album
        $album = $this->repository->getMediaDetails($albumId);
        if (!$album) {
            header('Location: /albums');
            exit();
        }

        $albumData = [
            'titre' => $album['titre'],
            'auteur' => $album['auteur'],
            'nb_tracks' => $album['track_number']
        ];

        // Traitement des chansons
        $songs = [];
        $errors = [];
        
        for ($i = 1; $i <= $albumData['nb_tracks']; $i++) {
            $title = trim($_POST["song_title_{$i}"] ?? '');
            $note = (int)($_POST["song_note_{$i}"] ?? 0);
            
            // Traitement de la durée (minutes et secondes)
            $minutes = (int)($_POST["song_duration_minutes_{$i}"] ?? 0);
            $seconds = (int)($_POST["song_duration_seconds_{$i}"] ?? 0);
            
            // Validation
            if (empty($title)) {
                $errors[] = "Le titre de la piste {$i} est requis.";
            }
            
            if ($minutes <= 0 && $seconds <= 0) {
                $errors[] = "La durée de la piste {$i} est requise.";
            }
            
            if ($note < 0 || $note > 5) {
                $errors[] = "La note de la piste {$i} doit être entre 0 et 5.";
            }
            
            // Construire la string de durée
            $durationString = '';
            if ($minutes > 0) {
                $durationString .= $minutes . 'min';
                if ($seconds > 0) {
                    $durationString .= ' ' . $seconds . 's';
                }
            } else if ($seconds > 0) {
                $durationString = $seconds . 's';
            }
            
            $songs[] = [
                'title' => $title,
                'duration' => $durationString,
                'note' => $note
            ];
        }

        if (!empty($errors)) {
            $this->errors = $errors;
            // Réafficher le formulaire avec les erreurs
            $title = "Modifier les chansons";
            
            // Récupérer les chansons existantes pour le formulaire
            $album = $this->repository->getMediaDetails($albumId);
            $realAlbumId = $album['album_id'];
            $existingSongs = $this->repository->getAlbumSongs($realAlbumId);
            $songsData = [];
            foreach ($existingSongs as $song) {
                $songsData[] = [
                    'id' => $song['id'],
                    'title' => $song['title'],
                    'duration' => $song['duration'],
                    'note' => $song['note']
                ];
            }
            
            // Compléter avec les données soumises en cas d'erreur
            for ($i = 0; $i < count($songs); $i++) {
                if (isset($songsData[$i])) {
                    $songsData[$i]['title'] = $songs[$i]['title'];
                    $songsData[$i]['duration'] = $songs[$i]['duration'];
                    $songsData[$i]['note'] = $songs[$i]['note'];
                } else {
                    $songsData[] = [
                        'id' => null,
                        'title' => $songs[$i]['title'],
                        'duration' => $songs[$i]['duration'],
                        'note' => $songs[$i]['note']
                    ];
                }
            }
            
            include 'views/layouts/header.php';
            include 'views/media/add-songs.php';
            include 'views/layouts/footer.php';
            return;
        }

        // Mettre à jour les chansons
        $album = $this->repository->getMediaDetails($albumId);
        $realAlbumId = $album['album_id']; // ID dans la table albums
        $success = $this->repository->updateAlbumSongs($realAlbumId, $songs);

        if ($success) {
            header('Location: /album-details/' . $albumId);
            exit();
        } else {
            $this->errors[] = 'Erreur lors de la mise à jour des chansons.';
            $this->index();
        }
    }
}