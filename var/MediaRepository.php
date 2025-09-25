<?php

require_once 'Database.php';

/**
 * Classe MediaRepository
 * Gère les requêtes de la base de données pour les médias
 */
class MediaRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Obtenir tous les livres
     * 
     * @return array
     */
    public function getAllBooks(): array
    {
        // Nettoyer les incohérences avant de retourner les données
        $this->fixInconsistentMedias();
        
        $stmt = $this->pdo->query("
            SELECT m.id, m.titre, m.auteur, m.disponible, b.page_number,
                   u.username as borrowed_by_username
            FROM medias m
            JOIN books b ON m.id = b.media_id
            LEFT JOIN users u ON m.borrowed_by_user_id = u.id
            WHERE m.type_media = 'book'
            ORDER BY m.titre
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtenir tous les films
     * 
     * @return array
     */
    public function getAllMovies(): array
    {
        // Nettoyer les incohérences avant de retourner les données
        $this->fixInconsistentMedias();
        
        $stmt = $this->pdo->query("
            SELECT m.id, m.titre, m.auteur, m.disponible, mv.duration, mv.genre,
                   u.username as borrowed_by_username
            FROM medias m
            JOIN movies mv ON m.id = mv.media_id
            LEFT JOIN users u ON m.borrowed_by_user_id = u.id
            WHERE m.type_media = 'movie'
            ORDER BY m.titre
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtenir tous les albums
     * 
     * @return array
     */
    public function getAllAlbums(): array
    {
        // Nettoyer les incohérences avant de retourner les données
        $this->fixInconsistentMedias();
        
        $stmt = $this->pdo->query("
            SELECT 
                m.id, m.titre, m.auteur, m.disponible, a.track_number, a.editor,
                COUNT(als.song_id) as songs_count,
                ROUND(AVG(s.note), 2) as average_rating,
                ROUND(SUM(s.duration), 2) as total_duration,
                u.username as borrowed_by_username
            FROM medias m
            JOIN albums a ON m.id = a.media_id
            LEFT JOIN users u ON m.borrowed_by_user_id = u.id
            LEFT JOIN album_songs als ON a.id = als.album_id
            LEFT JOIN songs s ON als.song_id = s.id
            WHERE m.type_media = 'album'
            GROUP BY m.id, m.titre, m.auteur, m.disponible, a.track_number, a.editor, u.username
            ORDER BY m.titre
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtenir tous les médias par type
     * 
     * @param string|null $type Type de média (book, movie, album) ou null pour tous
     * @return array
     */
    public function getAllMedias(?string $type = null): array
    {
        // Nettoyer les incohérences avant de retourner les données
        $this->fixInconsistentMedias();
        
        $query = "
            SELECT 
                m.id, m.titre, m.auteur, m.disponible, m.type_media,
                b.page_number,
                mv.duration, mv.genre,
                a.track_number, a.editor,
                u.username as borrowed_by_username
            FROM medias m
            LEFT JOIN books b ON m.id = b.media_id AND m.type_media = 'book'
            LEFT JOIN movies mv ON m.id = mv.media_id AND m.type_media = 'movie'
            LEFT JOIN albums a ON m.id = a.media_id AND m.type_media = 'album'
            LEFT JOIN users u ON m.borrowed_by_user_id = u.id
        ";
        
        if ($type) {
            $query .= " WHERE m.type_media = ?";
            $query .= " ORDER BY m.titre";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$type]);
        } else {
            $query .= " ORDER BY m.type_media, m.titre";
            $stmt = $this->pdo->query($query);
        }
        
        return $stmt->fetchAll();
    }

    /**
     * Obtenir les chansons d'un album
     * 
     * @param int $albumId ID de l'album
     * @return array
     */
    public function getAlbumSongs(int $albumId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT s.*, als.track_position 
            FROM songs s
            JOIN album_songs als ON s.id = als.song_id
            WHERE als.album_id = ?
            ORDER BY als.track_position
        ");
        $stmt->execute([$albumId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtenir les statistiques générales
     * 
     * @return array
     */
    public function getStats(): array
    {
        $stmt = $this->pdo->query("
            SELECT 
                type_media,
                COUNT(*) as total,
                SUM(CASE WHEN disponible = TRUE THEN 1 ELSE 0 END) as disponibles
            FROM medias 
            GROUP BY type_media
        ");
        return $stmt->fetchAll();
    }

    /**
     * Emprunter un média
     * 
     * @param int $mediaId ID du média
     * @param int $userId ID de l'utilisateur qui emprunte
     * @return bool
     */
    public function emprunterMedia(int $mediaId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE medias 
            SET disponible = FALSE, borrowed_by_user_id = ?
            WHERE id = ? AND disponible = TRUE AND borrowed_by_user_id IS NULL
        ");
        $stmt->execute([$userId, $mediaId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Rendre un média
     * 
     * @param int $mediaId ID du média
     * @param int $userId ID de l'utilisateur qui rend (optionnel pour admin)
     * @return bool
     */
    public function rendreMedia(int $mediaId, int $userId): bool
    {
        if ($userId != null) {

            $stmt = $this->pdo->prepare("
                UPDATE medias 
                SET disponible = TRUE, borrowed_by_user_id = NULL
                WHERE id = ? AND disponible = FALSE AND borrowed_by_user_id = ?
            ");
            $stmt->execute([$mediaId, $userId]);
        } else {

            $stmt = $this->pdo->prepare("
                UPDATE medias 
                SET disponible = TRUE, borrowed_by_user_id = NULL
                WHERE id = ? AND disponible = FALSE
            ");
            $stmt->execute([$mediaId]);
        }
        return $stmt->rowCount() > 0;
    }

    /**
     * Obtenir les médias empruntés par un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @return array
     */
    public function getMediasBorrowedByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT m.id, m.titre, m.auteur, m.type_media
            FROM medias m
            WHERE m.borrowed_by_user_id = ?
            ORDER BY m.titre
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Vérifier si un média peut être emprunté par un utilisateur
     * 
     * @param int $mediaId ID du média
     * @return bool
     */
    public function canBorrowMedia(int $mediaId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT disponible, borrowed_by_user_id 
            FROM medias 
            WHERE id = ?
        ");
        $stmt->execute([$mediaId]);
        $media = $stmt->fetch();
        
        return $media && $media['disponible'] && $media['borrowed_by_user_id'] === null;
    }

    /**
     * Nettoyer les médias marqués comme empruntés mais sans utilisateur assigné
     * Les remet automatiquement disponibles
     * 
     * @return int Nombre de médias corrigés
     */
    public function fixInconsistentMedias(): int
    {
        $stmt = $this->pdo->prepare("
            UPDATE medias 
            SET disponible = TRUE 
            WHERE disponible = FALSE AND borrowed_by_user_id IS NULL
        ");
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Obtenir tous les médias avec nettoyage automatique des incohérences
     * 
     * @param string|null $type Type de média
     * @return array
     */
    private function getCleanMedias(?string $type = null): array
    {
        // Nettoyer les incohérences avant de retourner les données
        $this->fixInconsistentMedias();
        
        return $this->getAllMedias($type);
    }
}