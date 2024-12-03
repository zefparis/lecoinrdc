<?php
class AuthMiddleware {
    private $secret;

    public function __construct() {
        $this->secret = JWT_SECRET;
        if (!$this->secret) {
            throw new Exception('JWT_SECRET non défini');
        }
    }

    public function verifyToken($token) {
        // Votre logique de vérification du token
    }
}
