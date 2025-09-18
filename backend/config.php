<?php
// backend/config.php
// Configuration centralisée pour 5F Culinary

// Configuration base de données
define('DB_CONFIG', [
    'host' => 'localhost',
    'dbname' => 'culinary_5f',
    'username' => 'culinary_user',
    'password' => 'mot_de_passe_fort', // À changer !
    'charset' => 'utf8mb4'
]);

// Configuration email
define('EMAIL_CONFIG', [
    'admin_email' => '5fculinary.food@gmail.com',
    'from_email' => 'noreply@5fculinary.local',
    'enable_notifications' => true
]);

// Configuration sécurité
define('SECURITY_CONFIG', [
    'admin_password' => 'admin5f2024', // À changer absolument !
    'session_timeout' => 3600, // 1 heure
    'max_message_length' => 2000
]);

// Fonction de connexion à la base
function getDatabase() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $config = DB_CONFIG;
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log("Erreur de connexion DB: " . $e->getMessage());
            throw new Exception("Erreur de connexion à la base de données");
        }
    }
    
    return $pdo;
}

// Fonction de validation
function validateContactData($data) {
    $errors = [];
    
    if (empty($data['name'])) {
        $errors[] = 'Le nom est requis';
    }
    
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Un email valide est requis';
    }
    
    if (empty($data['message'])) {
        $errors[] = 'Le message est requis';
    }
    
    if (strlen($data['message']) > SECURITY_CONFIG['max_message_length']) {
        $errors[] = 'Le message est trop long (max ' . SECURITY_CONFIG['max_message_length'] . ' caractères)';
    }
    
    return $errors;
}

// Fonction de nettoyage des données
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Fonction de log
function logMessage($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message\n";
    file_put_contents(__DIR__ . '/../logs/app.log', $log_entry, FILE_APPEND | LOCK_EX);
}

// Créer le dossier de logs si nécessaire
if (!file_exists(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}
?>
