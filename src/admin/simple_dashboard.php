<?php
/**
 * Simple Admin Dashboard for MPP
 * View and analyze submitted data
 */

require_once '../config/database.php';

// Proper authentication check
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $pdo = getDatabaseConnection();
    
    // Get analytics data
    $prayer_total = $pdo->query("SELECT COUNT(*) as count FROM prayer_signups")->fetch()['count'];
    $volunteer_total = $pdo->query("SELECT COUNT(*) as count FROM volunteer_registrations")->fetch()['count'];
    $newsletter_total = $pdo->query("SELECT COUNT(*) as count FROM newsletter_subscriptions")->fetch()['count'];
    
    // Get today's signups
    $prayer_today = $pdo->query("SELECT COUNT(*) as count FROM prayer_signups WHERE DATE(created_at) = CURDATE()")->fetch()['count'];
    $volunteer_today = $pdo->query("SELECT COUNT(*) as count FROM volunteer_registrations WHERE DATE(created_at) = CURDATE()")->fetch()['count'];
    
    // Get state breakdown for prayers
    $state_breakdown = $pdo->query("
        SELECT state, COUNT(*) as count 
        FROM prayer_signups 
        GROUP BY state 
        ORDER BY count DESC 
        LIMIT 10
    ")->fetchAll();
    
    // Get prayer time breakdown
    $time_breakdown = $pdo->query("
        SELECT prayer_time, COUNT(*) as count 
        FROM prayer_signups 
        GROUP BY prayer_time 
        ORDER BY count DESC
    ")->fetchAll();
    
    // Get recent signups
    $recent_prayers = $pdo->query("
        SELECT name, email, state, prayer_time, created_at 
        FROM prayer_signups 
        ORDER BY created_at DESC 
        LIMIT 10
    ")->fetchAll();
    
    $recent_volunteers = $pdo->query("
        SELECT full_name as name, email, state, service_type as availability, created_at 
        FROM volunteer_registrations 
        ORDER BY created_at DESC 
        LIMIT 10
    ")->fetchAll();
    
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPP Admin Dashboard</title>
    <link href="../../public/assets/css/globals.css" rel="stylesheet">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: #f5f5f5; 
        }
        .header { 
            background: linear-gradient(135deg, #FF6600, #8B4513); 
            color: white; 
            padding: 20px; 
            text-align: center; 
        }
        .container { 
            max-width: 1400px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 20px; 
            margin: 20px 0; 
        }
        .stat-card { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            text-align: center; 
        }
        .stat-number { 
            font-size: 2.5rem; 
            font-weight: bold; 
            color: #FF6600; 
        }
        .content-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 20px; 
            margin: 20px 0; 
        }
        .content-card { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        .content-card h3 { 
            color: #FF6600; 
            margin-top: 0; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background: #FF6600; 
            color: white; 
        }
        .logout-btn { 
            position: absolute; 
            top: 20px; 
            right: 20px; 
            background: rgba(255,255,255,0.2); 
            color: white; 
            border: 1px solid white; 
            padding: 8px 16px; 
            border-radius: 4px; 
            cursor: pointer; 
        }
        .logout-btn:hover { 
            background: rgba(255,255,255,0.3); 
        }
        .full-width { 
            grid-column: 1 / -1; 
        }
        .export-btn {
            background: #8B4513;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 5px;
        }
        .export-btn:hover {
            background: #6B3E2A;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔥 MPP Admin Dashboard</h1>
        <p>84 Days Marathon Praise & Prayer - Data Analytics</p>
        <form method="post" action="logout.php" style="display: inline;">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
    
    <div class="container">
        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($prayer_total) ?></div>
                <div>Total Prayer Signups</div>
                <div style="color: #666; font-size: 0.9em;"><?= $prayer_today ?> today</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?= number_format($volunteer_total) ?></div>
                <div>Total Volunteer Signups</div>
                <div style="color: #666; font-size: 0.9em;"><?= $volunteer_today ?> today</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?= number_format($newsletter_total) ?></div>
                <div>Newsletter Subscribers</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?= number_format($prayer_total + $volunteer_total) ?></div>
                <div>Total Registrations</div>
                <div style="color: #666; font-size: 0.9em;">Combined signups</div>
            </div>
        </div>
        
        <!-- Analytics -->
        <div class="content-grid">
            <div class="content-card">
                <h3>📍 Prayer Signups by State</h3>
                <?php if ($state_breakdown): ?>
                    <table>
                        <tr><th>State</th><th>Count</th><th>%</th></tr>
                        <?php foreach ($state_breakdown as $row): ?>
                            <?php $percentage = round(($row['count'] / $prayer_total) * 100, 1); ?>
                            <tr>
                                <td><?= htmlspecialchars($row['state']) ?></td>
                                <td><?= $row['count'] ?></td>
                                <td><?= $percentage ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>No data available</p>
                <?php endif; ?>
            </div>
            
            <div class="content-card">
                <h3>🕐 Prayer Time Slots</h3>
                <?php if ($time_breakdown): ?>
                    <table>
                        <tr><th>Time Slot</th><th>Count</th><th>%</th></tr>
                        <?php foreach ($time_breakdown as $row): ?>
                            <?php $percentage = round(($row['count'] / $prayer_total) * 100, 1); ?>
                            <tr>
                                <td><?= htmlspecialchars($row['prayer_time']) ?></td>
                                <td><?= $row['count'] ?></td>
                                <td><?= $percentage ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>No data available</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="content-grid">
            <div class="content-card">
                <h3>� Recent Prayer Signups</h3>
                <button class="export-btn" onclick="exportData('prayer')">Export CSV</button>
                <?php if ($recent_prayers): ?>
                    <table>
                        <tr><th>Name</th><th>State</th><th>Prayer Time</th><th>Date</th></tr>
                        <?php foreach ($recent_prayers as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['state']) ?></td>
                                <td><?= htmlspecialchars($row['prayer_time']) ?></td>
                                <td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>No prayer signups yet</p>
                <?php endif; ?>
            </div>
            
            <div class="content-card">
                <h3>�‍♀️ Recent Volunteer Signups</h3>
                <button class="export-btn" onclick="exportData('volunteer')">Export CSV</button>
                <?php if ($recent_volunteers): ?>
                    <table>
                        <tr><th>Name</th><th>State</th><th>Availability</th><th>Date</th></tr>
                        <?php foreach ($recent_volunteers as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['state']) ?></td>
                                <td><?= htmlspecialchars($row['availability']) ?></td>
                                <td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>No volunteer signups yet</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="content-card full-width">
            <h3>🔧 Quick Actions</h3>
            <button class="export-btn" onclick="location.href='../../../test_forms_backend.php'">Test Forms & Backend</button>
            <button class="export-btn" onclick="refreshData()">Refresh Data</button>
            <button class="export-btn" onclick="location.href='../../../'">View Website</button>
        </div>
    </div>

    <script>
        function exportData(type) {
            const url = `export_data.php?type=${type}`;
            window.open(url, '_blank');
        }
        
        function refreshData() {
            location.reload();
        }
        
        // Auto-refresh every 30 seconds
        setInterval(refreshData, 30000);
    </script>
</body>
</html>
