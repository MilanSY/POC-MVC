<?php

/**
 * Classe Media
 * Représente un média générique dans la médiathèque
 */
abstract class Media
{
    protected string $titre;
    protected string $auteur;
    protected bool $disponible;

    /**
     * Constructeur de Media
     * 
     * @param string $titre Titre du média
     * @param string $auteur Auteur du média
     * @param bool $disponible Statut de disponibilité
     */
    public function __construct(string $titre, string $auteur, bool $disponible = true)
    {
        $this->titre = $titre;
        $this->auteur = $auteur;
        $this->disponible = $disponible;
    }

    /**
     * Emprunter le média
     * 
     * @return bool Statut de succès
     */
    public function emprunter(): bool
    {
        if ($this->disponible) {
            $this->disponible = false;
            return true;
        }
        return false;
    }

    /**
     * Rendre le média
     * 
     * @return bool Statut de succès
     */
    public function rendre(): bool
    {
        if (!$this->disponible) {
            $this->disponible = true;
            return true;
        }
        return false;
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
     * Obtenir l'auteur
     * 
     * @return string
     */
    public function getAuteur(): string
    {
        return $this->auteur;
    }

    /**
     * Vérifier si le média est disponible
     * 
     * @return bool
     */
    public function isDisponible(): bool
    {
        return $this->disponible;
    }
}
