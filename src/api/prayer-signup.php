<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../config/email.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required_fields = ['name', 'email', 'phone', 'state', 'prayer_time', 'commitment'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Missing required fields: ' . implode(', ', $missing_fields)
        ]);
        exit();
    }
    
    // Sanitize input data
    $name = filter_var(trim($input['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($input['email']), FILTER_SANITIZE_EMAIL);
    $phone = filter_var(trim($input['phone']), FILTER_SANITIZE_STRING);
    $state = filter_var(trim($input['state']), FILTER_SANITIZE_STRING);
    $church = isset($input['church']) ? filter_var(trim($input['church']), FILTER_SANITIZE_STRING) : '';
    $prayer_time = filter_var(trim($input['prayer_time']), FILTER_SANITIZE_STRING);
    $commitment = filter_var(trim($input['commitment']), FILTER_SANITIZE_STRING);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }
    
    // Validate prayer time slot
    $valid_slots = [
        '12am-3am', '3am-6am', '6am-9am', '9am-12pm',
        '12pm-3pm', '3pm-6pm', '6pm-9pm', '9pm-12am'
    ];
    
    if (!in_array($prayer_time, $valid_slots)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid prayer time slot']);
        exit();
    }
    
    // Get database connection
    $pdo = getDatabaseConnection();
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM prayer_signups WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit();
    }
    
    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO prayer_signups (name, email, phone, state, church, prayer_time, commitment, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $result = $stmt->execute([
        $name, $email, $phone, $state, $church, $prayer_time, $commitment
    ]);
    
    if (!$result) {
        throw new Exception('Failed to insert prayer signup');
    }
    
    $signup_id = $pdo->lastInsertId();
    
    // Send welcome email
    try {
        sendPrayerWelcomeEmail($email, $name, $prayer_time);
    } catch (Exception $e) {
        // Log email error but don't fail the registration
        error_log("Email sending failed: " . $e->getMessage());
    }
    
    // Log successful registration
    error_log("Prayer signup successful: ID $signup_id, Email: $email, Time: $prayer_time");
    
    // Return success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful! Welcome to the prayer movement.',
        'data' => [
            'id' => $signup_id,
            'name' => $name,
            'prayer_time' => $prayer_time,
            'registered_at' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (PDOException $e) {
    // Database error
    error_log("Database error in prayer signup: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred. Please try again later.'
    ]);
    
} catch (Exception $e) {
    // General error
    error_log("General error in prayer signup: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred. Please try again later.'
    ]);
}

/**
 * Send welcome email to new prayer partner
 */
function sendPrayerWelcomeEmail($email, $name, $prayer_time) {
    $subject = "Welcome to 84 Days Marathon Praise & Prayer!";
    
    $time_labels = [
        '12am-3am' => '12:00 AM - 3:00 AM (Midnight Watch)',
        '3am-6am' => '3:00 AM - 6:00 AM (Early Morning)',
        '6am-9am' => '6:00 AM - 9:00 AM (Morning Prayer)',
        '9am-12pm' => '9:00 AM - 12:00 PM (Morning Worship)',
        '12pm-3pm' => '12:00 PM - 3:00 PM (Midday Prayer)',
        '3pm-6pm' => '3:00 PM - 6:00 PM (Afternoon Prayer)',
        '6pm-9pm' => '6:00 PM - 9:00 PM (Evening Prayer)',
        '9pm-12am' => '9:00 PM - 12:00 AM (Night Prayer)'
    ];
    
    $prayer_time_label = $time_labels[$prayer_time] ?? $prayer_time;
    
    $message = "
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
            .prayer-time { background: #FF6600; color: white; padding: 15px; text-align: center; margin: 20px 0; border-radius: 5px; }
            .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
            .button { display: inline-block; background: #FF6600; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
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
                
                <p>Thank you for joining the 84 Days Marathon Praise & Prayer movement! You are now part of thousands of prayer warriors across Nigeria believing for national transformation.</p>
                
                <div class='prayer-time'>
                    <h3>Your Prayer Time Slot:</h3>
                    <p><strong>$prayer_time_label</strong></p>
                </div>
                
                <h3>What's Next:</h3>
                <ul>
                    <li>Begin praying at your selected time slot daily</li>
                    <li>Check your email daily for prayer points and updates</li>
                    <li>Join our WhatsApp group for community and encouragement</li>
                    <li>Invite family and friends to join the movement</li>
                </ul>
                
                <h3>Today's Prayer Focus:</h3>
                <p><em>\"If my people, who are called by my name, will humble themselves and pray and seek my face and turn from their wicked ways, then I will hear from heaven, and I will forgive their sin and will heal their land.\"</em> - 2 Chronicles 7:14</p>
                
                <p>Pray for:</p>
                <ul>
                    <li>National leadership and divine wisdom</li>
                    <li>Unity among believers across denominational lines</li>
                    <li>Economic breakthrough and job creation</li>
                    <li>Peace and security across Nigeria</li>
                    <li>Spiritual revival in families and communities</li>
                </ul>
                
                <p style='text-align: center;'>
                    <a href='https://marathonpraise.ng' class='button'>Visit Prayer Portal</a>
                </p>
            </div>
            
            <div class='footer'>
                <p>84 Days Marathon Praise & Prayer<br>
                Transforming Nigeria Through Prayer<br>
                <a href='mailto:prayer@marathonpraise.ng'>prayer@marathonpraise.ng</a></p>
                
                <p>You received this email because you registered for the 84 Days Marathon Praise & Prayer movement.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $name, $subject, $message);
}
?>