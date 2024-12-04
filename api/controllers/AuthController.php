<?php
// backend/api/controllers/AuthController.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/User.php';
require 'vendor/autoload.php'; // Assurez-vous que l'autoload de Composer est inclus

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class AuthController {
    private $db;
    private $user;
    private $secretKey = 'votre_cle_secrete'; // Utilisez une clé secrète forte

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
        $payload = [
            'id' => $user['id'],
            'email' => $user['email'],
            'exp' => time() + (60 * 60 * 24) // 24 heures
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    private function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
}
