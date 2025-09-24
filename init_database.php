<?php
/**
 * Script d'initialisation de la base de données
 * Crée la base de données et execute le script SQL
 */

require_once 'DatabaseParam.php';

try {
    // Configuration de la base de données depuis DatabaseParam.php
    echo "🔄 Connexion au serveur MySQL...\n";
    
    // Connexion au serveur MySQL (sans spécifier de base de données)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "✅ Connexion réussie au serveur MySQL\n";

    // Créer la base de données si elle n'existe pas
    echo "🔄 Création de la base de données '$dbname'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Base de données '$dbname' créée ou existe déjà\n";

    // Se connecter à la base de données
    $pdo->exec("USE $dbname");
    
    // Lire et exécuter le fichier SQL
    echo "🔄 Lecture du fichier database.sql...\n";
    $sqlContent = file_get_contents('database.sql');
    
    if ($sqlContent === false) {
        throw new Exception("Impossible de lire le fichier database.sql");
    }

    echo "🔄 Exécution du script SQL...\n";
    
    // Améliorer le parsing des requêtes SQL
    // Supprimer les commentaires en début de ligne
    $lines = explode("\n", $sqlContent);
    $cleanedLines = [];
    
    foreach ($lines as $line) {
        $trimmedLine = trim($line);
        // Ignorer les lignes vides et les commentaires
        if (!empty($trimmedLine) && !preg_match('/^\s*--/', $trimmedLine)) {
            $cleanedLines[] = $line;
        }
    }
    
    $cleanedContent = implode("\n", $cleanedLines);
    
    // Diviser en requêtes en utilisant le point-virgule suivi d'une nouvelle ligne
    $queries = preg_split('/;\s*\n\s*/', $cleanedContent);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            try {
                $pdo->exec($query);
                // Optionnel: afficher le début de chaque requête exécutée
                $queryStart = substr(preg_replace('/\s+/', ' ', $query), 0, 50);
                echo "   ✓ " . $queryStart . "...\n";
            } catch (PDOException $e) {
                echo "⚠️  Erreur lors de l'exécution d'une requête: " . $e->getMessage() . "\n";
                echo "Requête: " . substr($query, 0, 200) . "...\n";
                // Continue avec les autres requêtes
            }
        }
    }

    echo "✅ Script SQL exécuté avec succès\n";

    // Vérifier que les tables ont été créées
    echo "🔄 Vérification des tables créées...\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📋 Tables créées:\n";
    foreach ($tables as $table) {
        echo "   - $table\n";
    }

    // Compter les données
    echo "\n📊 Statistiques des données:\n";
    foreach (['medias', 'books', 'movies', 'albums', 'songs', 'users'] as $table) {
        if (in_array($table, $tables)) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "   - $table: $count enregistrement(s)\n";
        }
    }
    
    // Vérifications spéciales pour l'authentification
    if (in_array('users', $tables)) {
        echo "\n🔐 Système d'authentification:\n";
        echo "   ✅ Table 'users' créée\n";
        echo "   🌐 Inscription: http://localhost:8000/register\n";
        echo "   🔑 Connexion: http://localhost:8000/login\n";
    }

    echo "\n🎉 Initialisation de la base de données terminée avec succès!\n";
    echo "Vous pouvez maintenant utiliser l'application sur http://localhost:8000\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Assurez-vous que MySQL est démarré et que les paramètres de connexion sont corrects.\n";
    exit(1);
}