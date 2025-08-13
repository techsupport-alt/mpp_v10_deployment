<?php
/**
 * Volunteer Management API
 * Handles volunteer CRUD operations for admin dashboard
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
$volunteer_id = $path_info ? ltrim($path_info, '/') : null;

try {
    $pdo = getDatabaseConnection();
    $emailService = new EmailService();
    
    switch ($method) {
        case 'GET':
            if ($volunteer_id) {
                // Get specific volunteer
                $stmt = $pdo->prepare("SELECT * FROM volunteer_registrations WHERE id = ?");
                $stmt->execute([$volunteer_id]);
                $volunteer = $stmt->fetch();
                
                if ($volunteer) {
                    echo json_encode(['success' => true, 'data' => $volunteer]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Volunteer not found']);
                }
            } else {
                // Get all volunteers with filtering and pagination
                $page = $_GET['page'] ?? 1;
                $limit = $_GET['limit'] ?? 20;
                $search = $_GET['search'] ?? '';
                $status = $_GET['status'] ?? '';
                $service_type = $_GET['service_type'] ?? '';
                $state = $_GET['state'] ?? '';
                
                $offset = ($page - 1) * $limit;
                
                // Build WHERE clause
                $where_conditions = ['1=1'];
                $params = [];
                
                if (!empty($search)) {
                    $where_conditions[] = '(full_name LIKE ? OR email LIKE ? OR phone_number LIKE ?)';
                    $search_param = "%$search%";
                    $params = array_merge($params, [$search_param, $search_param, $search_param]);
                }
                
                if (!empty($status)) {
                    $where_conditions[] = 'status = ?';
                    $params[] = $status;
                }
                
                if (!empty($service_type)) {
                    $where_conditions[] = 'service_type = ?';
                    $params[] = $service_type;
                }
                
                if (!empty($state)) {
                    $where_conditions[] = 'state = ?';
                    $params[] = $state;
                }
                
                $where_clause = implode(' AND ', $where_conditions);
                
                // Get total count
                $count_sql = "SELECT COUNT(*) FROM volunteer_registrations WHERE $where_clause";
                $count_stmt = $pdo->prepare($count_sql);
                $count_stmt->execute($params);
                $total = $count_stmt->fetchColumn();
                
                // Get volunteers
                $sql = "SELECT * FROM volunteer_registrations WHERE $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_merge($params, [$limit, $offset]));
                $volunteers = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'data' => $volunteers,
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
            // Update volunteer status and details
            if (!$volunteer_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Volunteer ID required']);
                exit();
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $status = $input['status'] ?? '';
            $notes = $input['notes'] ?? '';
            $rejection_reason = $input['rejection_reason'] ?? '';
            
            if (empty($status)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Status is required']);
                exit();
            }
            
            // Get volunteer details for email notification
            $stmt = $pdo->prepare("SELECT * FROM volunteer_registrations WHERE id = ?");
            $stmt->execute([$volunteer_id]);
            $volunteer = $stmt->fetch();
            
            if (!$volunteer) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Volunteer not found']);
                exit();
            }
            
            // Update volunteer
            $update_fields = ['status = ?', 'notes = ?', 'updated_at = NOW()'];
            $update_params = [$status, $notes];
            
            if ($status === 'approved') {
                $update_fields[] = 'approval_date = NOW()';
                $update_fields[] = 'approved_by = ?';
                $update_params[] = $_SESSION['admin_id'];
            } elseif ($status === 'rejected') {
                $update_fields[] = 'rejection_reason = ?';
                $update_params[] = $rejection_reason;
            }
            
            $update_params[] = $volunteer_id;
            
            $sql = "UPDATE volunteer_registrations SET " . implode(', ', $update_fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute($update_params)) {
                // Send email notification
                $email_variables = [
                    'name' => $volunteer['full_name'],
                    'service_type' => $volunteer['service_type'],
                    'duration' => $volunteer['duration'],
                    'start_date' => $volunteer['start_date'],
                    'time_of_day' => $volunteer['time_of_day'],
                    'state' => $volunteer['state'],
                    'country' => $volunteer['country'],
                    'status' => $status
                ];
                
                if ($status === 'approved') {
                    $emailService->queueEmail(
                        $volunteer['email'],
                        $volunteer['full_name'],
                        'volunteer_approved',
                        $email_variables,
                        'high'
                    );
                }
                
                // Log activity
                $stmt = $pdo->prepare("INSERT INTO admin_activity_log (admin_id, action, details) VALUES (?, 'volunteer_status_update', ?)");
                $stmt->execute([$_SESSION['admin_id'], "Updated volunteer {$volunteer['full_name']} status to $status"]);
                
                echo json_encode(['success' => true, 'message' => 'Volunteer updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update volunteer']);
            }
            break;
            
        case 'DELETE':
            // Delete volunteer (soft delete by setting status to deleted)
            if (!$volunteer_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Volunteer ID required']);
                exit();
            }
            
            $stmt = $pdo->prepare("UPDATE volunteer_registrations SET status = 'deleted', updated_at = NOW() WHERE id = ?");
            
            if ($stmt->execute([$volunteer_id])) {
                // Log activity
                $stmt = $pdo->prepare("INSERT INTO admin_activity_log (admin_id, action, details) VALUES (?, 'volunteer_deleted', ?)");
                $stmt->execute([$_SESSION['admin_id'], "Deleted volunteer ID: $volunteer_id"]);
                
                echo json_encode(['success' => true, 'message' => 'Volunteer deleted successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to delete volunteer']);
            }
            break;
            
        case 'POST':
            // Bulk operations
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? '';
            $volunteer_ids = $input['volunteer_ids'] ?? [];
            
            if (empty($action) || empty($volunteer_ids)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Action and volunteer IDs required']);
                exit();
            }
            
            $placeholders = str_repeat('?,', count($volunteer_ids) - 1) . '?';
            
            switch ($action) {
                case 'approve_bulk':
                    $sql = "UPDATE volunteer_registrations SET status = 'approved', approval_date = NOW(), approved_by = ?, updated_at = NOW() WHERE id IN ($placeholders)";
                    $params = array_merge([$_SESSION['admin_id']], $volunteer_ids);
                    break;
                    
                case 'reject_bulk':
                    $rejection_reason = $input['rejection_reason'] ?? 'Bulk rejection';
                    $sql = "UPDATE volunteer_registrations SET status = 'rejected', rejection_reason = ?, updated_at = NOW() WHERE id IN ($placeholders)";
                    $params = array_merge([$rejection_reason], $volunteer_ids);
                    break;
                    
                case 'delete_bulk':
                    $sql = "UPDATE volunteer_registrations SET status = 'deleted', updated_at = NOW() WHERE id IN ($placeholders)";
                    $params = $volunteer_ids;
                    break;
                    
                default:
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid bulk action']);
                    exit();
            }
            
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) {
                $affected_rows = $stmt->rowCount();
                
                // Log activity
                $stmt = $pdo->prepare("INSERT INTO admin_activity_log (admin_id, action, details) VALUES (?, 'bulk_volunteer_operation', ?)");
                $stmt->execute([$_SESSION['admin_id'], "Bulk $action: $affected_rows volunteers affected"]);
                
                echo json_encode(['success' => true, 'message' => "$affected_rows volunteers updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Bulk operation failed']);
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
