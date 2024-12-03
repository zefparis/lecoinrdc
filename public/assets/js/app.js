// Configuration globale
const CONFIG = {
    API_URL: '/api',
    UPLOAD_MAX_SIZE: 5242880, // 5MB
    ALLOWED_FILE_TYPES: ['image/jpeg', 'image/png']
};

// Gestionnaire d'erreurs global
window.onerror = function(msg, url, lineNo, columnNo, error) {
    console.error('Erreur:', msg, 'URL:', url, 'Ligne:', lineNo);
    return false;
};

// Utilitaires
const utils = {
    formatDate: (date) => {
        return new Date(date).toLocaleDateString('fr-FR');
    },
    
    validateEmail: (email) => {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },
    
    sanitizeInput: (input) => {
        return input.replace(/<[^>]*>/g, '');
    }
};

// Gestionnaire de fichiers
const fileHandler = {
    validateFile: (file) => {
        if (file.size > CONFIG.UPLOAD_MAX_SIZE) {
            throw new Error('Fichier trop volumineux');
        }
        if (!CONFIG.ALLOWED_FILE_TYPES.includes(file.type)) {
            throw new Error('Type de fichier non autorisé');
        }
        return true;
    }
};
