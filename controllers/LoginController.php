<?php

require_once 'models/User.php';
require_once __DIR__ . '/../var/PasswordValidator.php';

/**
 * Contrôleur pour la connexion
 */
class LoginController
{
    private $errors = [];
    private $oldInput = [];

    /**
     * Afficher le formulaire de connexion
     */
    public function index()
    {
        $title = "Connexion";
        $errors = $this->errors;
        $oldInput = $this->oldInput;
        include 'views/layouts/header.php';
        include 'views/auth/login.php';
        include 'views/layouts/footer.php';
    }

    /**
     * Traiter la connexion
     */
    public function store()
    {
        $this->oldInput = $_POST;
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email)) {
            $this->errors[] = 'L\'adresse email est requise.';
        }

        if (empty($password)) {
            $this->errors[] = 'Le mot de passe est requis.';
        }

        if (!empty($this->errors)) {
            $this->index();
            return;
        }

        $user = User::findByEmail($email);

        if (!$user || !$user->verifyPassword($password)) {
            $this->errors[] = 'Adresse email ou mot de passe incorrect.';
            $this->index();
            return;
        }

        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['logged_in'] = true;

        header('Location: /home');
        exit();
    }
}