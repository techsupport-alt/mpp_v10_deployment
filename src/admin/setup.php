<?php
/**
 * Admin Setup Script - Run this ONCE after deployment
 * Creates a secure admin user and removes default credentials
 */

require_once '../config/database.php';

// Prevent running this script multiple times
$lockFile = '../setup.lock';
if (file_exists($lockFile)) {
    die('Setup has already been completed. Delete setup.lock file to run again.');
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    
    // Validation
    if (empty($email) || empty($password) || empty($fullName)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $conn = getDatabaseConnection();
            
            // Start transaction
            $conn->beginTransaction();
            
            // Remove default admin user
            $deleteQuery = "DELETE FROM admin_users WHERE email = 'admin@mpp.org'";
            $conn->prepare($deleteQuery)->execute();
            
            // Create new admin user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertQuery = "INSERT INTO admin_users (id, email, password_hash, full_name, role) 
                           VALUES (UUID(), :email, :password_hash, :full_name, 'super_admin')";
            
            $stmt = $conn->prepare($insertQuery);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $hashedPassword);
            $stmt->bindParam(':full_name', $fullName);
            
            if ($stmt->execute()) {
                // Commit transaction
                $conn->commit();
                
                // Create lock file to prevent running again
                file_put_contents($lockFile, date('Y-m-d H:i:s'));
                
                $message = 'Admin setup completed successfully! You can now login with your credentials.';
                
                // Redirect to login after 3 seconds
                header('refresh:3;url=login.php');
            } else {
                throw new Exception('Failed to create admin user');
            }
            
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Admin setup error: " . $e->getMessage());
            $error = 'Setup failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPP Admin Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        :root {
            --mmp-orange: #FF6600;
            --mmp-brown: #8B4513;
        }
        .gradient-bg {
            background: linear-gradient(135deg, var(--mmp-orange) 0%, var(--mmp-brown) 100%);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                MPP Admin Setup
            </h2>
            <p class="mt-2 text-center text-sm text-gray-200">
                Create your secure admin account
            </p>
            <div class="mt-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                <p class="text-sm">
                    <strong>⚠️ Security Notice:</strong> This setup will remove the default admin credentials and create your secure account. Run this only once after deployment.
                </p>
            </div>
        </div>
        
        <form class="mt-8 space-y-6 bg-white rounded-lg shadow-xl p-8" method="POST">
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>
            
            <div class="space-y-4">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input id="full_name" name="full_name" type="text" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" 
                           placeholder="Your full name"
                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" 
                           placeholder="your-email@domain.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" 
                           placeholder="At least 8 characters"
                           minlength="8">
                    <p class="mt-1 text-xs text-gray-500">Minimum 8 characters with letters and numbers</p>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input id="confirm_password" name="confirm_password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" 
                           placeholder="Confirm your password"
                           minlength="8">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200"
                        style="background-color: var(--mmp-orange);">
                    Create Secure Admin Account
                </button>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-md">
                <h4 class="font-medium text-gray-900 mb-2">After Setup:</h4>
                <ul class="text-xs text-gray-600 space-y-1">
                    <li>• Default admin@mpp.org account will be removed</li>
                    <li>• You'll be redirected to the login page</li>
                    <li>• This setup page will be locked</li>
                    <li>• Keep your credentials secure!</li>
                </ul>
            </div>
        </form>
    </div>

    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
