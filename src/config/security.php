<?php
/**
 * Security Configuration File
 * Contains security-related settings and functions for the application
 */

// Security Headers Configuration
define('SECURITY_HEADERS', [
    'X-Frame-Options' => 'DENY',
    'X-Content-Type-Options' => 'nosniff',
    'X-XSS-Protection' => '1; mode=block',
    'Referrer-Policy' => 'strict-origin-when-cross-origin',
    'Content-Security-Policy' => "default-src 'self'; script-src 'self' https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com;"
]);

// Rate Limiting Configuration
define('LOGIN_ATTEMPTS_LIMIT', 5);
define('LOGIN_BLOCK_TIME', 300); // 5 minutes in seconds

// Session Configuration
define('SESSION_TIMEOUT', 86400); // 24 hours in seconds
define('SESSION_REGENERATE_INTERVAL', 1800); // 30 minutes in seconds

/**
 * Apply security headers
 */
function applySecurityHeaders() {
    foreach (SECURITY_HEADERS as $header => $value) {
        header("$header: $value");
    }
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * @param string $token
 * @return bool
 */
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input data
 * @param mixed $data
 * @return mixed
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Escape output for HTML context
 * @param string $output
 * @return string
 */
function escapeOutput($output) {
    return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
}

/**
 * Check and enforce rate limiting
 * @param string $key Rate limit key (e.g., 'login_attempts')
 * @param int $limit Maximum allowed attempts
 * @param int $timeout Timeout period in seconds
 * @return bool True if rate limit exceeded
 */
function checkRateLimit($key, $limit, $timeout) {
    $attempts = $_SESSION[$key] ?? 0;
    $lastAttempt = $_SESSION["{$key}_time"] ?? 0;
    
    if ($attempts >= $limit && (time() - $lastAttempt) < $timeout) {
        return true;
    }
    return false;
}

/**
 * Record rate limited attempt
 * @param string $key Rate limit key
 */
function recordRateLimitAttempt($key) {
    $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
    $_SESSION["{$key}_time"] = time();
}

/**
 * Reset rate limiting for a key
 * @param string $key Rate limit key
 */
function resetRateLimit($key) {
    unset($_SESSION[$key]);
    unset($_SESSION["{$key}_time"]);
}

/**
 * Validate email format
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Secure password hashing
 * @param string $password
 * @return string
 */
function securePasswordHash($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}
