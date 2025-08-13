<?php
/**
 * Enhanced Admin Dashboard
 * Requires authentication via session
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

// Debug session variables
error_log('Session check: ' . print_r($_SESSION, true));

// Check if user is authenticated
if (!isset($_SESSION['admin_id'])) {
    error_log('Session check failed - redirecting to login');
    header('Location: login.php');
    exit();
}

error_log('Session check passed - admin_id: ' . $_SESSION['admin_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPP Admin Dashboard</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .loading { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .dashboard-card { transition: transform 0.2s, box-shadow 0.2s; }
        .dashboard-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-blue-800 text-white p-4 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <img src="../../public/assets/images/mpp_logo.png" alt="MPP Logo" class="h-8 w-auto">
                <h1 class="text-xl font-bold">MPP Admin Dashboard</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span id="admin-name" class="font-medium">Loading...</span>
                <button onclick="logout()" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded transition-colors">
                    Logout
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto p-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="dashboard-card bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Total Volunteers</h3>
                        <p id="total-volunteers" class="text-2xl font-bold text-gray-900">-</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Approved</h3>
                        <p id="approved-volunteers" class="text-2xl font-bold text-gray-900">-</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Pending</h3>
                        <p id="pending-volunteers" class="text-2xl font-bold text-gray-900">-</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Prayer Signups</h3>
                        <p id="total-prayers" class="text-2xl font-bold text-gray-900">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Tabs -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('volunteers')" id="volunteers-tab" class="tab-button border-b-2 border-blue-500 text-blue-600 py-4 px-1 text-sm font-medium">
                        Volunteers
                    </button>
                    <button onclick="showTab('prayers')" id="prayers-tab" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                        Prayer Signups
                    </button>
                    <button onclick="showTab('reports')" id="reports-tab" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                        Reports
                    </button>
                    <button onclick="showTab('settings')" id="settings-tab" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                        Settings
                    </button>
                </nav>
            </div>

            <!-- Volunteers Tab -->
            <div id="volunteers-content" class="tab-content p-6">
                <!-- Filters -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" id="search-input" placeholder="Search volunteers..." class="border border-gray-300 rounded-md px-3 py-2">
                    <select id="status-filter" class="border border-gray-300 rounded-md px-3 py-2">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <select id="service-filter" class="border border-gray-300 rounded-md px-3 py-2">
                        <option value="">All Services</option>
                        <option value="prayer">Prayer</option>
                        <option value="worship">Worship</option>
                        <option value="children">Children</option>
                        <option value="youth">Youth</option>
                        <option value="administration">Administration</option>
                        <option value="technical">Technical</option>
                        <option value="outreach">Outreach</option>
                    </select>
                    <button onclick="searchVolunteers()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Search
                    </button>
                </div>

                <!-- Bulk Actions -->
                <div class="mb-4 flex space-x-2">
                    <button onclick="bulkApprove()" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                        Bulk Approve
                    </button>
                    <button onclick="bulkReject()" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                        Bulk Reject
                    </button>
                    <button onclick="exportData('csv')" class="bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700">
                        Export CSV
                    </button>
                </div>

                <!-- Volunteers Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="volunteers-table-body" class="divide-y divide-gray-200">
                            <!-- Volunteers will be loaded here -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div id="pagination" class="mt-6 flex justify-between items-center">
                    <!-- Pagination controls will be inserted here -->
                </div>
            </div>

            <!-- Prayer Signups Tab -->
            <div id="prayers-content" class="tab-content p-6 hidden">
                <div id="prayers-table">
                    <!-- Prayer signups content will be loaded here -->
                </div>
            </div>

            <!-- Reports Tab -->
            <div id="reports-content" class="tab-content p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Volunteers by Service Type</h3>
                        <canvas id="serviceChart"></canvas>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Geographic Distribution</h3>
                        <canvas id="geoChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settings-content" class="tab-content p-6 hidden">
                <div class="max-w-md">
                    <h3 class="text-lg font-semibold mb-4">Email Settings</h3>
                    <button onclick="processEmailQueue()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4">
                        Process Email Queue
                    </button>
                    <button onclick="testEmail()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" style="display: flex; align-items: center; justify-content: center;">
        <div class="bg-white p-6 rounded-lg">
            <div class="loading w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full mx-auto mb-4"></div>
            <p class="text-center">Loading...</p>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let currentTab = 'volunteers';
        let selectedVolunteers = new Set();

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadStatistics();
            loadVolunteers();
        });

        // Authentication check
        async function checkAuth() {
            try {
                const response = await fetch('api/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({ action: 'check_session' })
                });
                
                const data = await response.json();
                console.log('Session check response:', data);
                if (!data.success) {
                    console.log('Session check failed, redirecting to login');
                    window.location.href = 'login.php';
                }
                
                document.getElementById('admin-name').textContent = data.admin.username;
            } catch (error) {
                console.error('Auth check failed:', error);
                window.location.href = 'login.php';
            }
        }

        // Logout
        async function logout() {
            try {
                await fetch('api/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'logout' })
                });
                window.location.href = 'login.php';
            } catch (error) {
                console.error('Logout failed:', error);
                window.location.href = 'login.php';
            }
        }

        // Load statistics
        async function loadStatistics() {
            try {
                const response = await fetch('api/statistics.php');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('total-volunteers').textContent = data.data.total_volunteers;
                    document.getElementById('approved-volunteers').textContent = data.data.approved_volunteers;
                    document.getElementById('pending-volunteers').textContent = data.data.pending_volunteers;
                    document.getElementById('total-prayers').textContent = data.data.total_prayers;
                }
            } catch (error) {
                console.error('Failed to load statistics:', error);
            }
        }

        // Load volunteers
        async function loadVolunteers(page = 1) {
            showLoading();
            try {
                const search = document.getElementById('search-input')?.value || '';
                const status = document.getElementById('status-filter')?.value || '';
                const service = document.getElementById('service-filter')?.value || '';
                
                const params = new URLSearchParams({
                    page: page,
                    limit: 20,
                    search: search,
                    status: status,
                    service_type: service
                });

                const response = await fetch(`api/volunteers.php?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    displayVolunteers(data.data);
                    displayPagination(data.pagination);
                }
            } catch (error) {
                console.error('Failed to load volunteers:', error);
            } finally {
                hideLoading();
            }
        }

        // Display volunteers in table
        function displayVolunteers(volunteers) {
            const tbody = document.getElementById('volunteers-table-body');
            tbody.innerHTML = '';
            
            volunteers.forEach(volunteer => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-3">
                        <input type="checkbox" value="${volunteer.id}" onchange="toggleVolunteerSelection(${volunteer.id})">
                    </td>
                    <td class="px-4 py-3">${volunteer.full_name}</td>
                    <td class="px-4 py-3">${volunteer.email}</td>
                    <td class="px-4 py-3">${volunteer.service_type}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full ${getStatusColor(volunteer.status)}">
                            ${volunteer.status}
                        </span>
                    </td>
                    <td class="px-4 py-3">${volunteer.state}, ${volunteer.country}</td>
                    <td class="px-4 py-3">
                        <div class="flex space-x-2">
                            <button onclick="approveVolunteer(${volunteer.id})" class="text-green-600 hover:text-green-800 text-sm">Approve</button>
                            <button onclick="rejectVolunteer(${volunteer.id})" class="text-red-600 hover:text-red-800 text-sm">Reject</button>
                            <button onclick="viewVolunteer(${volunteer.id})" class="text-blue-600 hover:text-blue-800 text-sm">View</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // Get status color class
        function getStatusColor(status) {
            switch (status) {
                case 'approved': return 'bg-green-100 text-green-800';
                case 'pending': return 'bg-yellow-100 text-yellow-800';
                case 'rejected': return 'bg-red-100 text-red-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        // Tab management
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(tab => {
                tab.classList.remove('border-blue-500', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab content
            document.getElementById(`${tabName}-content`).classList.remove('hidden');
            
            // Add active class to selected tab
            const activeTab = document.getElementById(`${tabName}-tab`);
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-blue-500', 'text-blue-600');
            
            currentTab = tabName;
            
            // Load tab-specific content
            if (tabName === 'reports') {
                loadReports();
            }
        }

        // Search volunteers
        function searchVolunteers() {
            currentPage = 1;
            loadVolunteers();
        }

        // Volunteer selection
        function toggleVolunteerSelection(id) {
            if (selectedVolunteers.has(id)) {
                selectedVolunteers.delete(id);
            } else {
                selectedVolunteers.add(id);
            }
        }

        function toggleSelectAll() {
            const checkboxes = document.querySelectorAll('#volunteers-table-body input[type="checkbox"]');
            const selectAll = document.getElementById('select-all').checked;
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll;
                const id = parseInt(checkbox.value);
                if (selectAll) {
                    selectedVolunteers.add(id);
                } else {
                    selectedVolunteers.delete(id);
                }
            });
        }

        // Volunteer actions
        async function approveVolunteer(id) {
            try {
                const response = await fetch(`api/volunteers.php/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: 'approved' })
                });
                
                const data = await response.json();
                if (data.success) {
                    loadVolunteers(currentPage);
                    loadStatistics();
                } else {
                    alert('Failed to approve volunteer: ' + data.message);
                }
            } catch (error) {
                console.error('Failed to approve volunteer:', error);
            }
        }

        async function rejectVolunteer(id) {
            const reason = prompt('Please provide a reason for rejection:');
            if (!reason) return;
            
            try {
                const response = await fetch(`api/volunteers.php/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        status: 'rejected',
                        rejection_reason: reason
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    loadVolunteers(currentPage);
                    loadStatistics();
                } else {
                    alert('Failed to reject volunteer: ' + data.message);
                }
            } catch (error) {
                console.error('Failed to reject volunteer:', error);
            }
        }

        // Bulk operations
        async function bulkApprove() {
            if (selectedVolunteers.size === 0) {
                alert('Please select volunteers to approve');
                return;
            }
            
            try {
                const response = await fetch('api/volunteers.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'approve_bulk',
                        volunteer_ids: Array.from(selectedVolunteers)
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    selectedVolunteers.clear();
                    loadVolunteers(currentPage);
                    loadStatistics();
                } else {
                    alert('Bulk approval failed: ' + data.message);
                }
            } catch (error) {
                console.error('Bulk approval failed:', error);
            }
        }

        async function bulkReject() {
            if (selectedVolunteers.size === 0) {
                alert('Please select volunteers to reject');
                return;
            }
            
            const reason = prompt('Please provide a reason for rejection:');
            if (!reason) return;
            
            try {
                const response = await fetch('api/volunteers.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'reject_bulk',
                        volunteer_ids: Array.from(selectedVolunteers),
                        rejection_reason: reason
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    selectedVolunteers.clear();
                    loadVolunteers(currentPage);
                    loadStatistics();
                } else {
                    alert('Bulk rejection failed: ' + data.message);
                }
            } catch (error) {
                console.error('Bulk rejection failed:', error);
            }
        }

        // Export data
        async function exportData(format) {
            try {
                const response = await fetch(`api/export.php?type=volunteers&format=${format}`);
                const blob = await response.blob();
                
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `volunteers_export.${format}`;
                a.click();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Export failed:', error);
            }
        }

        // Reports
        async function loadReports() {
            showLoading();
            try {
                const response = await fetch('api/statistics.php');
                const data = await response.json();

                if (data.success) {
                    const chartColors = {
                        primary: 'hsl(222.2 47.4% 11.2%)',
                        secondary: 'hsl(210 40% 96.1%)',
                        accent: 'hsl(210 40% 98%)',
                        text: 'hsl(215.4 16.3% 46.9%)',
                        border: 'hsl(214.3 31.8% 91.4%)',
                        
                        // Chart specific colors from a shadcn-like palette
                        palette: [
                            '#2563eb', '#84cc16', '#f97316', '#f59e0b',
                            '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'
                        ]
                    };

                    // Destroy existing charts if they exist
                    if (window.serviceChart instanceof Chart) {
                        window.serviceChart.destroy();
                    }
                    if (window.geoChart instanceof Chart) {
                        window.geoChart.destroy();
                    }

                    // Create service type chart (Doughnut)
                    const serviceCtx = document.getElementById('serviceChart').getContext('2d');
                    window.serviceChart = new Chart(serviceCtx, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(data.data.service_distribution),
                            datasets: [{
                                data: Object.values(data.data.service_distribution),
                                backgroundColor: chartColors.palette,
                                borderColor: chartColors.accent,
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: chartColors.text,
                                        font: { size: 12 }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: chartColors.primary,
                                    titleColor: chartColors.accent,
                                    bodyColor: chartColors.secondary,
                                    cornerRadius: 8,
                                    padding: 12
                                }
                            }
                        }
                    });

                    // Create geographic chart (Bar)
                    const geoCtx = document.getElementById('geoChart').getContext('2d');
                    window.geoChart = new Chart(geoCtx, {
                        type: 'bar',
                        data: {
                            labels: Object.keys(data.data.geographical_distribution),
                            datasets: [{
                                label: 'Volunteers',
                                data: Object.values(data.data.geographical_distribution),
                                backgroundColor: chartColors.palette[0],
                                borderRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: chartColors.border },
                                    ticks: { color: chartColors.text }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: chartColors.text }
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: chartColors.primary,
                                    titleColor: chartColors.accent,
                                    bodyColor: chartColors.secondary,
                                    cornerRadius: 8,
                                    padding: 12
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Failed to load reports:', error);
            } finally {
                hideLoading();
            }
        }

        // Display pagination
        function displayPagination(pagination) {
            const paginationDiv = document.getElementById('pagination');
            const { page, pages, total } = pagination;
            
            paginationDiv.innerHTML = `
                <div class="text-sm text-gray-700">
                    Showing page ${page} of ${pages} (${total} total)
                </div>
                <div class="space-x-2">
                    ${page > 1 ? `<button onclick="loadVolunteers(${page - 1})" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Previous</button>` : ''}
                    ${page < pages ? `<button onclick="loadVolunteers(${page + 1})" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Next</button>` : ''}
                </div>
            `;
        }

        // Utility functions
        function showLoading() {
            document.getElementById('loading-overlay').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loading-overlay').classList.add('hidden');
        }

        // Process email queue
        async function processEmailQueue() {
            try {
                const response = await fetch('api/statistics.php?action=process_email_queue', {
                    method: 'POST'
                });
                const data = await response.json();
                alert(data.message || 'Email queue processed');
            } catch (error) {
                console.error('Failed to process email queue:', error);
            }
        }

        // View volunteer details
        function viewVolunteer(id) {
            // This could open a modal or navigate to a detail page
            window.open(`volunteer_details.php?id=${id}`, '_blank');
        }
    </script>
</body>
</html>
