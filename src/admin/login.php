<?php
/**
 * Enhanced Admin Login System
 * Works with the new authentication API
 */

// Common session configuration
$is_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

// Ensure secure session settings
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', $is_https ? '1' : '0');
ini_set('session.cookie_samesite', 'Lax');

session_start([
    'cookie_lifetime' => 86400, // 1 day
    'cookie_secure' => $is_https,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax'
]);

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: enhanced_dashboard.php');
    exit();
}

require_once '../config/database.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPP Admin Login</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../../favicon-32x32.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .login-container {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .form-input {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .form-input:focus {
            background: rgba(255, 255, 255, 1);
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .login-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #dc2626;
        }
    </style>
</head>
<body class="flex items-center justify-center">
    <div class="login-container rounded-2xl p-8 w-full max-w-md mx-4">
        <!-- Logo and Header -->
        <div class="text-center mb-8">
            <img src="../../public/assets/images/mpp_logo.png" alt="MPP Logo" class="h-16 w-auto mx-auto mb-4">
            <h1 class="text-2xl font-bold text-white mb-2">Admin Dashboard</h1>
            <p class="text-white/80">Sign in to access the backend</p>
        </div>

        <!-- Error Message -->
        <div id="error-message" class="error-message rounded-lg p-4 mb-6 hidden">
            <p id="error-text" class="text-sm font-medium"></p>
        </div>

        <!-- Login Form -->
        <form id="login-form" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-white mb-2">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    class="form-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-0 transition-all"
                    placeholder="Enter your username"
                    value="admin"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-white mb-2">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    class="form-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-0 transition-all"
                    placeholder="Enter your password"
                    value="password"
                >
            </div>

            <button 
                type="submit" 
                id="login-btn"
                class="login-button w-full text-white font-semibold py-3 px-6 rounded-lg focus:outline-none focus:ring-0"
            >
                <span id="login-text">Sign In</span>
                <span id="login-spinner" class="hidden">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Signing in...
                </span>
            </button>
        </form>

        <!-- Default Credentials Notice -->
        <div class="mt-8 p-4 bg-yellow-500/20 border border-yellow-500/30 rounded-lg">
            <h3 class="text-yellow-200 font-semibold text-sm mb-2">Default Credentials</h3>
            <p class="text-yellow-100 text-xs">
                <strong>Username:</strong> admin<br>
                <strong>Password:</strong> password
            </p>
            <p class="text-yellow-100 text-xs mt-2">
                <em>Please change these credentials after first login!</em>
            </p>
        </div>

        <!-- Links -->
        <div class="mt-6 text-center">
            <a href="../../index.php" class="text-white/80 hover:text-white text-sm transition-colors">
                ← Back to Main Site
            </a>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');
            const loginBtn = document.getElementById('login-btn');
            const loginText = document.getElementById('login-text');
            const loginSpinner = document.getElementById('login-spinner');
            
            // Hide previous errors
            errorDiv.classList.add('hidden');
            
            // Show loading state
            loginBtn.disabled = true;
            loginText.classList.add('hidden');
            loginSpinner.classList.remove('hidden');
            
            try {
                    const response = await fetch('../api/auth.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        credentials: 'include',
                        body: JSON.stringify({
                            action: 'login',
                            username: username,
                            password: password
                        })
                    });
                
                const data = await response.json();
                console.log('Login response:', data);
                
                if (data.success) {
                    console.log('Login successful, redirecting to dashboard...');
                    // Successful login - redirect to dashboard after brief delay
                    setTimeout(() => {
                        window.location.href = 'enhanced_dashboard.php';
                    }, 100);
                } else {
                    console.log('Login failed:', data.message);
                    // Show error message
                    errorText.textContent = data.message || 'Invalid username or password';
                    errorDiv.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Login error:', error);
                errorText.textContent = 'Connection error. Please try again.';
                errorDiv.classList.remove('hidden');
            } finally {
                // Reset button state
                loginBtn.disabled = false;
                loginText.classList.remove('hidden');
                loginSpinner.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
