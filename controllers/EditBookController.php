<?php

/**
 * Contrôleur pour la modification des livres
 */
class EditBookController
{
    private $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * Afficher le formulaire de modification d'un livre
     */
    public function index()
    {
        // Vérifier l'authentification
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header('Location: /login');
            exit();
        }

        // Récupérer l'ID du livre depuis l'URL
        $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
        $path = explode("/", $uri);
        
        if (!isset($path[2]) || empty($path[2]) || !is_numeric($path[2])) {
            header('Location: /books');
            exit();
        }
        
        $bookId = (int)$path[2];
        $book = $this->repository->getMediaDetails($bookId);
        
        if (!$book || $book['type_media'] !== 'book') {
            header('Location: /books');
            exit();
        }

        // Préparer les données pour le formulaire (comme pour l'ajout)
        $oldInput = [
            'titre' => $book['titre'],
            'auteur' => $book['auteur'],
            'page_number' => $book['page_number']
        ];
        $errors = [];
        $isEdit = true; // Flag pour indiquer que c'est une modification
        
        // Créer un objet book pour l'action du formulaire
        $bookObj = (object) $book;

        include 'views/layouts/header.php';
        include 'views/media/add-book.php'; // Réutiliser la vue d'ajout
        include 'views/layouts/footer.php';
    }
    
    /**
     * Traiter la modification du livre
     */
    public function store()
    {
        // Vérifier l'authentification
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header('Location: /login');
            exit();
        }

        // Récupérer l'ID du livre
        $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
        $path = explode("/", $uri);
        $bookId = (int)$path[2];

        $errors = [];
        $oldInput = $_POST;

        // Validation
        if (empty(trim($_POST['titre']))) {
            $errors['titre'] = 'Le titre est requis.';
        }

        if (empty(trim($_POST['auteur']))) {
            $errors['auteur'] = 'L\'auteur est requis.';
        }

        if (empty($_POST['page_number']) || !is_numeric($_POST['page_number']) || $_POST['page_number'] < 1) {
            $errors['page_number'] = 'Le nombre de pages doit être un nombre positif.';
        }

        // Si pas d'erreur, mettre à jour
        if (empty($errors)) {
            $success = $this->repository->updateBook(
                $bookId,
                trim($_POST['titre']),
                trim($_POST['auteur']),
                (int)$_POST['page_number']
            );

            if ($success) {
                header('Location: /book-details/' . $bookId);
                exit();
            } else {
                $errors['general'] = 'Erreur lors de la modification du livre.';
            }
        }

        // Si erreur, réafficher le formulaire avec les erreurs
        $book = $this->repository->getMediaDetails($bookId);
        $isEdit = true;
        $bookObj = (object) $book;

        include 'views/layouts/header.php';
        include 'views/media/add-book.php';
        include 'views/layouts/footer.php';
    }
}