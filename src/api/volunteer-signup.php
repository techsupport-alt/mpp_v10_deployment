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
    $text_fields = [
        'first_name', 'last_name', 'email', 'phone_number',
        'emergency_name', 'emergency_phone', 'emergency_relationship'
    ];
    
    $select_fields = [
        'gender', 'country', 'state', 'church_type', 'service_type', 'start_date', 
        'duration', 'time_of_day', 'born_again', 'prayer_frequency', 'bible_study'
    ];
    
    $other_fields = ['age'];
    
    $missing_fields = [];
    
    // Check text fields (can be trimmed)
    foreach ($text_fields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            $missing_fields[] = $field;
        }
    }
    
    // Check select fields (should not be empty or default values)
    foreach ($select_fields as $field) {
        if (!isset($input[$field]) || $input[$field] === '' || $input[$field] === null) {
            $missing_fields[] = $field;
        }
    }
    
    // Check other fields
    foreach ($other_fields as $field) {
        if ($field === 'age') {
            if (!isset($input[$field]) || $input[$field] === '' || $input[$field] === null || !is_numeric($input[$field])) {
                $missing_fields[] = $field;
            }
        } else {
            if (!isset($input[$field]) || $input[$field] === '' || $input[$field] === null) {
                $missing_fields[] = $field;
            }
        }
    }

    // Special check for church_name if church_type is Non-RCCG
    if (isset($input['church_type']) && $input['church_type'] === 'Non-RCCG') {
        if (!isset($input['church_name']) || empty(trim($input['church_name']))) {
            $missing_fields[] = 'church_name';
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
    
    // Validate consent fields
    if (!isset($input['commitment_consent']) || $input['commitment_consent'] !== true) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'You must commit to serve faithfully during your selected period']);
        exit();
    }
    
    // if (!isset($input['devotional_consent']) || $input['devotional_consent'] !== true) {
    //     http_response_code(400);
    //     echo json_encode(['success' => false, 'message' => 'You must agree to receive daily devotional prompts and updates']);
    //     exit();
    // }
    
    // Sanitize input data
    $first_name = sanitizeInput($input['first_name']);
    $last_name = sanitizeInput($input['last_name']);
    $full_name = $first_name . ' ' . $last_name;
    $email = filter_var(trim($input['email']), FILTER_SANITIZE_EMAIL);
    $phone_number = sanitizeInput($input['phone_number']);
    $gender = sanitizeInput($input['gender']);
    $age = (int)$input['age'];
    $country = sanitizeInput($input['country'] ?? 'Nigeria');
    $state = sanitizeInput($input['state']);
    $church_type = sanitizeInput($input['church_type']);
    $church_name = isset($input['church_name']) ? sanitizeInput($input['church_name']) : null;
    $church_affiliation = $church_type === 'Non-RCCG' ? $church_name : $church_type;
    
    // Volunteer preferences
    $duration = sanitizeInput($input['duration']);
    $start_date = $input['start_date'];
    $time_of_day = sanitizeInput($input['time_of_day']);
    $service_type = sanitizeInput($input['service_type']);
    
    // Spiritual background
    $born_again = sanitizeInput($input['born_again']);
    $prayer_frequency = sanitizeInput($input['prayer_frequency']);
    $bible_study = sanitizeInput($input['bible_study']);
    
    // Emergency contact
    $emergency_name = sanitizeInput($input['emergency_name']);
    $emergency_phone = sanitizeInput($input['emergency_phone']);
    $emergency_relationship = sanitizeInput($input['emergency_relationship']);
    $comments = isset($input['comments']) ? sanitizeInput($input['comments']) : null;

    // Consent
    $commitment_consent = !empty($input['commitment_consent']);
    $devotional_consent = !empty($input['devotional_consent']);    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }
    
    // Validate gender
    if (!in_array($gender, ['male', 'female'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid gender selection']);
        exit();
    }
    
    // Validate age
    if ($age < 16 || $age > 100) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Age must be between 16 and 100']);
        exit();
    }
    
    // Validate duration
    $valid_durations = ['3', '5', '7', '14', '21', '30', '40'];
    if (!in_array($duration, $valid_durations)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid duration selection']);
        exit();
    }
    
    // Validate time of day
    $valid_times = ['morning', 'afternoon', 'night', 'flexible'];
    if (!in_array($time_of_day, $valid_times)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid time preference']);
        exit();
    }
    
    // Validate service type
    $valid_services = ['intercession', 'technical', 'ushering', 'logistics', 'protocol', 'medical', 'sanitation', 'praise'];
    if (!in_array($service_type, $valid_services)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid service type selection']);
        exit();
    }
    
    // Validate start date
    $start_date_obj = DateTime::createFromFormat('Y-m-d', $start_date);
    if (!$start_date_obj || $start_date_obj < new DateTime('today')) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Start date must be today or in the future']);
        exit();
    }
    
    // Get database connection
    $pdo = getDatabaseConnection();
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM volunteer_registrations WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already registered as volunteer']);
        exit();
    }
    
    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO volunteer_registrations (
            first_name, last_name, full_name, email, phone_number, gender, age, country, state, church_affiliation,
            service_type, start_date, duration, time_of_day,
            born_again, bible_study, prayer_frequency,
            emergency_name, emergency_phone, emergency_relationship,
            comments, commitment_consent, devotional_consent
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $first_name, $last_name, $full_name, $email, $phone_number, $gender, $age, $country, $state, $church_affiliation,
        $service_type, $start_date, $duration, $time_of_day,
        $born_again, $bible_study, $prayer_frequency,
        $emergency_name, $emergency_phone, $emergency_relationship,
        $comments, $commitment_consent, $devotional_consent
    ]);
    
    if (!$result) {
        throw new Exception('Failed to insert volunteer registration');
    }
    
    $registration_id = $pdo->lastInsertId();
    
    // Send confirmation email
    try {
        sendVolunteerConfirmationEmail($email, $full_name, $service_type, $duration, $start_date);
    } catch (Exception $e) {
        // Log email error but don't fail the registration
        error_log("Email sending failed: " . $e->getMessage());
    }
    
    // Send notification to admin
    try {
        sendVolunteerNotificationToAdmin($full_name, $email, $service_type, $duration, $start_date);
    } catch (Exception $e) {
        error_log("Admin notification failed: " . $e->getMessage());
    }
    
    // Log successful registration
    error_log("Volunteer registration successful: ID $registration_id, Email: $email, Service: $service_type");
    
    // Return success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Volunteer application submitted successfully! Our team will contact you within 48 hours to discuss your role and next steps.',
        'data' => [
            'id' => $registration_id,
            'name' => $full_name,
            'service_type' => $service_type,
            'duration' => $duration,
            'start_date' => $start_date,
            'registered_at' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (PDOException $e) {
    // Database error
    error_log("Database error in volunteer registration: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred. Please try again later.'
    ]);
    
} catch (Exception $e) {
    // General error
    error_log("General error in volunteer registration: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred. Please try again later.',
        'debug' => defined('DB_DEBUG') && DB_DEBUG ? $e->getMessage() : null
    ]);
}

/**
 * Send confirmation email to volunteer
 */
function sendVolunteerConfirmationEmail($email, $name, $service_type, $duration, $start_date) {
    $subject = "Thank You for Volunteering - 84 Days Marathon Praise & Prayer";
    
    $service_labels = [
        'intercession' => 'Intercession',
        'technical' => 'Technical Support (Media/Production/Sound)',
        'ushering' => 'Ushering',
        'logistics' => 'Logistics (Transportation/Accommodation)',
        'protocol' => 'Protocol/Security',
        'medical' => 'Medical',
        'sanitation' => 'Sanitation',
        'praise' => 'Volunteer Praise Team'
    ];
    
    $service_label = $service_labels[$service_type] ?? $service_type;
    $start_date_formatted = date('F j, Y', strtotime($start_date));
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Thank You for Volunteering</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #FF6600, #8B4513); padding: 30px; text-align: center; color: white; }
            .content { padding: 30px; background: #f9f9f9; }
            .volunteer-info { background: #8B4513; color: white; padding: 20px; margin: 20px 0; border-radius: 5px; }
            .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
            .button { display: inline-block; background: #8B4513; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
            ul { padding-left: 20px; }
            li { margin: 8px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🔥 Thank You for Your Heart to Serve!</h1>
                <p>84 Days Marathon Praise & Prayer</p>
            </div>
            
            <div class='content'>
                <h2>Dear $name,</h2>
                
                <p>Thank you for volunteering to serve in the 84 Days Marathon Praise & Prayer movement! Your willingness to use your gifts and talents for Nigeria's transformation is deeply appreciated.</p>
                
                <div class='volunteer-info'>
                    <h3>📋 Your Volunteer Registration Details:</h3>
                    <p><strong>Service Area:</strong> $service_label</p>
                    <p><strong>Duration:</strong> $duration Days</p>
                    <p><strong>Start Date:</strong> $start_date_formatted</p>
                </div>
                
                <h3>What Happens Next:</h3>
                <ul>
                    <li>Our volunteer coordinator will review your application within 48 hours</li>
                    <li>You'll receive a call or email with specific details about your role</li>
                    <li>Training and orientation will be provided for your service area</li>
                    <li>You'll be connected with your team leader and fellow volunteers</li>
                    <li>All necessary resources and materials will be provided</li>
                </ul>
                
                <h3>While You Wait:</h3>
                <ul>
                    <li>Begin praying for Nigeria's transformation</li>
                    <li>Join our prayer movement if you haven't already</li>
                    <li>Follow our social media channels for updates</li>
                    <li>Prepare your heart for this sacred assignment</li>
                </ul>
                
                <p style='text-align: center;'>
                    <a href='https://marathonpraise.ng' class='button'>Visit Our Website</a>
                </p>
                
                <p><strong>Scripture Encouragement:</strong><br>
                <em>\"Each of you should use whatever gift you have to serve others, as faithful stewards of God's grace in its various forms.\"</em> - 1 Peter 4:10</p>
                
                <p>We are excited to have you join us in this historic movement. Together, we will see God's glory manifest across Nigeria!</p>
                
                <p>Blessings,<br>
                <strong>The MPP Volunteer Team</strong></p>
            </div>
            
            <div class='footer'>
                <p>84 Days Marathon Praise & Prayer<br>
                Volunteer Coordination Team<br>
                <a href='mailto:volunteers@marathonpraise.ng'>volunteers@marathonpraise.ng</a></p>
                
                <p>You received this email because you applied to volunteer for the 84 Days Marathon Praise & Prayer movement.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $name, $subject, $message);
}

/**
 * Send notification to admin about new volunteer
 */
function sendVolunteerNotificationToAdmin($name, $email, $service_type, $duration, $start_date) {
    $admin_email = 'volunteers@marathonpraise.ng'; // Configure this
    $subject = "New Volunteer Registration - $name";
    
    $service_labels = [
        'intercession' => 'Intercession',
        'technical' => 'Technical Support (Media/Production/Sound)',
        'ushering' => 'Ushering',
        'logistics' => 'Logistics (Transportation/Accommodation)',
        'protocol' => 'Protocol/Security',
        'medical' => 'Medical',
        'sanitation' => 'Sanitation',
        'praise' => 'Volunteer Praise Team'
    ];
    
    $service_label = $service_labels[$service_type] ?? $service_type;
    $start_date_formatted = date('F j, Y', strtotime($start_date));
    
    $message = "
    🔥 NEW VOLUNTEER REGISTRATION
    
    Name: $name
    Email: $email
    Service Area: $service_label
    Duration: $duration Days
    Start Date: $start_date_formatted
    Applied: " . date('Y-m-d H:i:s') . "
    
    ACTION REQUIRED: Please contact within 48 hours to discuss role assignment and next steps.
    
    View full details in admin dashboard.
    ";
    
    return sendEmail($admin_email, 'Admin', $subject, $message, true); // true for plain text
}
?>
