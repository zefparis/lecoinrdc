// backend/api/controllers/AuthController.php

<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->user = new User();
    }

    public function login($request) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            return json_encode(['error' => 'Email et mot de passe requis']);
        }

        $email = $data['email'];
        $password = $data['password'];

        try {
            $user = $this->user->findByEmail($email);
            
            if (!$user || !password_verify($password, $user['password'])) {
                http_response_code(401);
                return json_encode(['error' => 'Identifiants invalides']);
            }

            // Générer un token JWT
            $token = $this->generateToken($user);
            
            unset($user['password']); // Ne pas renvoyer le mot de passe

            return json_encode([
                'token' => $token,
                'user' => $user
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(['error' => 'Erreur serveur']);
        }
    }

    public function register($request) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
            http_response_code(400);
            return json_encode(['error' => 'Données manquantes']);
        }

        try {
            // Vérifier si l'email existe déjà
            if ($this->user->findByEmail($data['email'])) {
                http_response_code(400);
                return json_encode(['error' => 'Email déjà utilisé']);
            }

            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT)
            ];

            $newUser = $this->user->create($userData);
            
            if ($newUser) {
                $token = $this->generateToken($newUser);
                unset($newUser['password']);

                return json_encode([
                    'token' => $token,
                    'user' => $newUser
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(['error' => 'Erreur serveur']);
        }
    }

    public function logout($request) {
        // Invalidation du token si nécessaire
        return json_encode(['message' => 'Déconnexion réussie']);
    }

    public function getUser($request) {
        try {
            // Récupérer l'utilisateur à partir du token
            $headers = getallheaders();
            $token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
            
            if (!$token) {
                http_response_code(401);
                return json_encode(['error' => 'Non autorisé']);
            }

            $userData = $this->validateToken($token);
            if (!$userData) {
                http_response_code(401);
                return json_encode(['error' => 'Token invalide']);
            }

            $user = $this->user->findById($userData['id']);
            unset($user['password']);

            return json_encode($user);

        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(['error' => 'Erreur serveur']);
        }
    }

    private function generateToken($user) {
        // Implémentez votre logique de génération de token JWT ici
        // Vous pouvez utiliser une bibliothèque comme firebase/php-jwt
        $payload = [
            'id' => $user['id'],
            'email' => $user['email'],
            'exp' => time() + (60 * 60 * 24) // 24 heures
        ];

        // Retournez le token généré
        return "token_example"; // À remplacer par votre implémentation
    }

    private function validateToken($token) {
        // Implémentez votre logique de validation de token JWT ici
        // Retournez les données de l'utilisateur si le token est valide
        return; // À remplacer par votre implémentation
    }
}
