<?php
// backend/api/routes/auth.php

// Vérification de l'accès direct au fichier
if (!defined('ACCESS_GRANTED')) {
    die('Accès direct au fichier non autorisé');
}

// Import des contrôleurs nécessaires
require_once __DIR__ . '/../controllers/login.php';
require_once __DIR__ . '/../controllers/register.php';
require_once __DIR__ . '/../controllers/forgot-password.php';
require_once __DIR__ . '/../controllers/reset-password.php';

// Routes pour les utilisateurs
$app->group('/api/users', function() use ($app) {
    // GET - Récupérer le profil de l'utilisateur connecté
    $app->get('/profile', function($request, $response) {
        return getUserProfile($request, $response);
    })->add(new AuthMiddleware());

    // PUT - Mettre à jour le profil utilisateur
    $app->put('/profile', function($request, $response) {
        return updateUserProfile($request, $response);
    })->add(new AuthMiddleware());

    // GET - Récupérer tous les utilisateurs (admin seulement)
    $app->get('', function($request, $response) {
        return getAllUsers($request, $response);
    })->add(new AuthMiddleware());

    // GET - Récupérer un utilisateur spécifique
    $app->get('/{id}', function($request, $response, $args) {
        return getUserById($request, $response, $args);
    })->add(new AuthMiddleware());

    // DELETE - Supprimer un compte utilisateur
    $app->delete('/{id}', function($request, $response, $args) {
        return deleteUser($request, $response, $args);
    })->add(new AuthMiddleware());

    // PUT - Mettre à jour le mot de passe
    $app->put('/password', function($request, $response) {
        return updatePassword($request, $response);
    })->add(new AuthMiddleware());

    // POST - Vérifier le token d'authentification
    $app->post('/verify-token', function($request, $response) {
        return verifyToken($request, $response);
    });
});

// Routes publiques (sans authentification)
$app->group('/api/auth', function() use ($app) {
    // POST - Inscription
    $app->post('/register', function($request, $response) {
        return register($request, $response);
    });

    // POST - Connexion
    $app->post('/login', function($request, $response) {
        return login($request, $response);
    });

    // POST - Mot de passe oublié
    $app->post('/forgot-password', function($request, $response) {
        return forgotPassword($request, $response);
    });

    // POST - Réinitialisation du mot de passe
    $app->post('/reset-password', function($request, $response) {
        return resetPassword($request, $response);
    });
});
