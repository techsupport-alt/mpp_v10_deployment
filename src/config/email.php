<?php
/**
 * Email Configuration for 84 Days Marathon Praise & Prayer
 * 
 * Update these values with your actual email server settings
 * For Hostinger: Check your cPanel -> Email Accounts for SMTP details
 */

// SMTP Configuration
define('SMTP_HOST', 'smtp.hostinger.com');      // SMTP server
define('SMTP_PORT', 587);                       // SMTP port (587 for TLS)
define('SMTP_SECURE', 'tls');                   // Encryption type
define('SMTP_USERNAME', 'noreply@marathonpraise.ng'); // Email address
define('SMTP_PASSWORD', 'MPP@2025');            // Email password
define('FROM_EMAIL', 'noreply@marathonpraise.ng');     // From email
define('FROM_NAME', '84 Days Marathon Praise & Prayer'); // From name
define('REPLY_TO', 'info@marathonpraise.ng');    // Reply-to address

// Email settings
define('EMAIL_DEBUG', false); // Set to true for debugging, false for production

/**
 * Send email using PHP's built-in mail function or SMTP
 * 
 * @param string $to Recipient email address
 * @param string $name Recipient name
 * @param string $subject Email subject
 * @param string $message Email message (HTML or plain text)
 * @param bool $is_plain_text Whether message is plain text (default: false for HTML)
 * @return bool Success status
 */
function sendEmail($to, $name, $subject, $message, $is_plain_text = false) {
    try {
        // Validate input
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid recipient email address");
        }
        
        if (empty($subject) || empty($message)) {
            throw new Exception("Subject and message cannot be empty");
        }
        
        // Try SMTP first, fallback to PHP mail()
        if (function_exists('smtp_send') && !empty(SMTP_HOST)) {
            return sendEmailSMTP($to, $name, $subject, $message, $is_plain_text);
        } else {
            return sendEmailPHP($to, $name, $subject, $message, $is_plain_text);
        }
        
    } catch (Exception $e) {
        error_log("Email sending failed to $to: " . $e->getMessage());
        return false;
    }
}

/**
 * Send email using SMTP (recommended for production)
 * Requires PHPMailer or similar SMTP library
 */
function sendEmailSMTP($to, $name, $subject, $message, $is_plain_text = false) {
    // This is a placeholder for SMTP implementation
    // You would typically use PHPMailer here
    // For now, we'll use the PHP mail function
    
    return sendEmailPHP($to, $name, $subject, $message, $is_plain_text);
}

/**
 * Send email using PHP's built-in mail() function
 * 
 * @param string $to Recipient email
 * @param string $name Recipient name
 * @param string $subject Email subject
 * @param string $message Email message
 * @param bool $is_plain_text Whether message is plain text
 * @return bool Success status
 */
function sendEmailPHP($to, $name, $subject, $message, $is_plain_text = false) {
    try {
        // Prepare headers
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "From: " . FROM_NAME . " <" . FROM_EMAIL . ">";
        $headers[] = "Reply-To: " . REPLY_TO;
        $headers[] = "Return-Path: " . FROM_EMAIL;
        $headers[] = "X-Mailer: PHP/" . phpversion();
        $headers[] = "X-Priority: 3";
        
        if ($is_plain_text) {
            $headers[] = "Content-Type: text/plain; charset=UTF-8";
        } else {
            $headers[] = "Content-Type: text/html; charset=UTF-8";
        }
        
        // Prepare subject (encode if contains non-ASCII characters)
        $encoded_subject = mb_encode_mimeheader($subject, 'UTF-8', 'B');
        
        // Send email
        $success = mail($to, $encoded_subject, $message, implode("\r\n", $headers));
        
        if ($success) {
            if (EMAIL_DEBUG) {
                error_log("Email sent successfully to: $to");
            }
        } else {
            error_log("Failed to send email to: $to");
        }
        
        return $success;
        
    } catch (Exception $e) {
        error_log("PHP mail error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send welcome email template
 */
function sendWelcomeEmail($email, $name) {
    $subject = "Welcome to 84 Days Marathon Praise & Prayer!";
    
    $message = getWelcomeEmailTemplate($name);
    
    return sendEmail($email, $name, $subject, $message);
}

/**
 * Send newsletter subscription confirmation
 */
function sendNewsletterConfirmation($email, $name = '') {
    $subject = "Welcome to the Prayer Community!";
    
    $display_name = !empty($name) ? $name : 'Prayer Warrior';
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Welcome to Prayer Community</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #FF6600, #8B4513); padding: 30px; text-align: center; color: white; }
            .content { padding: 30px; background: #f9f9f9; }
            .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Welcome to the Prayer Community!</h1>
            </div>
            
            <div class='content'>
                <h2>Dear $display_name,</h2>
                
                <p>Thank you for joining our prayer community! You'll now receive daily prayer points, testimonies, and updates about God's move across Nigeria.</p>
                
                <p>Be blessed as you join thousands in believing for Nigeria's transformation!</p>
            </div>
            
            <div class='footer'>
                <p>84 Days Marathon Praise & Prayer</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $display_name, $subject, $message);
}

/**
 * Get welcome email template
 */
function getWelcomeEmailTemplate($name) {
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Welcome to 84 Days MPP</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #FF6600, #8B4513); padding: 30px; text-align: center; color: white; }
            .content { padding: 30px; background: #f9f9f9; }
            .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
            .highlight { background: #FF6600; color: white; padding: 15px; text-align: center; margin: 20px 0; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Welcome to the Prayer Movement!</h1>
                <p>84 Days Marathon Praise & Prayer</p>
            </div>
            
            <div class='content'>
                <h2>Dear $name,</h2>
                
                <p>Welcome to this historic movement! You are now part of thousands believing for Nigeria's transformation through prayer.</p>
                
                <div class='highlight'>
                    <h3>Your Prayer Journey Begins Now</h3>
                </div>
                
                <p>Get ready to experience God's power as we unite in prayer for our beloved nation.</p>
                
                <p>Blessings,<br>The MMP Team</p>
            </div>
            
            <div class='footer'>
                <p>84 Days Marathon Praise & Prayer<br>
                <a href='mailto:info@marathonpraise.ng'>info@marathonpraise.ng</a></p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Test email configuration
 */
function testEmail($test_email = null) {
    $test_email = $test_email ?: 'test@example.com';
    
    try {
        $subject = "Test Email - 84 Days MPP";
        $message = "This is a test email to verify your email configuration is working properly.";
        
        $result = sendEmail($test_email, 'Test User', $subject, $message, true);
        
        return [
            'success' => $result,
            'message' => $result ? 'Test email sent successfully' : 'Failed to send test email',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Email test failed',
            'error' => $e->getMessage()
        ];
    }
}

// Uncomment this line to test email when file is accessed directly
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Content-Type: application/json');
    
    $test_email = $_GET['email'] ?? 'test@example.com';
    echo json_encode(testEmail($test_email), JSON_PRETTY_PRINT);
}
?>
