<?php

/**
 * Contrôleur pour la déconnexion
 */
class LogoutController
{
    /**
     * Traiter la déconnexion
     */
    public function index()
    {
        // Détruire la session
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        // Rediriger vers la page d'accueil
        header('Location: /home');
        exit();
    }
}