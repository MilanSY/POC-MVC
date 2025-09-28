<?php

require_once 'models/User.php';
require_once __DIR__ . '/../var/PasswordValidator.php';

/**
 * Contrôleur pour l'inscription
 */
class RegisterController
{
    private $errors = [];
    private $oldInput = [];

    /**
     * Afficher le formulaire d'inscription
     */
    public function index()
    {
        $title = "Inscription";
        $errors = $this->errors;
        $oldInput = $this->oldInput;
        include 'views/layouts/header.php';
        include 'views/auth/register.php';
        include 'views/layouts/footer.php';
    }

    /**
     * Traiter l'inscription
     */
    public function store()
    {
        $this->oldInput = $_POST;
        
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        
        if (empty($username)) {
            $this->errors[] = 'Le nom d\'utilisateur est requis.';
        } elseif (strlen($username) < 3) {
            $this->errors[] = 'Le nom d\'utilisateur doit contenir au moins 3 caractères.';
        } elseif (User::usernameExists($username)) {
            $this->errors[] = 'Ce nom d\'utilisateur existe déjà.';
        }

        if (empty($email)) {
            $this->errors[] = 'L\'adresse email est requise.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'L\'adresse email n\'est pas valide.';
        } elseif (User::emailExists($email)) {
            $this->errors[] = 'Cette adresse email existe déjà.';
        }

        if (empty($password)) {
            $this->errors[] = 'Le mot de passe est requis.';
        } else {
            $passwordValidation = PasswordValidator::validate($password, $username);
            
            if (!$passwordValidation['valid']) {
                foreach ($passwordValidation['errors'] as $error) {
                    $this->errors[] = $error;
                }
            }
        }

        if (empty($confirmPassword)) {
            $this->errors[] = 'La confirmation du mot de passe est requise.';
        } elseif ($password !== $confirmPassword) {
            $this->errors[] = 'Les mots de passe ne correspondent pas.';
        }

        if (!empty($this->errors)) {
            $this->index();
            return;
        }

        
        try {
            $passwordHash = User::hashPassword($password);
            $user = new User($username, $email, $passwordHash);
            
            if ($user->save()) {
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['logged_in'] = true;

                header('Location: /home');
                exit();
            } else {
                $this->errors[] = 'Erreur lors de la création du compte.';
            }
        } catch (Exception $e) {
            $this->errors[] = 'Erreur lors de la création du compte: ' . $e->getMessage();
        }

        $this->index();
    }
}