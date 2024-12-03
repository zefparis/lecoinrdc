<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure le fichier de configuration
require_once __DIR__ . '/api/config.php';

// Fonction pour tester la connexion
function testerConnexion() {
    try {
        // Tentative de connexion à la base de données
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        // Test simple de la connexion
        $pdo->query('SELECT 1');

        // Retourner le résultat en JSON
        header('Content-Type: application/json');
        echo json_encode([
            'statut' => 'succès',
            'message' => 'Connexion réussie à la base de données',
            'configuration' => [
                'hote' => DB_HOST,
                'base_de_donnees' => DB_NAME,
                'utilisateur' => DB_USER
            ]
        ]);

    } catch (PDOException $e) {
        // En cas d'erreur, retourner l'erreur en JSON
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'statut' => 'erreur',
            'message' => 'Erreur de connexion à la base de données',
            'erreur' => $e->getMessage(),
            'configuration' => [
                'hote' => DB_HOST,
                'base_de_donnees' => DB_NAME,
                'utilisateur' => DB_USER
            ]
        ]);
    }
}

// Exécuter le test
testerConnexion();
