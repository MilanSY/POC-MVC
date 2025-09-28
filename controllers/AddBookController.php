<?php

/**
 * Contrôleur pour l'ajout de livres
 */
class AddBookController
{
    private $repository;
    private $errors = [];
    private $oldInput = [];

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    /**
     * Afficher le formulaire d'ajout de livre
     */
    public function index()
    {
        $title = "Ajouter un livre";
        $errors = $this->errors;
        $oldInput = $this->oldInput;
        include 'views/layouts/header.php';
        include 'views/media/add-book.php';
        include 'views/layouts/footer.php';
    }

    /**
     * Traiter l'ajout de livre
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
        
        if (empty($_POST['page_number']) || !is_numeric($_POST['page_number']) || $_POST['page_number'] <= 0) {
            $this->errors[] = "Le nombre de pages doit être un nombre positif.";
        }

        if (empty($this->errors)) {
            $success = $this->repository->addBook($_POST['titre'], $_POST['auteur'], (int)$_POST['page_number']);
            
            if ($success) {
                header('Location: /books?success=1');
                exit();
            } else {
                $this->errors[] = "Erreur lors de l'ajout du livre.";
            }
        }

        $this->index();
    }
}