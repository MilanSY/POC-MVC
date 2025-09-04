<?php

require_once 'Media.php';

/**
 * Enum MovieGenre
 * Représente le genre d'un film
 */
enum MovieGenre: string
{
    case ACTION = 'Action';
    case COMEDY = 'Comédie';
    case DRAMA = 'Drame';
    case SCIFI = 'Science-Fiction';
    case HORROR = 'Horreur';
    case DOCUMENTARY = 'Documentaire';
}

/**
 * Classe Movie
 * Représente un film dans la médiathèque
 */
class Movie extends Media
{
    private float $duration; // Durée en minutes
    private MovieGenre $genre;

    /**
     * Constructeur de Movie
     * 
     * @param string $titre Titre du film
     * @param string $auteur Réalisateur du film
     * @param float $duration Durée en minutes
     * @param MovieGenre $genre Genre du film
     * @param bool $disponible Statut de disponibilité
     */
    public function __construct(
        string $titre, 
        string $auteur, 
        float $duration, 
        MovieGenre $genre, 
        bool $disponible = true
    ) {
        parent::__construct($titre, $auteur, $disponible);
        $this->duration = $duration;
        $this->genre = $genre;
    }

    /**
     * Obtenir la durée
     * 
     * @return float Durée en minutes
     */
    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * Obtenir le genre
     * 
     * @return MovieGenre
     */
    public function getGenre(): MovieGenre
    {
        return $this->genre;
    }
}
