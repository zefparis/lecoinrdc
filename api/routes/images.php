<?php
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/backend/api/images', '', $path);

switch("$method:$path") {
    case 'GET:/':
        require_once __DIR__ . '/../controllers/images.php';
        break;
        
    case 'POST:/upload':
        require_once __DIR__ . '/../controllers/upload.php';
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route d\'images non trouvée']);
        break;
}
