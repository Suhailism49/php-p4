<?php
/**
 * Database configuratie voor nieuwswebsite
 * Plaats dit bestand in: C:\xampp\htdocs\nieuwswebsite1\config\database.php
 */

// Error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

class DatabaseConfig {
    // Database instellingen - PAS DEZE AAN NAAR JOUW SITUATIE
    private const DB_HOST = 'localhost';
    private const DB_NAME = 'nieuwswebsite';
    private const DB_USER = 'root';
    private const DB_PASS = '';        // Meestal leeg bij XAMPP
    private const DB_CHARSET = 'utf8mb4';
    
    private static $instance = null;
    private $pdo;
    
    /**
     * Singleton pattern voor database connectie
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=" . self::DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::DB_CHARSET
            ];
            
            $this->pdo = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connectie gefaald: " . $e->getMessage() . "<br><br>
                <strong>Controleer:</strong><br>
                1. Is MySQL gestart in XAMPP?<br>
                2. Bestaat database 'nieuwswebsite'?<br>
                3. Zijn de instellingen in dit bestand correct?");
        }
    }
    
    /**
     * Verkrijg database instance (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Verkrijg PDO verbinding
     */
    public function getConnection() {
        return $this->pdo;
    }
    
    /**
     * Voorkom cloning van de singleton
     */
    private function __clone() {}
    
    /**
     * Voorkom unserialization van de singleton
     */
    public function __wakeup() {}
}

/**
 * Handige functie om database connectie te krijgen
 */
function getDB() {
    return DatabaseConfig::getInstance()->getConnection();
}

/**
 * Database utilities
 */
class DatabaseUtils {
    
    /**
     * Escape HTML output voor veiligheid
     */
    public static function escape($data) {
        if (is_array($data)) {
            return array_map([self::class, 'escape'], $data);
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valideer email format
     */
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Genereer veilige hash voor wachtwoorden
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verificeer wachtwoord tegen hash
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Genereer unieke session ID
     */
    public static function generateSessionId() {
        return bin2hex(random_bytes(16));
    }
    
    /**
     * Sanitize string input
     */
    public static function sanitizeString($input) {
        return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
    }
    
    /**
     * Valideer integer input
     */
    public static function validateInt($input) {
        return filter_var($input, FILTER_VALIDATE_INT);
    }
}

// Test de verbinding direct
try {
    $testDB = getDB();
    // Als we hier komen, werkt de verbinding
} catch (Exception $e) {
    echo "<div style='background:#ffe6e6;padding:20px;border:1px solid #ff0000;margin:20px;border-radius:5px;'>";
    echo "<h3>🚨 Database Verbinding Fout</h3>";
    echo "<p><strong>Foutmelding:</strong> " . $e->getMessage() . "</p>";
    echo "<h4>Controleer:</h4>";
    echo "<ul>";
    echo "<li>Is XAMPP gestart? (Apache + MySQL)</li>";
    echo "<li>Bestaat database 'nieuwswebsite' in phpMyAdmin?</li>";
    echo "<li>Zijn de database instellingen correct in dit bestand?</li>";
    echo "</ul>";
    echo "<p><a href='http://localhost/phpmyadmin' target='_blank'>Open phpMyAdmin</a></p>";
    echo "</div>";
}
?>