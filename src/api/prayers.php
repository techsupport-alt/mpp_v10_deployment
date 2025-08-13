<?php
/**
 * Prayer Management API
 * Handles prayer signup CRUD operations for admin dashboard
 */

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../config/email_service.php';

// Check admin authentication for non-GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$path_info = $_SERVER['PATH_INFO'] ?? '';
$prayer_id = $path_info ? ltrim($path_info, '/') : null;

try {
    $pdo = getDatabaseConnection();
    $emailService = new EmailService();
    
    switch ($method) {
        case 'GET':
            if ($prayer_id) {
                // Get specific prayer signup
                $stmt = $pdo->prepare("SELECT * FROM prayer_signups WHERE id = ?");
                $stmt->execute([$prayer_id]);
                $prayer = $stmt->fetch();
                
                if ($prayer) {
                    echo json_encode(['success' => true, 'data' => $prayer]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Prayer signup not found']);
                }
            } else {
                // Get all prayer signups with filtering and pagination
                $page = $_GET['page'] ?? 1;
                $limit = $_GET['limit'] ?? 20;
                $search = $_GET['search'] ?? '';
                $status = $_GET['status'] ?? '';
                $prayer_type = $_GET['prayer_type'] ?? '';
                $country = $_GET['country'] ?? '';
                
                $offset = ($page - 1) * $limit;
                
                // Build WHERE clause
                $where_conditions = ['1=1'];
                $params = [];
                
                if (!empty($search)) {
                    $where_conditions[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR prayer_request LIKE ?)';
                    $search_param = "%$search%";
                    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
                }
                
                if (!empty($status)) {
                    $where_conditions[] = 'status = ?';
                    $params[] = $status;
                }
                
                if (!empty($prayer_type)) {
                    $where_conditions[] = 'prayer_type = ?';
                    $params[] = $prayer_type;
                }
                
                if (!empty($country)) {
                    $where_conditions[] = 'country = ?';
                    $params[] = $country;
                }
                
                $where_clause = implode(' AND ', $where_conditions);
                
                // Get total count
                $count_sql = "SELECT COUNT(*) FROM prayer_signups WHERE $where_clause";
                $count_stmt = $pdo->prepare($count_sql);
                $count_stmt->execute($params);
                $total = $count_stmt->fetchColumn();
                
                // Get prayer signups
                $sql = "SELECT * FROM prayer_signups WHERE $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_merge($params, [$limit, $offset]));
                $prayers = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'data' => $prayers,
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => $total,
                        'pages' => ceil($total / $limit)
                    ]
                ]);
            }
            break;
            
        case 'PUT':
            // Update prayer signup status and details
            if (!$prayer_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Prayer ID required']);
                exit();
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $status = $input['status'] ?? '';
            $admin_notes = $input['admin_notes'] ?? '';
            $prayer_response = $input['prayer_response'] ?? '';
            
            if (empty($status)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Status is required']);
                exit();
            }
            
            // Get prayer details for email notification
            $stmt = $pdo->prepare("SELECT * FROM prayer_signups WHERE id = ?");
            $stmt->execute([$prayer_id]);
            $prayer = $stmt->fetch();
            
            if (!$prayer) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Prayer signup not found']);
                exit();
            }
            
            // Update prayer signup
            $update_fields = ['status = ?', 'admin_notes = ?', 'updated_at = NOW()'];
            $update_params = [$status, $admin_notes];
            
            if (!empty($prayer_response)) {
                $update_fields[] = 'prayer_response = ?';
                $update_params[] = $prayer_response;
            }
            
            if ($status === 'prayed') {
                $update_fields[] = 'prayed_at = NOW()';
                $update_fields[] = 'prayed_by = ?';
                $update_params[] = $_SESSION['admin_id'];
            }
            
            $update_params[] = $prayer_id;
            
            $sql = "UPDATE prayer_signups SET " . implode(', ', $update_fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute($update_params)) {
                // Send email notification if status is 'prayed' or has response
                if ($status === 'prayed' || !empty($prayer_response)) {
                    $email_variables = [
                        'name' => $prayer['name'],
                        'prayer_type' => $prayer['prayer_type'],
                        'prayer_request' => $prayer['prayer_request'],
                        'prayer_response' => $prayer_response,
                        'status' => $status
                    ];
                    
                    $emailService->queueEmail(
                        $prayer['email'],
                        $prayer['name'],
                        'prayer_response',
                        $email_variables,
                        'high'
                    );
                }
                
                // Log activity
                $stmt = $pdo->prepare("INSERT INTO admin_activity_log (admin_id, action, details) VALUES (?, 'prayer_status_update', ?)");
                $stmt->execute([$_SESSION['admin_id'], "Updated prayer from {$prayer['name']} status to $status"]);
                
                echo json_encode(['success' => true, 'message' => 'Prayer signup updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update prayer signup']);
            }
            break;
            
        case 'DELETE':
            // Delete prayer signup (soft delete by setting status to deleted)
            if (!$prayer_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Prayer ID required']);
                exit();
            }
            
            $stmt = $pdo->prepare("UPDATE prayer_signups SET status = 'deleted', updated_at = NOW() WHERE id = ?");
            
            if ($stmt->execute([$prayer_id])) {
                // Log activity
                $stmt = $pdo->prepare("INSERT INTO admin_activity_log (admin_id, action, details) VALUES (?, 'prayer_deleted', ?)");
                $stmt->execute([$_SESSION['admin_id'], "Deleted prayer signup ID: $prayer_id"]);
                
                echo json_encode(['success' => true, 'message' => 'Prayer signup deleted successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to delete prayer signup']);
            }
            break;
            
        case 'POST':
            // Special actions for prayer management
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? '';
            
            switch ($action) {
                case 'bulk_pray':
                    $prayer_ids = $input['prayer_ids'] ?? [];
                    $prayer_response = $input['prayer_response'] ?? '';
                    
                    if (empty($prayer_ids)) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Prayer IDs required']);
                        exit();
                    }
                    
                    $placeholders = str_repeat('?,', count($prayer_ids) - 1) . '?';
                    $sql = "UPDATE prayer_signups SET status = 'prayed', prayed_at = NOW(), prayed_by = ?, prayer_response = ?, updated_at = NOW() WHERE id IN ($placeholders)";
                    $params = array_merge([$_SESSION['admin_id'], $prayer_response], $prayer_ids);
                    
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute($params)) {
                        $affected_rows = $stmt->rowCount();
                        
                        // Log activity
                        $stmt = $pdo->prepare("INSERT INTO admin_activity_log (admin_id, action, details) VALUES (?, 'bulk_prayer_operation', ?)");
                        $stmt->execute([$_SESSION['admin_id'], "Bulk prayed for $affected_rows prayer requests"]);
                        
                        echo json_encode(['success' => true, 'message' => "$affected_rows prayer requests marked as prayed"]);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Bulk prayer operation failed']);
                    }
                    break;
                    
                case 'assign_prayer_time':
                    $prayer_id = $input['prayer_id'] ?? '';
                    $time_slot_id = $input['time_slot_id'] ?? '';
                    
                    if (empty($prayer_id) || empty($time_slot_id)) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Prayer ID and time slot required']);
                        exit();
                    }
                    
                    $stmt = $pdo->prepare("UPDATE prayer_signups SET assigned_time_slot = ?, assigned_by = ?, updated_at = NOW() WHERE id = ?");
                    
                    if ($stmt->execute([$time_slot_id, $_SESSION['admin_id'], $prayer_id])) {
                        echo json_encode(['success' => true, 'message' => 'Prayer time assigned successfully']);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Failed to assign prayer time']);
                    }
                    break;
                    
                case 'get_prayer_statistics':
                    // Get prayer statistics for dashboard
                    $stats = [];
                    
                    // Total prayers by status
                    $stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM prayer_signups GROUP BY status");
                    $stmt->execute();
                    $status_counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                    $stats['status_distribution'] = $status_counts;
                    
                    // Prayers by type
                    $stmt = $pdo->prepare("SELECT prayer_type, COUNT(*) as count FROM prayer_signups GROUP BY prayer_type");
                    $stmt->execute();
                    $type_counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                    $stats['type_distribution'] = $type_counts;
                    
                    // Recent prayer requests
                    $stmt = $pdo->prepare("SELECT * FROM prayer_signups ORDER BY created_at DESC LIMIT 10");
                    $stmt->execute();
                    $recent_prayers = $stmt->fetchAll();
                    $stats['recent_prayers'] = $recent_prayers;
                    
                    // Prayers by country
                    $stmt = $pdo->prepare("SELECT country, COUNT(*) as count FROM prayer_signups GROUP BY country ORDER BY count DESC LIMIT 10");
                    $stmt->execute();
                    $country_counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                    $stats['country_distribution'] = $country_counts;
                    
                    echo json_encode(['success' => true, 'data' => $stats]);
                    break;
                    
                default:
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid action']);
                    break;
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error occurred',
        'error' => $e->getMessage()
    ]);
}
?>
