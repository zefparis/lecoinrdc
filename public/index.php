<?php
// Configuration de base
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Définition des constantes
define('PUBLIC_PATH', __DIR__);
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('ASSETS_PATH', PUBLIC_PATH . '/assets');

// Vérification des dossiers requis
$requiredDirs = [
    UPLOAD_PATH,
    UPLOAD_PATH . '/products',
    UPLOAD_PATH . '/profiles',
    UPLOAD_PATH . '/temp'
];

foreach ($requiredDirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Fonction pour gérer les uploads de fichiers
function handleFileUpload($file, $destination, $allowedTypes = ['image/jpeg', 'image/png']) {
    try {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Paramètres invalides.');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('Aucun fichier envoyé.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Dépassement de la taille limite.');
            default:
                throw new RuntimeException('Erreur inconnue.');
        }

        if (!in_array($file['type'], $allowedTypes)) {
            throw new RuntimeException('Type de fichier non autorisé.');
        }

        $fileName = sprintf('%s-%s.%s',
            uniqid(),
            hash('sha256', random_bytes(16)),
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        $filePath = $destination . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new RuntimeException('Échec lors du déplacement du fichier.');
        }

        return [
            'success' => true,
            'path' => $filePath,
            'filename' => $fileName
        ];

    } catch (RuntimeException $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Fonction pour nettoyer les fichiers temporaires
function cleanTempFiles($tempDir, $maxAge = 3600) {
    if ($handle = opendir($tempDir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $filePath = $tempDir . '/' . $file;
                if (is_file($filePath) && (time() - filemtime($filePath) > $maxAge)) {
                    unlink($filePath);
                }
            }
        }
        closedir($handle);
    }
}

// Nettoyage automatique des fichiers temporaires
cleanTempFiles(UPLOAD_PATH . '/temp');

// Headers de sécurité
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
