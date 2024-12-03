<?php
// /api/middleware/CorsMiddleware.php

class CorsMiddleware {
    public function handleCors() {
        // Autoriser spécifiquement localhost:3000
        header('Access-Control-Allow-Origin: http://localhost:3000');
        
        // Autoriser les credentials
        header('Access-Control-Allow-Credentials: true');
        
        // Autoriser certains headers
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
        
        // Autoriser certaines méthodes HTTP
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        
        // Durée de mise en cache des résultats du contrôle en amont
        header('Access-Control-Max-Age: 3600');

        // Gérer les requêtes OPTIONS (pre-flight)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    // Vos autres méthodes restent inchangées
    public function setContentType($type = 'application/json') {
        header("Content-Type: {$type}");
    }

    public function setCustomHeaders(array $headers) {
        foreach ($headers as $header => $value) {
            header("{$header}: {$value}");
        }
    }

    public function securiseHeaders() {
        // Protection contre le clickjacking
        header('X-Frame-Options: DENY');
        
        // Protection XSS
        header('X-XSS-Protection: 1; mode=block');
        
        // Empêcher la détection MIME
        header('X-Content-Type-Options: nosniff');
        
        // Politique de sécurité du contenu - Modifiée pour permettre localhost:3000
        header("Content-Security-Policy: default-src 'self' http://localhost:3000");
        
        // Référer Policy
        header('Referrer-Policy: no-referrer-when-downgrade');
    }
}
