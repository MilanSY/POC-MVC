<?php

/**
 * Contrôleur pour les films
 */
class MovieController
{
    private $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    public function index()
    {
        $movies = $this->repository->getAllMovies();
        
        $title = "Films disponibles";
        $mediaType = "movies";
        
        include 'views/layouts/header.php';
        include 'views/movies/index.php';
        include 'views/layouts/footer.php';
    }
}