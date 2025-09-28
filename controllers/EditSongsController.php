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
        
        $realAlbumId = $album['album_id']; 
        $songs = $this->repository->getAlbumSongs($realAlbumId);
        
        $title = "Modifier les chansons";
        $errors = $this->errors;
        $albumData = [
            'titre' => $album['titre'],
            'auteur' => $album['auteur'],
            'nb_tracks' => $album['track_number']
        ];
        
        $songsData = [];
        foreach ($songs as $song) {
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
        include 'views/media/add-songs.php';
        include 'views/layouts/footer.php';
    }

    /**
     * Traiter la modification de chansons
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

        $songs = [];
        $errors = [];
        
        for ($i = 1; $i <= $albumData['nb_tracks']; $i++) {
            $title = trim($_POST["song_title_{$i}"] ?? '');
            $note = (int)($_POST["song_note_{$i}"] ?? 0);
            
            $minutes = (int)($_POST["song_duration_minutes_{$i}"] ?? 0);
            $seconds = (int)($_POST["song_duration_seconds_{$i}"] ?? 0);
            
            if (empty($title)) {
                $errors[] = "Le titre de la piste {$i} est requis.";
            }
            
            if ($minutes <= 0 && $seconds <= 0) {
                $errors[] = "La durée de la piste {$i} est requise.";
            }
            
            if ($note < 0 || $note > 5) {
                $errors[] = "La note de la piste {$i} doit être entre 0 et 5.";
            }
            
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
            $title = "Modifier les chansons";
            
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

        $album = $this->repository->getMediaDetails($albumId);
        $realAlbumId = $album['album_id'];
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