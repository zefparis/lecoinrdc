<?php
// backend/api/routes/auth.php

require_once __DIR__ . '/../controllers/AuthController.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/backend/api/auth', '', $path);

$auth = new AuthController();

switch("$method:$path") {
    case 'POST:/login':
        echo $auth->login($_REQUEST);
        break;
        
    case 'POST:/register':
        echo $auth->register($_REQUEST);
        break;
        
    case 'POST:/logout':
        echo $auth->logout($_REQUEST);
        break;
        
    case 'POST:/forgot-password':
        echo $auth->forgotPassword($_REQUEST);
        break;
        
    case 'POST:/reset-password':
        echo $auth->resetPassword($_REQUEST);
        break;
        
    case 'GET:/user':
        echo $auth->getUser($_REQUEST);
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route d\'authentification non trouvée']);
        break;
}
