<?php
/**
 * Admin Dashboard with Analytics
 * Displays registration statistics and data management
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

class AdminDashboard {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function getAnalytics() {
        try {
            // Get registration statistics
            $stats = $this->getRegistrationStats();
            
            // Get location analytics
            $locationData = $this->getLocationAnalytics();
            
            // Get service type analytics
            $serviceData = $this->getServiceAnalytics();
            
            // Get recent registrations
            $recentRegistrations = $this->getRecentRegistrations();
            
            return [
                'stats' => $stats,
                'locations' => $locationData,
                'services' => $serviceData,
                'recent' => $recentRegistrations
            ];
            
        } catch (Exception $e) {
            error_log("Dashboard analytics error: " . $e->getMessage());
            return null;
        }
    }

    private function getRegistrationStats() {
        $query = "SELECT * FROM registration_analytics";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getLocationAnalytics() {
        $query = "SELECT * FROM prayer_location_analytics LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getServiceAnalytics() {
        $query = "SELECT * FROM volunteer_service_analytics";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getRecentRegistrations() {
        $query = "
        (SELECT 'prayer' as type, full_name, email, location as details, created_at 
         FROM prayer_signups 
         WHERE is_active = TRUE 
         ORDER BY created_at DESC LIMIT 5)
        UNION ALL
        (SELECT 'volunteer' as type, full_name, email, 
         CONCAT(service_type, ' - ', state) as details, created_at 
         FROM volunteer_registrations 
         WHERE is_active = TRUE 
         ORDER BY created_at DESC LIMIT 5)
        ORDER BY created_at DESC LIMIT 10";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function exportPrayerSignups() {
        $query = "SELECT full_name, email, phone_number, location, created_at 
                 FROM prayer_signups 
                 WHERE is_active = TRUE 
                 ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function exportVolunteerRegistrations() {
        $query = "SELECT full_name, email, phone_number, gender, age, state, 
                         service_type, status, created_at 
                 FROM volunteer_registrations 
                 WHERE is_active = TRUE 
                 ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

$dashboard = new AdminDashboard();
$analytics = $dashboard->getAnalytics();

// Handle export requests
if (isset($_GET['export'])) {
    $type = $_GET['export'];
    $filename = "mpp_" . $type . "_" . date('Y-m-d') . ".csv";
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    if ($type === 'prayer') {
        $data = $dashboard->exportPrayerSignups();
        fputcsv($output, ['Name', 'Email', 'Phone', 'Location', 'Date Registered']);
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    } elseif ($type === 'volunteers') {
        $data = $dashboard->exportVolunteerRegistrations();
        fputcsv($output, ['Name', 'Email', 'Phone', 'Gender', 'Age', 'State', 'Service Type', 'Status', 'Date Registered']);
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    }
    
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPP Admin Dashboard</title>
    <link href="../../public/assets/css/globals.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --mmp-orange: #FF6600;
            --mmp-brown: #8B4513;
            --mmp-orange-light: #FFB366;
            --mmp-brown-light: #CD853F;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b-4" style="border-bottom-color: var(--mmp-orange);">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold" style="color: var(--mmp-brown);">MPP Admin Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php if ($analytics && $analytics['stats']): ?>
                <?php foreach ($analytics['stats'] as $stat): ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center" 
                                     style="background-color: <?php echo $stat['type'] === 'prayer_signups' ? 'var(--mmp-orange)' : 'var(--mmp-brown)'; ?>">
                                    <span class="text-white text-sm font-bold">
                                        <?php echo $stat['type'] === 'prayer_signups' ? 'P' : 'V'; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <?php echo ucfirst(str_replace('_', ' ', $stat['type'])); ?>
                                </h3>
                                <p class="text-3xl font-bold" style="color: <?php echo $stat['type'] === 'prayer_signups' ? 'var(--mmp-orange)' : 'var(--mmp-brown)'; ?>">
                                    <?php echo number_format($stat['total_count']); ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    Today: <?php echo $stat['today_count']; ?> | 
                                    Week: <?php echo $stat['week_count']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Location Analytics Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Prayer Signups by Location</h3>
                <canvas id="locationChart"></canvas>
            </div>

            <!-- Service Analytics Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Volunteer Services Distribution</h3>
                <canvas id="serviceChart"></canvas>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold">Recent Registrations</h3>
            </div>
            <div class="px-6 py-4">
                <?php if ($analytics && $analytics['recent']): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($analytics['recent'] as $registration): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $registration['type'] === 'prayer' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                <?php echo ucfirst($registration['type']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($registration['full_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($registration['email']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($registration['details']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M j, Y g:i A', strtotime($registration['created_at'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">No recent registrations found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Export Section -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold">Data Export</h3>
            </div>
            <div class="px-6 py-4">
                <div class="flex space-x-4">
                    <a href="?export=prayer" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white hover:opacity-90"
                       style="background-color: var(--mmp-orange);">
                        Export Prayer Signups (CSV)
                    </a>
                    <a href="?export=volunteers" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white hover:opacity-90"
                       style="background-color: var(--mmp-brown);">
                        Export Volunteer Registrations (CSV)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Location Chart
        <?php if ($analytics && $analytics['locations']): ?>
        const locationData = <?php echo json_encode($analytics['locations']); ?>;
        const locationCtx = document.getElementById('locationChart').getContext('2d');
        new Chart(locationCtx, {
            type: 'doughnut',
            data: {
                labels: locationData.map(item => item.location),
                datasets: [{
                    data: locationData.map(item => item.count),
                    backgroundColor: [
                        '#FF6600', '#FF8533', '#FFB366', '#8B4513', '#CD853F',
                        '#D2B48C', '#DDB892', '#B8860B', '#CC5500', '#A0522D'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        <?php endif; ?>

        // Service Chart
        <?php if ($analytics && $analytics['services']): ?>
        const serviceData = <?php echo json_encode($analytics['services']); ?>;
        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        new Chart(serviceCtx, {
            type: 'bar',
            data: {
                labels: serviceData.map(item => item.service_type.replace('_', ' ').toUpperCase()),
                datasets: [{
                    label: 'Volunteers',
                    data: serviceData.map(item => item.count),
                    backgroundColor: '#8B4513',
                    borderColor: '#6B3E2A',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
