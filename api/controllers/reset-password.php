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

    if (!isset($data->token) || !isset($data->password)) {
        throw new Exception('Token et nouveau mot de passe requis');
    }

    // Vérifier si le token est valide et non expiré
    $stmt = $db->prepare("SELECT user_id FROM password_resets 
                         WHERE token = ? AND expiration > NOW()");
    $stmt->execute([$data->token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset) {
        throw new Exception('Token invalide ou expiré');
    }

    // Mettre à jour le mot de passe
    $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $reset['user_id']]);

    // Supprimer le token utilisé
    $stmt = $db->prepare("DELETE FROM password_resets WHERE user_id = ?");
    $stmt->execute([$reset['user_id']]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Mot de passe mis à jour avec succès'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
