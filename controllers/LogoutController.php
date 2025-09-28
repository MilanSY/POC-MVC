<?php

require_once __DIR__ . '/../var/SessionManager.php';

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
        SessionManager::logout();
    }
}