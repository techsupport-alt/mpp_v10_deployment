<?php
/**
 * Data Export API Endpoint
 * Handles CSV/Excel export of volunteers and prayer signups
 */

session_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit();
}

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $pdo = getDatabaseConnection();
    
    $type = $_GET['type'] ?? 'volunteers';
    $format = $_GET['format'] ?? 'csv';
    $date_from = $_GET['date_from'] ?? null;
    $date_to = $_GET['date_to'] ?? null;
    
    // Build date filter
    $date_filter = '';
    $params = [];
    
    if ($date_from && $date_to) {
        $date_filter = ' WHERE created_at BETWEEN ? AND ?';
        $params = [$date_from . ' 00:00:00', $date_to . ' 23:59:59'];
    } elseif ($date_from) {
        $date_filter = ' WHERE created_at >= ?';
        $params = [$date_from . ' 00:00:00'];
    } elseif ($date_to) {
        $date_filter = ' WHERE created_at <= ?';
        $params = [$date_to . ' 23:59:59'];
    }
    
    if ($type === 'volunteers') {
        // Export volunteers
        $sql = "SELECT 
                    id,
                    full_name,
                    email,
                    phone_number,
                    gender,
                    age,
                    country,
                    state,
                    church_affiliation,
                    service_type,
                    duration,
                    start_date,
                    time_of_day,
                    born_again,
                    holy_spirit,
                    prayer_frequency,
                    bible_study,
                    emergency_name,
                    emergency_phone,
                    emergency_relationship,
                    comments,
                    created_at
                FROM volunteer_registrations" . $date_filter . " ORDER BY created_at DESC";
        
        $filename = "volunteers_export_" . date('Y-m-d_H-i-s');
        
    } elseif ($type === 'prayers') {
        // Export prayer signups
        $sql = "SELECT 
                    id,
                    name,
                    email,
                    phone,
                    state,
                    church,
                    prayer_time,
                    commitment,
                    created_at
                FROM prayer_signups" . $date_filter . " ORDER BY created_at DESC";
        
        $filename = "prayer_signups_export_" . date('Y-m-d_H-i-s');
        
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid export type']);
        exit();
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($data)) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No data found for export']);
        exit();
    }
    
    // Log export activity
    $stmt = $pdo->prepare("INSERT INTO admin_activity_log (admin_id, action, details) VALUES (?, 'data_export', ?)");
    $stmt->execute([$_SESSION['admin_id'], "Exported $type data (" . count($data) . " records)"]);
    
    if ($format === 'json') {
        // JSON export
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '.json"');
        echo json_encode(['data' => $data, 'count' => count($data), 'exported_at' => date('Y-m-d H:i:s')]);
        
    } else {
        // CSV export (default)
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        
        if (!empty($data)) {
            $output = fopen('php://output', 'w');
            
            // Write CSV headers
            fputcsv($output, array_keys($data[0]));
            
            // Write CSV data
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
            
            fclose($output);
        }
    }
    
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Export error occurred',
        'error' => $e->getMessage()
    ]);
}
?>
