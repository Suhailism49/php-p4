<?php
/**
 * Configuratiebestand voor het Ziekmeldsysteem
 * Bevat database-instellingen en algemene configuratie
 * 
 * Auteur: [Suhail Ismaili]
 * Datum: 2025-06-12
 */

// Database configuratie
define('DB_HOST', 'localhost');
define('DB_NAME', 'ziekmeldsysteem');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Algemene configuratie
define('SITE_TITLE', 'Ziekmeldsysteem Docenten');
define('SITE_URL', 'http://localhost/ziekmeldsysteem');

// Tijdzone instellen
date_default_timezone_set('Europe/Amsterdam');

// Error reporting (in productie uitschakelen)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Database connectie klasse
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database connectie mislukt: " . $e->getMessage());
        }
    }
    
    /**
     * Singleton pattern voor database connectie
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Krijg de PDO connectie
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }
}

/**
 * Hulpfuncties
 */

/**
 * HTML escape functie voor veilige output
 * @param string $string
 * @return string
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Datum formatteren naar Nederlandse notatie
 * @param string $date
 * @return string
 */
function formatDatum($date) {
    if (empty($date)) return 'Onbekend';
    $datum = new DateTime($date);
    return $datum->format('d-m-Y');
}

/**
 * Datum formatteren naar Nederlandse notatie met tijd
 * @param string $datetime
 * @return string
 */
function formatDatumTijd($datetime) {
    if (empty($datetime)) return 'Onbekend';
    $datum = new DateTime($datetime);
    return $datum->format('d-m-Y H:i');
}

/**
 * Validatie functie voor email
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validatie functie voor datum
 * @param string $date
 * @return bool
 */
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}
?>