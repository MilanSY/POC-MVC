<?php

/**
 * Classe utilitaire pour la gestion des sessions
 */
class SessionManager
{
    /**
     * Nettoyer complètement une session
     * Supprime toutes les données de session et détruit la session
     */
    public static function clearSession(): void
    {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }

    /**
     * Vérifier si l'utilisateur en session existe toujours en base de données
     * Si l'utilisateur n'existe plus, la session est automatiquement nettoyée
     * 
     * @return bool True si l'utilisateur est valide, False sinon
     */
    public static function validateUserSession(): bool
    {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user_id'])) {
            return false;
        }

        $currentUser = User::findById($_SESSION['user_id']);
        if (!$currentUser) {
            self::clearSession();
            session_start();    
            return false;
        }

        return true;
    }

    /**
     * Déconnecter un utilisateur proprement
     */
    public static function logout(): void
    {
        self::clearSession();
        header('Location: /home');
        exit();
    }
}