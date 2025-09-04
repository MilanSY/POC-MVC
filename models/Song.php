<?php

/**
 * Classe Song
 * Représente une chanson dans la médiathèque
 */
class Song
{
    private string $titre;
    private float $duration; // Durée en minutes
    private int $note; // Note sur 5
    
    /**
     * Constructeur de Song
     * 
     * @param string $titre Titre de la chanson
     * @param float $duration Durée en minutes
     * @param int $note Note sur 5 (1-5)
     */
    public function __construct(string $titre, float $duration, int $note = 0)
    {
        $this->titre = $titre;
        $this->duration = $duration;
        $this->setNote($note);
    }
    
    /**
     * Obtenir le titre
     * 
     * @return string
     */
    public function getTitre(): string
    {
        return $this->titre;
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
     * Obtenir la note
     * 
     * @return int Note sur 5
     */
    public function getNote(): int
    {
        return $this->note;
    }
    
    /**
     * Définir la note
     * 
     * @param int $note Note sur 5 (1-5)
     * @return void
     */
    public function setNote(int $note): void
    {
        // Ensure the note is between 0 and 5
        $this->note = max(0, min(5, $note));
    }
}
