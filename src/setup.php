<?php
/**
 * Backend Setup and Database Initialization Script
 * Run this once to set up the enhanced backend
 */

session_start();
require_once 'config/database.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPP Backend Setup</title>
    <link href="../public/assets/css/globals.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; color: #FF6600; }
        .step { margin: 20px 0; padding: 20px; background: #f9f9f9; border-left: 4px solid #FF6600; }
        .success { background: #d4edda; border-color: #28a745; color: #155724; }
        .error { background: #f8d7da; border-color: #dc3545; color: #721c24; }
        .warning { background: #fff3cd; border-color: #ffc107; color: #856404; }
        button { background: #FF6600; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #e55a00; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: 'Courier New', monospace; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MPP Backend Setup</h1>
            <p>Initialize and configure the enhanced backend system</p>
        </div>

        <?php
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'init_database':
                    echo '<div class="step">';
                    echo '<h3>🗄️ Database Initialization</h3>';
                    
                    try {
                        $pdo = getDatabaseConnection();
                        
                        // Read and execute the final consolidated schema
                        $schema_file = '/Applications/XAMPP/xamppfiles/htdocs/mpp_v10/backend/database/final_schema.sql';
                        if (file_exists($schema_file)) {
                            $schema_sql = file_get_contents($schema_file);
                            
                            // Split by semicolon and execute each statement
                            $statements = array_filter(array_map('trim', explode(';', $schema_sql)));
                            
                            $executed = 0;
                            foreach ($statements as $statement) {
                                if (!empty($statement) && !preg_match('/^\s*--/', $statement)) {
                                    try {
                                        $pdo->exec($statement);
                                        $executed++;
                                    } catch (PDOException $e) {
                                        if (strpos($e->getMessage(), 'already exists') === false) {
                                            echo '<div class="error">Error executing statement: ' . htmlspecialchars($e->getMessage()) . '</div>';
                                        }
                                    }
                                }
                            }
                            
                            echo '<div class="success">✅ Database schema updated successfully! Executed ' . $executed . ' statements.</div>';
                        } else {
                            echo '<div class="error">❌ Schema file not found: ' . $schema_file . '</div>';
                        }
                        
                    } catch (Exception $e) {
                        echo '<div class="error">❌ Database connection failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }
                    
                    echo '</div>';
                    break;
                    
                case 'test_apis':
                    echo '<div class="step">';
                    echo '<h3>🔌 API Testing</h3>';
                    
                    $apis_to_test = [
                        'statistics.php' => 'Statistics API',
                        'auth.php' => 'Authentication API',
                        'export.php' => 'Data Export API',
                        'volunteer-signup.php' => 'Volunteer Signup API',
                        'prayer-signup.php' => 'Prayer Signup API'
                    ];
                    
                    foreach ($apis_to_test as $api_file => $api_name) {
                        $api_path = 'api/' . $api_file;
                        if (file_exists($api_path)) {
                            echo '<div class="success">✅ ' . $api_name . ' - File exists</div>';
                        } else {
                            echo '<div class="error">❌ ' . $api_name . ' - File missing</div>';
                        }
                    }
                    
                    echo '</div>';
                    break;
                    
                case 'create_admin':
                    echo '<div class="step">';
                    echo '<h3>👤 Admin User Creation</h3>';
                    
                    $username = $_POST['admin_username'] ?? '';
                    $password = $_POST['admin_password'] ?? '';
                    $full_name = $_POST['admin_name'] ?? '';
                    $email = $_POST['admin_email'] ?? '';
                    
                    if (empty($username) || empty($password) || empty($full_name) || empty($email)) {
                        echo '<div class="error">❌ All fields are required for admin creation</div>';
                    } else {
                        try {
                            $pdo = getDatabaseConnection();
                            
                            // Check if admin already exists
                            $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ? OR email = ?");
                            $stmt->execute([$username, $email]);
                            
                            if ($stmt->fetch()) {
                                echo '<div class="warning">⚠️ Admin user with this username or email already exists</div>';
                            } else {
                                // Create new admin
                                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                                $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash, full_name, email, role) VALUES (?, ?, ?, ?, 'super_admin')");
                                
                                if ($stmt->execute([$username, $password_hash, $full_name, $email])) {
                                    echo '<div class="success">✅ Admin user created successfully!</div>';
                                    echo '<div class="code">Username: ' . htmlspecialchars($username) . '<br>Password: [hidden for security]</div>';
                                } else {
                                    echo '<div class="error">❌ Failed to create admin user</div>';
                                }
                            }
                            
                        } catch (Exception $e) {
                            echo '<div class="error">❌ Error creating admin: ' . htmlspecialchars($e->getMessage()) . '</div>';
                        }
                    }
                    
                    echo '</div>';
                    break;
                    
                case 'test_email':
                    echo '<div class="step">';
                    echo '<h3>📧 Email Service Testing</h3>';
                    
                    $test_email = $_POST['test_email'] ?? '';
                    
                    if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
                        echo '<div class="error">❌ Valid email address is required</div>';
                    } else {
                        try {
                            require_once 'config/email_service.php';
                            $emailService = new EmailService();
                            
                            $variables = [
                                'name' => 'Test User',
                                'service_type' => 'Testing',
                                'duration' => '1',
                                'start_date' => date('Y-m-d'),
                                'time_of_day' => 'Morning',
                                'state' => 'Test State',
                                'country' => 'Nigeria'
                            ];
                            
                            $queue_id = $emailService->queueEmail($test_email, 'Test User', 'volunteer_welcome', $variables, 'high');
                            
                            if ($queue_id) {
                                echo '<div class="success">✅ Test email queued successfully (ID: ' . $queue_id . ')</div>';
                                
                                // Try to process the queue
                                $result = $emailService->processEmailQueue(1);
                                if ($result && $result['sent'] > 0) {
                                    echo '<div class="success">✅ Test email sent successfully!</div>';
                                } else {
                                    echo '<div class="warning">⚠️ Email queued but may not have been sent. Check email configuration.</div>';
                                }
                            } else {
                                echo '<div class="error">❌ Failed to queue test email</div>';
                            }
                            
                        } catch (Exception $e) {
                            echo '<div class="error">❌ Email service error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                        }
                    }
                    
                    echo '</div>';
                    break;
            }
        }
        ?>

        <!-- Database Initialization -->
        <div class="step">
            <h3>1. 🗄️ Initialize Database Schema</h3>
            <p>Run the enhanced database schema to add admin users, activity logging, and improved tables.</p>
            <form method="POST">
                <input type="hidden" name="action" value="init_database">
                <button type="submit">Initialize Database</button>
            </form>
        </div>

        <!-- API Testing -->
        <div class="step">
            <h3>2. 🔌 Test API Endpoints</h3>
            <p>Verify that all API endpoints are properly configured and accessible.</p>
            <form method="POST">
                <input type="hidden" name="action" value="test_apis">
                <button type="submit">Test APIs</button>
            </form>
        </div>

        <!-- Admin User Creation -->
        <div class="step">
            <h3>3. 👤 Create Admin User</h3>
            <p>Create a new administrator account for accessing the backend dashboard.</p>
            <form method="POST">
                <input type="hidden" name="action" value="create_admin">
                <div style="margin: 10px 0;">
                    <label>Username:</label><br>
                    <input type="text" name="admin_username" required style="width: 100%; padding: 8px; margin: 5px 0;">
                </div>
                <div style="margin: 10px 0;">
                    <label>Password:</label><br>
                    <input type="password" name="admin_password" required style="width: 100%; padding: 8px; margin: 5px 0;">
                </div>
                <div style="margin: 10px 0;">
                    <label>Full Name:</label><br>
                    <input type="text" name="admin_name" required style="width: 100%; padding: 8px; margin: 5px 0;">
                </div>
                <div style="margin: 10px 0;">
                    <label>Email:</label><br>
                    <input type="email" name="admin_email" required style="width: 100%; padding: 8px; margin: 5px 0;">
                </div>
                <button type="submit">Create Admin User</button>
            </form>
        </div>

        <!-- Email Testing -->
        <div class="step">
            <h3>4. 📧 Test Email Service</h3>
            <p>Send a test email to verify the email service is working correctly.</p>
            <form method="POST">
                <input type="hidden" name="action" value="test_email">
                <div style="margin: 10px 0;">
                    <label>Test Email Address:</label><br>
                    <input type="email" name="test_email" required style="width: 100%; padding: 8px; margin: 5px 0;">
                </div>
                <button type="submit">Send Test Email</button>
            </form>
        </div>

        <div class="step">
            <h3>🎉 Setup Complete!</h3>
            <p>Once all steps above are completed successfully, your MPP backend will be fully operational with:</p>
            <ul>
                <li>Enhanced database schema with admin users and activity logging</li>
                <li>REST API endpoints for statistics, authentication, and data export</li>
                <li>Email queue system with template support</li>
                <li>Admin authentication and session management</li>
                <li>Comprehensive volunteer and prayer signup handling</li>
            </ul>
            <p><strong>Next steps:</strong></p>
            <ol>
                <li>Access the admin dashboard at <a href="admin/dashboard.php">admin/dashboard.php</a></li>
                <li>Configure email settings in <code>config/email.php</code></li>
                <li>Customize email templates in the <code>templates/</code> directory</li>
            </ol>
        </div>
    </div>
</body>
</html>
