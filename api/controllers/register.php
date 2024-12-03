<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->username) || !isset($data->email) || !isset($data->password)) {
        throw new Exception('Tous les champs sont requis');
    }

    // Vérifier si l'email existe déjà
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data->email]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('Cet email est déjà utilisé');
    }

    // Vérifier si le username existe déjà
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$data->username]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('Ce nom d\'utilisateur est déjà utilisé');
    }

    // Hash du mot de passe
    $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);

    // Insérer le nouvel utilisateur
    $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$data->username, $data->email, $hashedPassword]);
    $userId = $db->lastInsertId();

    // Générer un token
    $token = bin2hex(random_bytes(32));
    $expiration = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Insérer le token
    $stmt = $db->prepare("INSERT INTO user_tokens (user_id, token, expiration) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $token, $expiration]);

    // Retourner la réponse
    echo json_encode([
        'status' => 'success',
        'message' => 'Inscription réussie',
        'user' => [
            'id' => $userId,
            'username' => $data->username,
            'email' => $data->email
        ],
        'token' => $token
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
