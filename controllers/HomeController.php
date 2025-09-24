<?php

/**
 * Contrôleur pour la page d'accueil
 */
class HomeController
{
    private $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    public function index()
    {
        $allMedias = $this->repository->getAllMedias();
        shuffle($allMedias);

        $totalCount = count($allMedias);
        
        $booksCount = count(array_filter($allMedias, fn($media) => $media['type_media'] === 'book'));
        $albumsCount = count(array_filter($allMedias, fn($media) => $media['type_media'] === 'album'));
        $moviesCount = count(array_filter($allMedias, fn($media) => $media['type_media'] === 'movie'));
        
        include 'views/layouts/header.php';
        include 'views/home/index.php';
        include 'views/layouts/footer.php';
    }
}