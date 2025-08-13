<?php
/**
 * Database Configuration for 84 Days Marathon Praise & Prayer
 * 
 * XAMPP Local Development Configuration
 */

class Database {
    private $connection;

    public function __construct() {
        $this->connection = getDatabaseConnection();
    }

    public function getConnection() {
        return $this->connection;
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollback() {
        return $this->connection->rollback();
    }

    public function prepare($statement) {
        return $this->connection->prepare($statement);
    }

    public function query($statement) {
        return $this->connection->query($statement);
    }
}

// Database configuration - XAMPP LOCAL SETUP
define('DB_HOST', 'localhost');           // XAMPP MySQL host
define('DB_NAME', 'mpp_v10');             // Correct database name
define('DB_USER', 'root');                // XAMPP default MySQL username
define('DB_PASS', '');                    // XAMPP default MySQL password (empty)
define('DB_CHARSET', 'utf8mb4');
define('DB_SOCKET', '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock'); // XAMPP MySQL socket

// Error reporting (enable for development)
define('DB_DEBUG', true); // Set to false in production

/**
 * Get PDO database connection
 * 
 * @return PDO Database connection instance
 * @throws Exception If connection fails
 */
function getDatabaseConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            // Try socket connection first for XAMPP
            $dsn = "mysql:unix_socket=" . DB_SOCKET . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
                PDO::ATTR_TIMEOUT => 10, // Connection timeout
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            if (DB_DEBUG) {
                error_log("Database connection successful");
            }
            
        } catch (PDOException $e) {
            // Log the error
            error_log("Database connection failed: " . $e->getMessage());
            
            // Don't expose sensitive information in production
            if (DB_DEBUG) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            } else {
                throw new Exception("Database connection failed. Please check your configuration.");
            }
        }
    }
    
    return $pdo;
}

/**
 * Test database connection
 * 
 * @return bool True if connection successful
 */
function testDatabaseConnection() {
    try {
        $pdo = getDatabaseConnection();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Initialize database tables if they don't exist
 */
function initializeDatabase() {
    try {
        $pdo = getDatabaseConnection();
        
        // Check if tables exist
        $tables = ['prayer_signups', 'volunteer_registrations', 'admin_users', 'newsletter_subscriptions'];
        $existingTables = [];
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                $existingTables[] = $table;
            }
        }
        
        return $existingTables;
        
    } catch (Exception $e) {
        error_log("Database initialization check failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Sanitize input data
 * 
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate email format
 * 
 * @param string $email Email address to validate
 * @return bool True if valid email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate secure password hash
 * 
 * @param string $password Plain text password
 * @return string Hashed password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password against hash
 * 
 * @param string $password Plain text password
 * @param string $hash Stored password hash
 * @return bool True if password matches
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate UUID for database records
 * 
 * @return string UUID string
 */
function generateUUID() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}
?>
