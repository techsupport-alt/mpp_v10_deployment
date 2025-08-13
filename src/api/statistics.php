<?php
/**
 * Statistics API Endpoint
 * Provides real-time statistics for the MPP dashboard
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $pdo = getDatabaseConnection();

    // Optimized Volunteer and Prayer Stats
    $stmt = $pdo->query("
        SELECT
            (SELECT COUNT(*) FROM volunteer_registrations) as total_volunteers,
            (SELECT COUNT(*) FROM volunteer_registrations WHERE status = 'approved') as approved_volunteers,
            (SELECT COUNT(*) FROM volunteer_registrations WHERE status = 'pending') as pending_volunteers,
            (SELECT COUNT(*) FROM prayer_signups) as total_prayers
    ");
    $main_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Optimized Service Distribution
    $stmt = $pdo->query("
        SELECT service_type, COUNT(*) as count 
        FROM volunteer_registrations 
        GROUP BY service_type
    ");
    $service_dist = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Optimized Geographical Distribution
    $stmt = $pdo->query("
        SELECT state, COUNT(*) as count 
        FROM volunteer_registrations 
        WHERE state IS NOT NULL AND state != ''
        GROUP BY state
    ");
    $geo_dist = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Compile statistics in the format expected by the dashboard
    $response = [
        'success' => true,
        'data' => [
            'total_volunteers' => (int)$main_stats['total_volunteers'],
            'approved_volunteers' => (int)$main_stats['approved_volunteers'],
            'pending_volunteers' => (int)$main_stats['pending_volunteers'],
            'total_prayers' => (int)$main_stats['total_prayers'],
            'service_distribution' => array_map('intval', $service_dist),
            'geographical_distribution' => array_map('intval', $geo_dist)
        ]
    ];

    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred',
        'error' => $e->getMessage()
    ]);
}
?>
