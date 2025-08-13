<?php
// Production error handling settings
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
error_reporting(E_ALL);

// Define application base path
define('BASE_PATH', realpath(__DIR__ . '/..'));

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com;");

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>84 Days Marathon Praise & Prayer | Transforming Nigeria Through Prayer</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Join the 84 Days Marathon Praise & Prayer movement - a nationwide prayer and worship initiative for spiritual transformation in Nigeria. Register for prayer time slots, volunteer opportunities, and daily updates.">
    <meta name="keywords" content="prayer, Nigeria, spiritual transformation, prayer movement, worship, revival, 84 days, marathon prayer, nationwide prayer">
    <meta name="author" content="84 Days Marathon Praise & Prayer">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="84 Days Marathon Praise & Prayer | Transforming Nigeria">
    <meta property="og:description" content="Join thousands in 84 days of continuous prayer and worship for Nigeria's spiritual transformation. Register today for your prayer time slot.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://marathonpraise.ng">
    <meta property="og:image" content="https://marathonpraise.ng/mpp_logo.png">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="84 Days Marathon Praise & Prayer">
    <meta name="twitter:description" content="Join the nationwide prayer movement transforming Nigeria through 84 days of continuous prayer and worship.">
    <meta name="twitter:image" content="https://marathonpraise.ng/mpp_logo.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16x16.png">
    <link rel="mask-icon" href="assets/images/mpp_logo.png" color="#FF6600">
    <meta name="theme-color" content="#FF6600">
    <meta name="msapplication-TileColor" content="#FF6600">
    <meta name="msapplication-TileImage" content="assets/images/mpp_logo.png">
    <link rel="manifest" href="manifest.json">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Global Styles with Enhanced Typography & Theme System -->
    <link href="/assets/css/globals.css" rel="stylesheet">
    
    <!-- Custom Styles with Enhanced Contrast -->
    <link href="/assets/css/styles.css" rel="stylesheet">

    <!-- Tailwind CSS -->
    <link href="/assets/css/tailwind.css" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="min-h-screen bg-background">
    <!-- Navigation -->
    <?php include BASE_PATH . '/src/components/navigation.php'; ?>

    <!-- Main Content Container -->
    <main id="main-content" class="pt-20">
        <?php include BASE_PATH . '/src/components/hero-section.php'; ?>
        <?php include BASE_PATH . '/src/components/about-section.php'; ?>
        <?php include BASE_PATH . '/src/components/participate-section.php'; ?>
        <?php include BASE_PATH . '/src/components/testimonies-section.php'; ?>
        <?php include BASE_PATH . '/src/components/faq-section.php'; ?>
        <?php include BASE_PATH . '/src/components/newsletter-signup.php'; ?>
        <?php include BASE_PATH . '/src/components/footer.php'; ?>
    </main>

    <!-- Loading Spinner -->
    <div id="loading-spinner" class="fixed inset-0 bg-background/80 backdrop-blur-sm z-50 items-center justify-center hidden">
        <div class="w-12 h-12 border-4 border-[var(--mmp-orange)]/30 border-t-[var(--mmp-orange)] rounded-full spinner"></div>
    </div>

    <!-- JavaScript -->
    <script src="/assets/js/main.js"></script>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
