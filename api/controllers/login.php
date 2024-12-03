<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../config/database.php';

// Récupérer les données POST
$data = json_decode(file_get_contents("php://input"));

if(isset($data->email) && isset($data->password)) {
    $email = $data->email;
    $password = $data->password;
    
    try {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row['password'])) {
                // Générer un token JWT ou une session
                $token = bin2hex(random_bytes(32)); // Exemple simple
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Connexion réussie',
                    'token' => $token,
                    'user' => [
                        'id' => $row['id'],
                        'email' => $row['email'],
                        'name' => $row['name']
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Mot de passe incorrect'
                ]);
            }
        } else {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Utilisateur non trouvé'
            ]);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur de base de données: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Données manquantes'
    ]);
}
?>
