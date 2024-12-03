<?php
header('Content-Type: application/json');

function getAuthorizationHeader() {
    $headers = null;
    
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER['Authorization']);
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    
    return $headers;
}

function getBearerToken() {
    $headers = getAuthorizationHeader();
    
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

// Récupérer le token
$token = getBearerToken();

if (!$token) {
    http_response_code(401);
    echo json_encode([
        'error' => 'Token non fourni ou format invalide',
        'headers' => getallheaders() // Pour le débogage
    ]);
    exit;
}

// Vérifier le token (à adapter selon votre logique)
try {
    // Votre logique de vérification du token
    // ...
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Token valide',
        'user' => [
            // Informations de l'utilisateur
        ]
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'error' => 'Token invalide',
        'message' => $e->getMessage()
    ]);
}
