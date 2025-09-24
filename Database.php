<?php

/**
 * Classe Database
 * Gère la connexion à la base de données MySQL
 */

require_once 'DatabaseParam.php';

class Database
{
    private static ?PDO $instance = null;

    /**
     * Obtenir l'instance de connexion PDO
     * 
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            global $host, $dbname, $username, $password;
            try {
                self::$instance = new PDO(
                    "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données: " . $e->getMessage());
            }
        }
        
        return self::$instance;
    }
}