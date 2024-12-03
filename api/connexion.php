<?php
// Paramètres de connexion à la base de données
$host = 'localhost';     // L'hôte de la base de données
$dbname = 'lecoinrdc';   // Le nom de votre base de données
$username = 'root';      // Votre nom d'utilisateur MySQL
$password = '';          // Votre mot de passe MySQL

try {
    // Tentative de connexion à la base de données
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Si la connexion réussit
    echo "<h2 style='color: green;'>✅ Connexion réussie à la base de données</h2>";
    
    // Informations supplémentaires sur la connexion
    echo "<h3>Informations sur la connexion :</h3>";
    echo "<ul>";
    echo "<li>Nom de la base de données : $dbname</li>";
    echo "<li>Version MySQL : " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "</li>";
    echo "<li>Jeu de caractères : utf8mb4</li>";
    echo "</ul>";

} catch(PDOException $e) {
    // En cas d'erreur
    echo "<h2 style='color: red;'>❌ Erreur de connexion</h2>";
    echo "<p>Message d'erreur : " . $e->getMessage() . "</p>";
    
    // Suggestions de dépannage
    echo "<h3>Vérifications à faire :</h3>";
    echo "<ul>";
    echo "<li>Le serveur MySQL est-il démarré ?</li>";
    echo "<li>Les identifiants sont-ils corrects ?</li>";
    echo "<li>La base de données '$dbname' existe-t-elle ?</li>";
    echo "<li>L'utilisateur a-t-il les droits nécessaires ?</li>";
    echo "</ul>";
}
?>
