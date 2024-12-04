<?php
// backend/api/routes/auth.php

require_once __DIR__ . '/../controllers/AuthController.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/backend/api/auth', '', $path);

$auth = new AuthController();
$requestData = json_decode(file_get_contents('php://input'), true);

switch("$method:$path") {
    case 'POST:/login':
        echo json_encode($auth->login($requestData));
        break;
        
    case 'POST:/register':
        echo json_encode($auth->register($requestData));
        break;
        
    case 'POST:/logout':
        echo json_encode($auth->logout($requestData));
        break;
        
    case 'POST:/forgot-password':
        echo json_encode($auth->forgotPassword($requestData));
        break;
        
    case 'POST:/reset-password':
        echo json_encode($auth->resetPassword($requestData));
        break;
        
    case 'GET:/user':
        echo json_encode($auth->getUser($_GET));
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route d\'authentification non trouvée']);
        break;
}
