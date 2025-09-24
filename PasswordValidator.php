<?php

/**
 * Classe PasswordValidator
 * Validation des mots de passe selon les règles de sécurité
 */
class PasswordValidator
{
    /**
     * Valider un mot de passe selon les règles spécifiées :
     * - Minimum 8 caractères
     * - Au moins une majuscule
     * - Au moins une minuscule
     * - Au moins un chiffre
     * - Au moins un caractère spécial
     * - Ne doit pas contenir l'identifiant de l'utilisateur
     * 
     * @param string $password Mot de passe à valider
     * @param string $username Nom d'utilisateur (pour vérifier qu'il n'est pas dans le mot de passe)
     * @return array Tableau avec 'valid' (bool) et 'errors' (array)
     */
    public static function validate(string $password, string $username = ''): array
    {
        // Regex combinée pour toutes les règles principales
        $regex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?~`]).{8,}$/';
        $isValidFormat = preg_match($regex, $password);
        
        // Vérifier que le mot de passe ne contient pas l'username
        $containsUsername = !empty($username) && stripos($password, $username) !== false;
        
        if ($isValidFormat && !$containsUsername) {
            return [
                'valid' => true,
                'errors' => []
            ];
        }
        
        $errorMessage = 'Le mot de passe doit contenir au minimum 8 caractères, au moins une majuscule, une minuscule, un chiffre et un caractère spécial.';
        
        if ($containsUsername) {
            $errorMessage .= ' Il ne doit pas contenir votre nom d\'utilisateur.';
        }
        
        return [
            'valid' => false,
            'errors' => [$errorMessage]
        ];
    }
}