<?php

require_once 'models/User.php';
require_once __DIR__ . '/../var/PasswordValidator.php';

/**
 * Contrôleur pour l'authentification
 * Gère l'inscription, la connexion et la déconnexion
 */
class AuthController
{
    private array $errors = [];
    private array $oldInput = [];
    
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Afficher le formulaire de connexion
     */
    public function loginForm()
    {
        $this->errors = $_SESSION['errors'] ?? [];
        $this->oldInput = $_SESSION['old_input'] ?? [];
        
        unset($_SESSION['errors'], $_SESSION['old_input']);
        
        include 'views/layouts/header.php';
        include 'views/auth/login.php';
        include 'views/layouts/footer.php';
    }
    
    /**
     * Traiter la connexion
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $this->oldInput = ['email' => $email];
        
        if (empty($email)) {
            $this->errors[] = 'L\'adresse email est requise.';
        }
        
        if (empty($password)) {
            $this->errors[] = 'Le mot de passe est requis.';
        }
        
        if (!empty($this->errors)) {
            $this->redirectWithErrors('/login');
            return;
        }
        
        $user = User::findByEmail($email);
        
        if (!$user || !$user->verifyPassword($password)) {
            $this->errors[] = 'Adresse email ou mot de passe incorrect.';
            $this->redirectWithErrors('/login');
            return;
        }
        
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['logged_in'] = true;
        
        header('Location: /');
        exit;
    }
    
    /**
     * Afficher le formulaire d'inscription
     */
    public function registerForm()
    {
         $this->errors = $_SESSION['errors'] ?? [];
        $this->oldInput = $_SESSION['old_input'] ?? [];
        
        unset($_SESSION['errors'], $_SESSION['old_input']);
        
        include 'views/layouts/header.php';
        include 'views/auth/register.php';
        include 'views/layouts/footer.php';
    }
    
    /**
     * Traiter l'inscription
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }
        
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $this->oldInput = [
            'username' => $username,
            'email' => $email
        ];
        
        
        if (empty($username)) {
            $this->errors[] = 'Le nom d\'utilisateur est requis.';
        } elseif (strlen($username) < 3) {
            $this->errors[] = 'Le nom d\'utilisateur doit contenir au moins 3 caractères.';
        } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            $this->errors[] = 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres, tirets et underscores.';
        }
        
        if (empty($email)) {
            $this->errors[] = 'L\'email est requis.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'L\'email n\'est pas valide.';
        }
        
        if (empty($password)) {
            $this->errors[] = 'Le mot de passe est requis.';
        } else {
            $passwordValidation = PasswordValidator::validate($password, $username);
            if (!$passwordValidation['valid']) {
                $this->errors = array_merge($this->errors, $passwordValidation['errors']);
            }
        }
        
        if (empty($confirmPassword)) {
            $this->errors[] = 'La confirmation du mot de passe est requise.';
        } elseif ($password !== $confirmPassword) {
            $this->errors[] = 'Les mots de passe ne correspondent pas.';
        }
        
        // Vérifier l'unicité du nom d'utilisateur et de l'email
        if (!empty($username) && User::usernameExists($username)) {
            $this->errors[] = 'Ce nom d\'utilisateur est déjà utilisé.';
        }
        
        if (!empty($email) && User::emailExists($email)) {
            $this->errors[] = 'Cette adresse email est déjà utilisée.';
        }
        
        if (!empty($this->errors)) {
            $this->redirectWithErrors('/register');
            return;
        }
        
        // Créer l'utilisateur
        $hashedPassword = User::hashPassword($password);
        $user = new User($username, $email, $hashedPassword);
        
        if ($user->save()) {
            // Inscription réussie, connecter automatiquement
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['logged_in'] = true;
            $_SESSION['success'] = 'Inscription réussie ! Vous êtes maintenant connecté.';
            
            header('Location: /');
            exit;
        } else {
            $this->errors[] = 'Erreur lors de la création du compte. Veuillez réessayer.';
            $this->redirectWithErrors('/register');
        }
    }
    
    /**
     * Déconnexion
     */
    public function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
    
    /**
     * Vérifier si un utilisateur est connecté
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Obtenir l'utilisateur connecté
     */
    public static function getLoggedUser(): ?array
    {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'] ?? null,
                'username' => $_SESSION['username'] ?? null
            ];
        }
        return null;
    }
    
    /**
     * Rediriger avec les erreurs et anciennes valeurs
     */
    private function redirectWithErrors(string $url)
    {
        $_SESSION['errors'] = $this->errors;
        $_SESSION['old_input'] = $this->oldInput;
        header("Location: $url");
        exit;
    }
    
    /**
     * Obtenir les erreurs pour les vues
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Obtenir les anciennes valeurs pour les vues
     */
    public function getOldInput(): array
    {
        return $this->oldInput;
    }
}