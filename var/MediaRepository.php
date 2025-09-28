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
            LEFT JOIN users u ON m.borrowed_by = u.id
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
            LEFT JOIN users u ON m.borrowed_by = u.id
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
                COUNT(s.id) as songs_count,
                ROUND(AVG(s.note), 2) as average_rating,
                u.username as borrowed_by_username
            FROM medias m
            JOIN albums a ON m.id = a.media_id
            LEFT JOIN users u ON m.borrowed_by = u.id
            LEFT JOIN songs s ON a.id = s.album_id
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
            LEFT JOIN users u ON m.borrowed_by = u.id
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
            SELECT s.* 
            FROM songs s
            WHERE s.album_id = ?
            ORDER BY s.id
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
            SET disponible = FALSE, borrowed_by = ?
            WHERE id = ? AND disponible = TRUE AND borrowed_by IS NULL
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
                SET disponible = TRUE, borrowed_by = NULL
                WHERE id = ? AND disponible = FALSE AND borrowed_by = ?
            ");
            $stmt->execute([$mediaId, $userId]);
        } else {

            $stmt = $this->pdo->prepare("
                UPDATE medias 
                SET disponible = TRUE, borrowed_by = NULL
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
            WHERE m.borrowed_by = ?
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
            SELECT disponible, borrowed_by 
            FROM medias 
            WHERE id = ?
        ");
        $stmt->execute([$mediaId]);
        $media = $stmt->fetch();
        
        return $media && $media['disponible'] && $media['borrowed_by'] === null;
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
            WHERE disponible = FALSE AND borrowed_by IS NULL
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

    /**
     * Ajouter un livre
     * 
     * @param string $titre Titre du livre
     * @param string $auteur Auteur du livre
     * @param int $pageNumber Nombre de pages
     * @return bool
     */
    public function addBook(string $titre, string $auteur, int $pageNumber): bool
    {
        try {
            $this->pdo->beginTransaction();
            
            // Insérer dans la table medias
            $stmt = $this->pdo->prepare("
                INSERT INTO medias (titre, auteur, type_media, disponible) 
                VALUES (?, ?, 'book', TRUE)
            ");
            $stmt->execute([$titre, $auteur]);
            $mediaId = $this->pdo->lastInsertId();
            
            // Insérer dans la table books
            $stmt = $this->pdo->prepare("
                INSERT INTO books (media_id, page_number) 
                VALUES (?, ?)
            ");
            $stmt->execute([$mediaId, $pageNumber]);
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Ajouter un film
     * 
     * @param string $titre Titre du film
     * @param string $auteur Réalisateur du film
     * @param string $duration Durée au format string (ex: "2h 30min")
     * @param string $genre Genre du film
     * @return bool
     */
    public function addMovie(string $titre, string $auteur, string $duration, string $genre): bool
    {
        try {
            $this->pdo->beginTransaction();
            
            // Insérer dans la table medias
            $stmt = $this->pdo->prepare("
                INSERT INTO medias (titre, auteur, type_media, disponible) 
                VALUES (?, ?, 'movie', TRUE)
            ");
            $stmt->execute([$titre, $auteur]);
            $mediaId = $this->pdo->lastInsertId();
            
            // Insérer dans la table movies
            $stmt = $this->pdo->prepare("
                INSERT INTO movies (media_id, duration, genre) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$mediaId, $duration, $genre]);
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Ajouter un album avec ses chansons
     * 
     * @param string $titre Titre de l'album
     * @param string $auteur Artiste de l'album
     * @param string $editeur Éditeur de l'album
     * @param array $songs Tableau des chansons [['title' => '', 'note' => 0, 'duration' => ''], ...]
     * @return bool
     */
    public function addAlbumWithSongs(string $titre, string $auteur, string $editeur, array $songs): bool
    {
        try {
            $this->pdo->beginTransaction();
            
            // Insérer dans la table medias
            $stmt = $this->pdo->prepare("
                INSERT INTO medias (titre, auteur, type_media, disponible) 
                VALUES (?, ?, 'album', TRUE)
            ");
            $stmt->execute([$titre, $auteur]);
            $mediaId = $this->pdo->lastInsertId();
            
            // Insérer dans la table albums
            $stmt = $this->pdo->prepare("
                INSERT INTO albums (media_id, track_number, editor) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$mediaId, count($songs), $editeur]);
            $albumId = $this->pdo->lastInsertId();
            
            // Insérer les chansons directement liées à l'album
            $songStmt = $this->pdo->prepare("
                INSERT INTO songs (title, note, duration, album_id) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($songs as $song) {
                // Insérer la chanson avec l'album_id
                $songStmt->execute([$song['title'], $song['note'], $song['duration'], $albumId]);
            }
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            // Pour débugger : log ou afficher l'erreur
            error_log("Erreur addAlbumWithSongs: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir les détails d'un média par son ID
     * 
     * @param int $mediaId ID du média
     * @return array|false Détails du média ou false si non trouvé
     */
    public function getMediaDetails(int $mediaId)
    {
        // Nettoyer les incohérences avant de retourner les données
        $this->fixInconsistentMedias();
        
        $stmt = $this->pdo->prepare("
            SELECT 
                m.id, m.titre, m.auteur, m.disponible, m.type_media, m.borrowed_by,
                b.page_number,
                mv.duration as movie_duration, mv.genre,
                a.track_number, a.editor, a.id as album_id,
                u.username as borrowed_by_username,
                u.id as borrowed_by_id
            FROM medias m
            LEFT JOIN books b ON m.id = b.media_id AND m.type_media = 'book'
            LEFT JOIN movies mv ON m.id = mv.media_id AND m.type_media = 'movie'
            LEFT JOIN albums a ON m.id = a.media_id AND m.type_media = 'album'
            LEFT JOIN users u ON m.borrowed_by = u.id
            WHERE m.id = ?
        ");
        $stmt->execute([$mediaId]);
        $media = $stmt->fetch();
        
        if ($media && $media['type_media'] === 'album' && $media['album_id']) {
            // Récupérer les chansons de l'album
            $media['songs'] = $this->getAlbumSongs($media['album_id']);
        }
        
        return $media;
    }

    /**
     * Supprimer un média
     * 
     * @param int $mediaId ID du média
     * @return bool
     */
    public function deleteMedia(int $mediaId): bool
    {
        try {
            $this->pdo->beginTransaction();
            
            // Les suppressions en cascade sont gérées par les contraintes de clé étrangère
            $stmt = $this->pdo->prepare("DELETE FROM medias WHERE id = ?");
            $stmt->execute([$mediaId]);
            
            $this->pdo->commit();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Mettre à jour un livre
     * 
     * @param int $mediaId ID du média
     * @param string $titre Nouveau titre
     * @param string $auteur Nouvel auteur
     * @param int $pageNumber Nouveau nombre de pages
     * @return bool
     */
    public function updateBook(int $mediaId, string $titre, string $auteur, int $pageNumber): bool
    {
        try {
            $this->pdo->beginTransaction();
            
            // Mettre à jour la table medias
            $stmt = $this->pdo->prepare("UPDATE medias SET titre = ?, auteur = ? WHERE id = ? AND type_media = 'book'");
            $stmt->execute([$titre, $auteur, $mediaId]);
            
            // Mettre à jour la table books
            $stmt = $this->pdo->prepare("UPDATE books SET page_number = ? WHERE media_id = ?");
            $stmt->execute([$pageNumber, $mediaId]);
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Mettre à jour un film
     * 
     * @param int $mediaId ID du média
     * @param string $titre Nouveau titre
     * @param string $auteur Nouvel auteur/réalisateur
     * @param string $duration Nouvelle durée
     * @param string $genre Nouveau genre
     * @return bool
     */
    public function updateMovie(int $mediaId, string $titre, string $auteur, string $duration, string $genre): bool
    {
        try {
            $this->pdo->beginTransaction();
            
            // Mettre à jour la table medias
            $stmt = $this->pdo->prepare("UPDATE medias SET titre = ?, auteur = ? WHERE id = ? AND type_media = 'movie'");
            $stmt->execute([$titre, $auteur, $mediaId]);
            
            // Mettre à jour la table movies
            $stmt = $this->pdo->prepare("UPDATE movies SET duration = ?, genre = ? WHERE media_id = ?");
            $stmt->execute([$duration, $genre, $mediaId]);
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Mettre à jour un album (sans les chansons)
     * 
     * @param int $mediaId ID du média
     * @param string $titre Nouveau titre
     * @param string $auteur Nouvel auteur/artiste
     * @param int $trackNumber Nouveau nombre de pistes
     * @param string $editor Nouvel éditeur
     * @return bool
     */
    public function updateAlbum(int $mediaId, string $titre, string $auteur, int $trackNumber, string $editor): bool
    {
        try {
            $this->pdo->beginTransaction();
            
            // Mettre à jour la table medias
            $stmt = $this->pdo->prepare("UPDATE medias SET titre = ?, auteur = ? WHERE id = ? AND type_media = 'album'");
            $stmt->execute([$titre, $auteur, $mediaId]);
            
            // Mettre à jour la table albums
            $stmt = $this->pdo->prepare("UPDATE albums SET track_number = ?, editor = ? WHERE media_id = ?");
            $stmt->execute([$trackNumber, $editor, $mediaId]);
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Mettre à jour une chanson
     * 
     * @param int $songId ID de la chanson
     * @param string $title Nouveau titre
     * @param string $duration Nouvelle durée
     * @param int $note Nouvelle note
     * @return bool
     */
    public function updateSong(int $songId, string $title, string $duration, int $note): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE songs 
                SET title = ?, duration = ?, note = ? 
                WHERE id = ?
            ");
            $stmt->execute([$title, $duration, $note, $songId]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Mettre à jour toutes les chansons d'un album
     * @param int $albumId ID de l'album
     * @param array $songs Tableau des chansons [['title' => '', 'duration' => '', 'note' => 0], ...]
     * @return bool Succès de l'opération
     */
    public function updateAlbumSongs(int $albumId, array $songs): bool
    {
        try {
            $this->pdo->beginTransaction();
            
            // Supprimer toutes les chansons existantes de cet album
            $deleteStmt = $this->pdo->prepare("DELETE FROM songs WHERE album_id = ?");
            $deleteStmt->execute([$albumId]);
            
            // Insérer les nouvelles chansons
            $insertStmt = $this->pdo->prepare("
                INSERT INTO songs (title, note, duration, album_id) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($songs as $song) {
                $insertStmt->execute([
                    $song['title'], 
                    $song['note'], 
                    $song['duration'], 
                    $albumId
                ]);
            }
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Erreur updateAlbumSongs: " . $e->getMessage());
            return false;
        }
    }
}