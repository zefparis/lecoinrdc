<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

try {
    // Connexion à la base de données lecoinrdc
    $pdo = new PDO(
        "mysql:host=localhost;dbname=lecoinrdc",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    $method = $_SERVER['REQUEST_METHOD'];

    if($method === 'GET') {
        // Requête pour récupérer les images avec les informations utilisateur
        $query = "SELECT i.*, u.username 
                 FROM images i 
                 JOIN users u ON i.user_id = u.id 
                 ORDER BY i.created_at DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        
        $images = [];
        
        while($row = $stmt->fetch()) {
            $images[] = [
                "id" => $row['id'],
                "title" => $row['title'],
                "filename" => $row['filename'],
                "description" => $row['description'], // Ajouté si vous avez ce champ
                "price" => $row['price'],           // Ajouté si vous avez ce champ
                "location" => $row['location'],     // Ajouté si vous avez ce champ
                "username" => $row['username'],
                "created_at" => $row['created_at']
            ];
        }
        
        if(count($images) > 0) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "data" => $images
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Aucune image trouvée"
            ]);
        }
    } else {
        http_response_code(405);
        echo json_encode([
            "status" => "error",
            "message" => "Méthode non autorisée"
        ]);
    }

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Erreur de connexion : " . $e->getMessage()
    ]);
}

// Ajouter une validation du token JWT
if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Non autorisé"
    ]);
    exit;
}

// Ajouter une pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

$query .= " LIMIT :limit OFFSET :offset";
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

// Ajouter des filtres
if (isset($_GET['search'])) {
    $query .= " WHERE title LIKE :search OR description LIKE :search";
    $stmt->bindValue(':search', '%'.$_GET['search'].'%');
}
?>
