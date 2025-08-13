<?php
/**
 * Data Export Script for MPP Admin
 * Export prayer signups and volunteer data as CSV
 */

require_once '../config/database.php';

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    die('Unauthorized access');
}

$type = $_GET['type'] ?? '';
$valid_types = ['prayer', 'volunteer', 'newsletter'];

if (!in_array($type, $valid_types)) {
    http_response_code(400);
    die('Invalid export type');
}

try {
    $pdo = getDatabaseConnection();
    
    $filename = "mpp_{$type}_export_" . date('Y-m-d_H-i-s') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: must-revalidate');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    if ($type === 'prayer') {
        // Prayer signups export
        fputcsv($output, [
            'ID', 'Name', 'Email', 'Phone', 'State', 'Church', 
            'Prayer Time', 'Commitment', 'Created Date', 'Status'
        ]);
        
        $stmt = $pdo->query("
            SELECT id, name, email, phone, state, church, prayer_time, commitment, created_at, is_active
            FROM prayer_signups 
            ORDER BY created_at DESC
        ");
        
        while ($row = $stmt->fetch()) {
            fputcsv($output, [
                $row['id'],
                $row['name'],
                $row['email'],
                $row['phone'],
                $row['state'],
                $row['church'],
                $row['prayer_time'],
                $row['commitment'],
                $row['created_at'],
                $row['is_active'] ? 'Active' : 'Inactive'
            ]);
        }
        
    } elseif ($type === 'volunteer') {
        // Volunteer signups export
        fputcsv($output, [
            'ID', 'Name', 'Email', 'Phone', 'State', 'Areas of Interest', 
            'Experience', 'Availability', 'Status', 'Created Date'
        ]);
        
        $stmt = $pdo->query("
            SELECT id, name, email, phone, state, areas, experience, availability, status, created_at
            FROM volunteer_signups 
            ORDER BY created_at DESC
        ");
        
        while ($row = $stmt->fetch()) {
            $areas = json_decode($row['areas'], true);
            $areas_text = is_array($areas) ? implode(', ', $areas) : $row['areas'];
            
            fputcsv($output, [
                $row['id'],
                $row['name'],
                $row['email'],
                $row['phone'],
                $row['state'],
                $areas_text,
                $row['experience'],
                $row['availability'],
                ucfirst($row['status']),
                $row['created_at']
            ]);
        }
        
    } elseif ($type === 'newsletter') {
        // Newsletter subscriptions export
        fputcsv($output, ['ID', 'Email', 'Subscribed Date', 'Status']);
        
        $stmt = $pdo->query("
            SELECT id, email, subscribed_at, is_active
            FROM newsletter_subscriptions 
            ORDER BY subscribed_at DESC
        ");
        
        while ($row = $stmt->fetch()) {
            fputcsv($output, [
                $row['id'],
                $row['email'],
                $row['subscribed_at'],
                $row['is_active'] ? 'Active' : 'Inactive'
            ]);
        }
    }
    
    fclose($output);
    exit();
    
} catch (Exception $e) {
    http_response_code(500);
    die('Export failed: ' . $e->getMessage());
}
?>
