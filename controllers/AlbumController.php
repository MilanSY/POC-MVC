<?php

/**
 * Contrôleur pour les albums
 */
class AlbumController
{
    private $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    public function index()
    {
        $albums = $this->repository->getAllAlbums();
        
        $title = "Albums disponibles";
        $mediaType = "albums";
        $repository = $this->repository;
        
        include 'views/layouts/header.php';
        include 'views/albums/index.php';
        include 'views/layouts/footer.php';
    }
}