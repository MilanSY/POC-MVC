<?php

require_once 'Media.php';
require_once 'Song.php';

/**
 * Classe Album
 * Représente un album dans la médiathèque
 */
class Album extends Media
{
    private int $trackNumber;
    private string $editor;
    private array $songs = []; // Tableau d'objets Song
    
    /**
     * Constructeur d'Album
     * 
     * @param string $titre Titre de l'album
     * @param string $auteur Artiste de l'album
     * @param int $trackNumber Nombre de pistes
     * @param string $editor Éditeur/label de l'album
     * @param bool $disponible Statut de disponibilité
     */
    public function __construct(
        string $titre, 
        string $auteur, 
        int $trackNumber, 
        string $editor, 
        bool $disponible = true
    ) {
        parent::__construct($titre, $auteur, $disponible);
        $this->trackNumber = $trackNumber;
        $this->editor = $editor;
    }
    
    /**
     * Obtenir le nombre de pistes
     * 
     * @return int
     */
    public function getTrackNumber(): int
    {
        return $this->trackNumber;
    }
    
    /**
     * Obtenir l'éditeur/label
     * 
     * @return string
     */
    public function getEditor(): string
    {
        return $this->editor;
    }
    
    /**
     * Ajouter une chanson à l'album
     * 
     * @param Song $song La chanson à ajouter
     * @return bool Statut de succès
     */
    public function addSong(Song $song): bool
    {
        if (count($this->songs) < $this->trackNumber) {
            $this->songs[] = $song;
            return true;
        }
        return false; // Album is full
    }
    
    /**
     * Obtenir toutes les chansons de l'album
     * 
     * @return array Tableau d'objets Song
     */
    public function getSongs(): array
    {
        return $this->songs;
    }
    
    /**
     * Obtenir la note moyenne de toutes les chansons de l'album
     * 
     * @return float Note moyenne (0-5)
     */
    public function getAverageRating(): float
    {
        if (empty($this->songs)) {
            return 0;
        }
        
        $sum = array_reduce($this->songs, function($carry, Song $song) {
            return $carry + $song->getNote();
        }, 0);
        
        return $sum / count($this->songs);
    }
    
    /**
     * Obtenir la durée totale de l'album
     * 
     * @return float Durée totale en minutes
     */
    public function getTotalDuration(): float
    {
        return array_reduce($this->songs, function($carry, Song $song) {
            return $carry + $song->getDuration();
        }, 0);
    }
}
