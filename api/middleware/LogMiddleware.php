<?php

class LogMiddleware {
    private $logFile;

    public function __construct($logFile = 'api.log') {
        $this->logFile = __DIR__ . '/../../logs/' . $logFile;
    }

    public function __invoke($request, $response, $next) {
        // Temps de début de la requête
        $startTime = microtime(true);

        // Information sur la requête
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $request->getMethod(),
            'uri' => $request->getUri()->getPath(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ];

        // Si l'utilisateur est authentifié, ajoutez son ID
        if (isset($_REQUEST['user']['id'])) {
            $logData['user_id'] = $_REQUEST['user']['id'];
        }

        // Exécution de la requête
        $response = $next($request, $response);

        // Temps de fin et calcul de la durée
        $endTime = microtime(true);
        $logData['duration'] = round(($endTime - $startTime) * 1000, 2) . 'ms';
        $logData['status'] = $response->getStatusCode();

        // Formatage du message de log
        $logMessage = sprintf(
            "[%s] %s %s - Status: %d - Duration: %s - IP: %s - User Agent: %s%s\n",
            $logData['timestamp'],
            $logData['method'],
            $logData['uri'],
            $logData['status'],
            $logData['duration'],
            $logData['ip'],
            $logData['user_agent'],
            isset($logData['user_id']) ? " - User ID: {$logData['user_id']}" : ''
        );

        // Écriture dans le fichier de log
        $this->writeLog($logMessage);

        return $response;
    }

    private function writeLog($message) {
        // Création du dossier logs s'il n'existe pas
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        // Écriture du log
        file_put_contents($this->logFile, $message, FILE_APPEND);

        // Rotation des logs si le fichier devient trop grand (plus de 5MB)
        if (file_exists($this->logFile) && filesize($this->logFile) > 5 * 1024 * 1024) {
            $this->rotateLog();
        }
    }

    private function rotateLog() {
        $timestamp = date('Y-m-d_H-i-s');
        $newLogFile = str_replace('.log', "_{$timestamp}.log", $this->logFile);
        rename($this->logFile, $newLogFile);

        // Suppression des anciens logs (garde les 5 derniers)
        $logFiles = glob(dirname($this->logFile) . '/*.log');
        usort($logFiles, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        foreach (array_slice($logFiles, 5) as $file) {
            unlink($file);
        }
    }
}
