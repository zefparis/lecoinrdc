<?php
// /api/models/Image.php

class Image {
    private $conn;
    private $table = 'images';

    // Propriétés
    public $id;
    public $user_id;
    public $filename;
    public $filepath;
    public $created_at;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer une nouvelle image
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, filename, filepath, created_at) 
                  VALUES (:user_id, :filename, :filepath, NOW())";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->filename = htmlspecialchars(strip_tags($this->filename));
        $this->filepath = htmlspecialchars(strip_tags($this->filepath));

        // Bind des paramètres
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':filename', $this->filename);
        $stmt->bindParam(':filepath', $this->filepath);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Récupérer les images d'un utilisateur
    public function getUserImages() {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt;
    }

    // Supprimer une image
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }
}
