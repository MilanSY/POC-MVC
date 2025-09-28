<?php

/**
 * Contrôleur pour l'ajout de films
 */
class AddMovieController
{
    private $repository;
    private $errors = [];
    private $oldInput = [];

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    /**
     * Afficher le formulaire d'ajout de film
     */
    public function index()
    {
        $title = "Ajouter un film";
        $errors = $this->errors;
        $oldInput = $this->oldInput;
        
        // Récupérer les genres disponibles
        $genres = [
            'Action' => 'Action',
            'Comédie' => 'Comédie',
            'Drame' => 'Drame',
            'Science-Fiction' => 'Science-Fiction',
            'Horreur' => 'Horreur',
            'Documentaire' => 'Documentaire'
        ];
        
        include 'views/layouts/header.php';
        include 'views/media/add-movie.php';
        include 'views/layouts/footer.php';
    }

    /**
     * Traiter l'ajout de film
     */
    public function store()
    {
        $this->oldInput = $_POST;
        
        // Validation des champs
        if (empty($_POST['titre'])) {
            $this->errors[] = "Le titre est obligatoire.";
        }
        
        if (empty($_POST['auteur'])) {
            $this->errors[] = "L'auteur est obligatoire.";
        }
        
        if (empty($_POST['duration_hours']) && empty($_POST['duration_minutes'])) {
            $this->errors[] = "La durée est obligatoire.";
        }
        
        if (!empty($_POST['duration_hours']) && (!is_numeric($_POST['duration_hours']) || $_POST['duration_hours'] < 0)) {
            $this->errors[] = "Les heures doivent être un nombre positif.";
        }
        
        if (!empty($_POST['duration_minutes']) && (!is_numeric($_POST['duration_minutes']) || $_POST['duration_minutes'] < 0 || $_POST['duration_minutes'] >= 60)) {
            $this->errors[] = "Les minutes doivent être un nombre entre 0 et 59.";
        }
        
        if (empty($_POST['genre'])) {
            $this->errors[] = "Le genre est obligatoire.";
        } else {
            // Vérifier que le genre est valide
            $validGenres = ['Action', 'Comédie', 'Drame', 'Science-Fiction', 'Horreur', 'Documentaire'];
            if (!in_array($_POST['genre'], $validGenres)) {
                $this->errors[] = "Genre non valide.";
            }
        }

        if (empty($this->errors)) {
            // Formatter la durée en string
            $hours = (int)($_POST['duration_hours'] ?? 0);
            $minutes = (int)($_POST['duration_minutes'] ?? 0);
            
            $durationString = '';
            if ($hours > 0) {
                $durationString .= $hours . 'h';
                if ($minutes > 0) {
                    $durationString .= ' ' . $minutes . 'min';
                }
            } else {
                $durationString = $minutes . 'min';
            }
            
            // Ajouter le film en base
            $success = $this->repository->addMovie($_POST['titre'], $_POST['auteur'], $durationString, $_POST['genre']);
            
            if ($success) {
                header('Location: /movies?success=1');
                exit();
            } else {
                $this->errors[] = "Erreur lors de l'ajout du film.";
            }
        }

        // Réafficher le formulaire avec les erreurs
        $this->index();
    }
}