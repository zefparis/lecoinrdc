<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

try {
    // Connexion à la base de données immo-express
    $pdo = new PDO(
        "mysql:host=localhost;dbname=immo-express",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérification des données requises
        if(isset($_FILES['image']) && isset($_POST['title']) && isset($_POST['user_id'])) {
            $upload_dir = '../uploads/';
            
            // Création du dossier uploads s'il n'existe pas
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $max_size = 5 * 1024 * 1024; // 5MB

            // Vérifications de sécurité
            if(!in_array($file_ext, $allowed_types)) {
                http_response_code(400);
                echo json_encode([
                    "status" => "error", 
                    "message" => "Type de fichier non autorisé. Types acceptés : " . implode(', ', $allowed_types)
                ]);
                exit();
            }

            if($_FILES['image']['size'] > $max_size) {
                http_response_code(400);
                echo json_encode([
                    "status" => "error", 
                    "message" => "Le fichier est trop volumineux. Taille maximum : 5MB"
                ]);
                exit();
            }

            // Génération d'un nom de fichier unique
            $new_filename = uniqid('img_') . '.' . $file_ext;
            $target_file = $upload_dir . $new_filename;

            // Upload du fichier
            if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Insertion dans la base de données
                $query = "INSERT INTO images (title, filename, user_id, description, price, location) 
                         VALUES (:title, :filename, :user_id, :description, :price, :location)";
                
                $stmt = $pdo->prepare($query);
                
                $stmt->bindParam(':title', $_POST['title']);
                $stmt->bindParam(':filename', $new_filename);
                $stmt->bindParam(':user_id', $_POST['user_id']);
                $stmt->bindParam(':description', $_POST['description'] ?? null);
                $stmt->bindParam(':price', $_POST['price'] ?? null);
                $stmt->bindParam(':location', $_POST['location'] ?? null);

                if($stmt->execute()) {
                    http_response_code(201);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Image uploadée avec succès",
                        "data" => [
                            "filename" => $new_filename,
                            "title" => $_POST['title'],
                            "id" => $pdo->lastInsertId()
                        ]
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode([
                        "status" => "error", 
                        "message" => "Erreur lors de l'enregistrement dans la base de données"
                    ]);
                }
            } else {
                http_response_code(500);
                echo json_encode([
                    "status" => "error", 
                    "message" => "Erreur lors de l'upload du fichier"
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error", 
                "message" => "Données manquantes. Title, image et user_id sont requis"
            ]);
        }
    }

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Erreur de connexion : " . $e->getMessage()
    ]);
}
?>
