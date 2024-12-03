<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Charger la configuration
require_once __DIR__ . '/api/config.php';

try {
    // Inclure les fichiers nécessaires
    require_once __DIR__ . '/api/connexion.php';
    require_once __DIR__ . '/api/middleware/CorsMiddleware.php';
    require_once __DIR__ . '/api/middleware/AuthMiddleware.php';
    require_once __DIR__ . '/api/middleware/LogMiddleware.php';
    require_once __DIR__ . '/api/middleware/ValidationMiddleware.php';

    // Initialiser les middlewares
    $corsMiddleware = new CorsMiddleware();
    $authMiddleware = new AuthMiddleware();
    $logMiddleware = new LogMiddleware();
    $validationMiddleware = new ValidationMiddleware();

    // Appliquer CORS
    $corsMiddleware->handleCors();

    // Obtenir l'URL demandée
    $request_uri = $_SERVER['REQUEST_URI'];
    
    // Router simple pour test
    echo json_encode([
        'status' => 'success',
        'message' => 'API fonctionnelle',
        'uri' => $request_uri
    ]);

} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
