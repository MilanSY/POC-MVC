<?php

/**
 * Contrôleur pour les livres
 */
class BookController
{
    private $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    public function index()
    {
        $books = $this->repository->getAllBooks();
        
        $title = "Livres disponibles";
        $mediaType = "books";
        
        include 'views/layouts/header.php';
        include 'views/books/index.php';
        include 'views/layouts/footer.php';
    }
}