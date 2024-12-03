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

    if (!isset($data->email)) {
        throw new Exception('Email requis');
    }

    // Vérifier si l'utilisateur existe
    $stmt = $db->prepare("SELECT id, username, email FROM users WHERE email = ?");
    $stmt->execute([$data->email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('Aucun compte associé à cet email');
    }

    // Générer un token de réinitialisation
    $token = bin2hex(random_bytes(32));
    $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Supprimer les anciens tokens de réinitialisation
    $stmt = $db->prepare("DELETE FROM password_resets WHERE user_id = ?");
    $stmt->execute([$user['id']]);

    // Insérer le nouveau token
    $stmt = $db->prepare("INSERT INTO password_resets (user_id, token, expiration) VALUES (?, ?, ?)");
    $stmt->execute([$user['id'], $token, $expiration]);

    // Envoyer l'email
    $resetLink = "http://localhost:5173/reset-password?token=" . $token;
    $to = $user['email'];
    $subject = "Réinitialisation de votre mot de passe";
    $message = "Bonjour " . $user['username'] . ",\n\n";
    $message .= "Vous avez demandé la réinitialisation de votre mot de passe. ";
    $message .= "Cliquez sur le lien suivant pour définir un nouveau mot de passe :\n\n";
    $message .= $resetLink . "\n\n";
    $message .= "Ce lien expirera dans 1 heure.\n\n";
    $message .= "Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.";
    
    $headers = 'From: noreply@votresite.com' . "\r\n";

    mail($to, $subject, $message, $headers);

    echo json_encode([
        'status' => 'success',
        'message' => 'Si un compte existe avec cet email, un lien de réinitialisation a été envoyé.'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
