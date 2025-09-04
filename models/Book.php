<?php

require_once 'Media.php';

/**
 * Classe Book
 * Représente un livre dans la médiathèque
 */
class Book extends Media
{
    private int $pageNumber;

    /**
     * Constructeur de Book
     * 
     * @param string $titre Titre du livre
     * @param string $auteur Auteur du livre
     * @param int $pageNumber Nombre de pages
     * @param bool $disponible Statut de disponibilité
     */
    public function __construct(string $titre, string $auteur, int $pageNumber, bool $disponible = true)
    {
        parent::__construct($titre, $auteur, $disponible);
        $this->pageNumber = $pageNumber;
    }

    /**
     * Obtenir le nombre de pages
     * 
     * @return int
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }
}
