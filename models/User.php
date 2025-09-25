<?php

require_once __DIR__ . '/../var/Database.php';

/**
 * Classe User
 * Gère les utilisateurs et l'authentification
 */
class User
{
    private ?int $id;
    private string $username;
    private string $email;
    private string $passwordHash;
    
    /**
     * Constructeur de User
     * 
     * @param string $username Nom d'utilisateur
     * @param string $email Email de l'utilisateur
     * @param string $passwordHash Hash du mot de passe
     * @param int|null $id ID de l'utilisateur (null pour un nouvel utilisateur)
     */
    public function __construct(
        string $username, 
        string $email, 
        string $passwordHash, 
        ?int $id = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getEmail(): string { return $this->email; }
    public function getPasswordHash(): string { return $this->passwordHash; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setUsername(string $username): void { $this->username = $username; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setPasswordHash(string $passwordHash): void { $this->passwordHash = $passwordHash; }

    /**
     * Créer un nouvel utilisateur en base de données
     * 
     * @return bool Succès de l'opération
     */
    public function save(): bool
    {
        try {
            $pdo = Database::getInstance();
            
            if ($this->id === null) {
                // Nouvel utilisateur
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, password_hash) 
                    VALUES (:username, :email, :password_hash)
                ");
                $result = $stmt->execute([
                    'username' => $this->username,
                    'email' => $this->email,
                    'password_hash' => $this->passwordHash
                ]);
                
                if ($result) {
                    $this->id = $pdo->lastInsertId();
                }
                
                return $result;
            } else {
                // Mise à jour utilisateur existant
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET username = :username, email = :email, password_hash = :password_hash 
                    WHERE id = :id
                ");
                return $stmt->execute([
                    'username' => $this->username,
                    'email' => $this->email,
                    'password_hash' => $this->passwordHash,
                    'id' => $this->id
                ]);
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la sauvegarde de l'utilisateur: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Trouver un utilisateur par son nom d'utilisateur
     * 
     * @param string $username Nom d'utilisateur
     * @return User|null Utilisateur trouvé ou null
     */
    public static function findByUsername(string $username): ?User
    {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $userData = $stmt->fetch();
            
            if ($userData) {
                return new User(
                    $userData['username'],
                    $userData['email'],
                    $userData['password_hash'],
                    $userData['id']
                );
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche d'utilisateur: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Trouver un utilisateur par son email
     * 
     * @param string $email Email de l'utilisateur
     * @return User|null Utilisateur trouvé ou null
     */
    public static function findByEmail(string $email): ?User
    {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $userData = $stmt->fetch();
            
            if ($userData) {
                return new User(
                    $userData['username'],
                    $userData['email'],
                    $userData['password_hash'],
                    $userData['id']
                );
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche d'utilisateur par email: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Vérifier si un mot de passe correspond au hash
     * 
     * @param string $password Mot de passe en clair
     * @return bool Correspondance du mot de passe
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    /**
     * Hasher un mot de passe avec Argon2ID
     * 
     * @param string $password Mot de passe en clair
     * @return string Hash du mot de passe
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 itérations
            'threads' => 3          // 3 threads
        ]);
    }

    /**
     * Vérifier si un nom d'utilisateur existe déjà
     * 
     * @param string $username Nom d'utilisateur à vérifier
     * @return bool True si le nom d'utilisateur existe
     */
    public static function usernameExists(string $username): bool
    {
        return self::findByUsername($username) !== null;
    }

    /**
     * Vérifier si un email existe déjà
     * 
     * @param string $email Email à vérifier
     * @return bool True si l'email existe
     */
    public static function emailExists(string $email): bool
    {
        return self::findByEmail($email) !== null;
    }
}