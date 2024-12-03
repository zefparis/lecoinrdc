<?php
// /api/models/User.php

class User {
    private $conn;
    private $table = 'users';

    // Propriétés
    public $id;
    public $username;
    public $email;
    public $password;
    public $created_at;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer un nouvel utilisateur
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (username, email, password, created_at) 
                  VALUES (:username, :email, :password, NOW())";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // Bind des paramètres
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Vérifier si l'email existe déjà
    public function emailExists() {
        $query = "SELECT id, username, password FROM " . $this->table . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            return true;
        }
        return false;
    }

    // Mettre à jour le mot de passe
    public function updatePassword() {
        $query = "UPDATE " . $this->table . " 
                  SET password = :password 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
}
