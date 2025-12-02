<?php
session_start();
include('../func.php');

if (!isAdmin()) {
  header("Location: " . $website_config['site_url'] . "/");
  exit;
}

// Safe default values
$total_screenshots = 0;
$total_connections = 0;
$total_bans = 0;
$total_joins = 0;
$total_servers = 0;
$active_servers = 0;

// Safely check if tables exist and get stats
$tables_to_check = ['screenshots', 'connections', 'bans', 'joins', 'servers'];
$existing_tables = [];

foreach ($tables_to_check as $table) {
    $check_query = "SHOW TABLES LIKE '$table'";
    $check_result = mysqli_query($link, $check_query);
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $existing_tables[] = $table;
    }
}

// Get stats only for existing tables
if (in_array('screenshots', $existing_tables)) {
    $result = mysqli_query($link, "SELECT COUNT(*) as count FROM screenshots WHERE date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total_screenshots = $row['count'];
    }
}

if (in_array('connections', $existing_tables)) {
    $result = mysqli_query($link, "SELECT COUNT(*) as count FROM connections WHERE date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total_connections = $row['count'];
    }
}

if (in_array('bans', $existing_tables)) {
    $result = mysqli_query($link, "SELECT COUNT(*) as count FROM bans WHERE date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total_bans = $row['count'];
    }
}

if (in_array('joins', $existing_tables)) {
    $result = mysqli_query($link, "SELECT COUNT(*) as count FROM joins WHERE date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total_joins = $row['count'];
    }
}

if (in_array('servers', $existing_tables)) {
    $result = mysqli_query($link, "SELECT COUNT(*) as count FROM servers");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total_servers = $row['count'];
    }
    
    $result = mysqli_query($link, "SELECT COUNT(*) as count FROM servers WHERE last_seen >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $active_servers = $row['count'];
    }
}

// Get sample data for demo
$recent_activity = [
    ['type' => 'screenshot', 'user' => 'PlayerABC', 'server' => 'Server1', 'date' => date('Y-m-d H:i:s', strtotime('-5 minutes'))],
    ['type' => 'connection', 'user' => 'PlayerXYZ', 'server' => 'Server2', 'date' => date('Y-m-d H:i:s', strtotime('-10 minutes'))],
    ['type' => 'ban', 'user' => 'Cheater123', 'server' => 'Server1', 'date' => date('Y-m-d H:i:s', strtotime('-15 minutes'))]
];

// Get sample servers
$servers = [
    ['name' => 'Main Server', 'ip' => '192.168.1.100', 'players' => '45', 'max_players' => '64', 'last_seen' => date('Y-m-d H:i:s'), 'version' => '1.0.0'],
    ['name' => 'Test Server', 'ip' => '192.168.1.101', 'players' => '12', 'max_players' => '32', 'last_seen' => date('Y-m-d H:i:s', strtotime('-10 minutes')), 'version' => '1.0.0'],
    ['name' => 'Dev Server', 'ip' => '192.168.1.102', 'players' => '3', 'max_players' => '16', 'last_seen' => date('Y-m-d H:i:s', strtotime('-2 hours')), 'version' => '0.9.5']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $website_config['site_name']; ?> | Server Overview</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
          --primary: #3b82f6;
          --primary-dark: #2563eb;
          --bg-dark: #0f172a;
          --bg-card: #1e293b;
          --bg-sidebar: #111827;
          --text-primary: #f8fafc;
          --text-secondary: #94a3b8;
          --border: #334155;
          --success: #10b981;
          --danger: #ef4444;
          --warning: #f59e0b;
          --info: #06b6d4;
        }

        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }

        body {
          font-family: 'Inter', sans-serif;
          background: var(--bg-dark);
          color: var(--text-primary);
          line-height: 1.6;
        }

        .dashboard-container {
          display: flex;
          min-height: 100vh;
        }

        .sidebar {
          width: 280px;
          background: var(--bg-sidebar);
          border-right: 1px solid var(--border);
          position: fixed;
          height: 100vh;
          overflow-y: auto;
          z-index: 1000;
        }

        .sidebar-header {
          padding: 1.5rem;
          border-bottom: 1px solid var(--border);
          display: flex;
          align-items: center;
          gap: 0.75rem;
        }

        .sidebar-logo {
          width: 40px;
          height: 40px;
          background: var(--primary);
          border-radius: 8px;
          display: flex;
          align-items: center;
          justify-content: center;
        }

        .sidebar-logo i {
          font-size: 20px;
          color: white;
        }

        .sidebar-brand {
          font-size: 1.25rem;
          font-weight: 600;
          color: var(--text-primary);
        }

        .sidebar-nav {
          padding: 1rem 0;
        }

        .nav-section {
          margin-bottom: 2rem;
        }

        .nav-section-title {
          padding: 0 1.5rem 0.5rem;
          font-size: 0.75rem;
          font-weight: 600;
          color: #64748b;
          text-transform: uppercase;
          letter-spacing: 0.5px;
        }

        .nav-item {
          margin: 0.25rem 1rem;
        }

        .nav-link {
          display: flex;
          align-items: center;
          gap: 0.75rem;
          padding: 0.75rem 1rem;
          color: var(--text-secondary);
          text-decoration: none;
          border-radius: 8px;
          transition: all 0.2s ease;
          font-weight: 500;
        }

        .nav-link:hover {
          background: rgba(59, 130, 246, 0.1);
          color: var(--primary);
        }

        .nav-link.active {
          background: var(--primary);
          color: white;
        }

        .nav-link i {
          font-size: 18px;
          width: 20px;
        }

        .main-content {
          margin-left: 280px;
          flex: 1;
          display: flex;
          flex-direction: column;
        }

        .header {
          background: var(--bg-card);
          border-bottom: 1px solid var(--border);
          padding: 1rem 2rem;
          display: flex;
          justify-content: space-between;
          align-items: center;
        }

        .header-title {
          font-size: 1.5rem;
          font-weight: 600;
          color: var(--text-primary);
        }

        .user-menu {
          display: flex;
          align-items: center;
          gap: 0.75rem;
          position: relative;
          cursor: pointer;
        }

        .user-avatar {
          width: 40px;
          height: 40px;
          border-radius: 50%;
          border: 2px solid var(--border);
        }

        .user-info {
          display: flex;
          flex-direction: column;
        }

        .user-name {
          font-size: 0.875rem;
          font-weight: 600;
          color: var(--text-primary);
        }

        .user-role {
          font-size: 0.75rem;
          color: var(--text-secondary);
        }

        .user-dropdown {
          position: absolute;
          top: 100%;
          right: 0;
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 8px;
          min-width: 200px;
          box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
          opacity: 0;
          visibility: hidden;
          transform: translateY(-10px);
          transition: all 0.3s ease;
          z-index: 1000;
          margin-top: 0.5rem;
        }

        .user-dropdown.show {
          opacity: 1;
          visibility: visible;
          transform: translateY(0);
        }

        .dropdown-item {
          display: flex;
          align-items: center;
          gap: 0.75rem;
          padding: 0.75rem 1rem;
          color: var(--text-secondary);
          text-decoration: none;
          border-bottom: 1px solid var(--border);
          transition: all 0.2s ease;
        }

        .dropdown-item:hover {
          background: rgba(59, 130, 246, 0.1);
          color: var(--primary);
        }

        .dropdown-arrow {
          margin-left: 0.5rem;
          transition: transform 0.3s ease;
        }

        .content-area {
          padding: 2rem;
          flex: 1;
        }

        .stats-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
          gap: 1.5rem;
          margin-bottom: 2rem;
        }

        .stat-card {
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 12px;
          padding: 1.5rem;
          display: flex;
          align-items: center;
          gap: 1rem;
          transition: all 0.2s ease;
          position: relative;
          overflow: hidden;
        }

        .stat-card:hover {
          transform: translateY(-2px);
          box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .stat-card::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          height: 3px;
          background: var(--primary);
        }

        .stat-card.screenshots::before { background: var(--info); }
        .stat-card.connections::before { background: var(--success); }
        .stat-card.bans::before { background: var(--danger); }
        .stat-card.joins::before { background: var(--warning); }

        .stat-icon {
          width: 60px;
          height: 60px;
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 24px;
          color: white;
        }

        .stat-icon.screenshots { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .stat-icon.connections { background: linear-gradient(135deg, #10b981, #047857); }
        .stat-icon.bans { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .stat-icon.joins { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-icon.servers { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }

        .stat-content h3 {
          font-size: 2rem;
          font-weight: 700;
          margin-bottom: 0.25rem;
          color: var(--text-primary);
        }

        .stat-content p {
          font-size: 0.875rem;
          color: var(--text-secondary);
          margin: 0;
        }

        .stat-trend {
          font-size: 0.75rem;
          display: flex;
          align-items: center;
          gap: 0.25rem;
          margin-top: 0.5rem;
        }

        .trend-up { color: var(--success); }
        .trend-down { color: var(--danger); }

        .content-grid {
          display: grid;
          grid-template-columns: 2fr 1fr;
          gap: 1.5rem;
          margin-bottom: 2rem;
        }

        .overview-card {
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 12px;
          overflow: hidden;
        }

        .card-header {
          padding: 1.5rem;
          border-bottom: 1px solid var(--border);
          display: flex;
          justify-content: between;
          align-items: center;
        }

        .card-title {
          font-size: 1.125rem;
          font-weight: 600;
          display: flex;
          align-items: center;
          gap: 0.75rem;
          color: var(--text-primary);
          margin: 0;
        }

        .card-title i {
          color: var(--primary);
          font-size: 1.25rem;
        }

        .card-body {
          padding: 1.5rem;
        }

        .chart-container {
          position: relative;
          height: 300px;
          margin: 1rem 0;
        }

        .activity-item {
          display: flex;
          align-items: center;
          gap: 1rem;
          padding: 1rem;
          border-bottom: 1px solid var(--border);
          transition: all 0.2s ease;
        }

        .activity-item:hover {
          background: rgba(59, 130, 246, 0.05);
        }

        .activity-item:last-child {
          border-bottom: none;
        }

        .activity-icon {
          width: 40px;
          height: 40px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 16px;
          color: white;
          flex-shrink: 0;
        }

        .activity-icon.screenshot { background: var(--info); }
        .activity-icon.ban { background: var(--danger); }
        .activity-icon.connection { background: var(--success); }

        .activity-content {
          flex: 1;
          min-width: 0;
        }

        .activity-content h4 {
          font-size: 0.875rem;
          font-weight: 600;
          margin-bottom: 0.25rem;
          color: var(--text-primary);
        }

        .activity-content p {
          font-size: 0.75rem;
          color: var(--text-secondary);
          margin: 0;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }

        .activity-time {
          font-size: 0.75rem;
          color: var(--text-secondary);
          flex-shrink: 0;
        }

        .server-list {
          grid-column: 1 / -1;
        }

        .server-table {
          width: 100%;
          border-collapse: collapse;
        }

        .server-table th {
          background: rgba(59, 130, 246, 0.1);
          border-bottom: 1px solid var(--border);
          padding: 1rem;
          text-align: left;
          font-weight: 600;
          color: var(--text-primary);
        }

        .server-table td {
          padding: 1rem;
          border-bottom: 1px solid var(--border);
          color: var(--text-secondary);
        }

        .server-table tr:hover {
          background: rgba(59, 130, 246, 0.05);
        }

        .server-status {
          display: inline-flex;
          align-items: center;
          gap: 0.5rem;
          padding: 0.25rem 0.75rem;
          border-radius: 6px;
          font-size: 0.75rem;
          font-weight: 500;
        }

        .status-online {
          background: rgba(16, 185, 129, 0.1);
          color: var(--success);
          border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-offline {
          background: rgba(239, 68, 68, 0.1);
          color: var(--danger);
          border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .status-dot {
          width: 8px;
          height: 8px;
          border-radius: 50%;
        }

        .status-online .status-dot { background: var(--success); }
        .status-offline .status-dot { background: var(--danger); }

        @media (max-width: 768px) {
          .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
          }

          .sidebar.open {
            transform: translateX(0);
          }

          .main-content {
            margin-left: 0;
          }

          .content-area {
            padding: 1rem;
          }

          .stats-grid {
            grid-template-columns: 1fr;
          }

          .content-grid {
            grid-template-columns: 1fr;
          }

          .server-table {
            font-size: 0.875rem;
          }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="uil uil-shield-check"></i>
                </div>
                <div class="sidebar-brand"><?php echo $website_config['site_name']; ?></div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">General</div>
                    <div class="nav-item">
                        <a href="/" class="nav-link">
                            <i class="uil uil-estate"></i>
                            <span>Home</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/account" class="nav-link">
                            <i class="uil uil-user"></i>
                            <span>Account</span>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Products</div>
                    <div class="nav-item">
                      <form id="download-form" action="/api/download/index.php" method="post" style="display: none;"></form>
                      <a href="#" onclick="document.getElementById('download-form').submit();" class="nav-link">
                          <i class="uil uil-download-alt"></i>
                          <span>Download <?php echo $website_config['site_name']; ?></span>
                      </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://config.fivesecurity.de" class="nav-link" target="_blank">
                            <i class="uil uil-setting"></i>
                            <span>Config Panel</span>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Information</div>
                    <div class="nav-item">
                        <a href="/tos" class="nav-link">
                            <i class="uil uil-file-alt"></i>
                            <span>Terms of Service</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/faq" class="nav-link">
                            <i class="uil uil-question-circle"></i>
                            <span>FAQ</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/refund" class="nav-link">
                            <i class="uil uil-credit-card"></i>
                            <span>Refund Policy</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="<?php echo $website_config['docs_url']; ?>" class="nav-link">
                            <i class="uil uil-book-open"></i>
                            <span>Documentation</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/discord" class="nav-link">
                            <i class="uil uil-discord"></i>
                            <span>Discord</span>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Admin</div>
                    <div class="nav-item">
                        <a href="/admin" class="nav-link">
                            <i class="uil uil-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/admin/server/overview" class="nav-link active">
                            <i class="uil uil-server"></i>
                            <span>Server Overview</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/admin/logs/authlogs" class="nav-link">
                            <i class="uil uil-file-search-alt"></i>
                            <span>Auth Logs</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/admin/auth/overview" class="nav-link">
                            <i class="uil uil-key-skeleton"></i>
                            <span>Key Management</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/admin/website/settings" class="nav-link">
                            <i class="uil uil-setting"></i>
                            <span>Settings</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/admin/website/config" class="nav-link">
                            <i class="uil uil-edit"></i>
                            <span>Config Editor</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <div class="main-content">
            <div class="header">
                <div class="header-title">Server Overview</div>
                <div class="user-menu" onclick="toggleUserDropdown()">
                    <img src="<?php echo $avatar; ?>" alt="User Avatar" class="user-avatar">
                    <div class="user-info">
                        <div class="user-name"><?php echo $_SESSION["username"]; ?></div>
                        <div class="user-role"><?php echo $_SESSION["group"]; ?></div>
                    </div>
                    <i class="uil uil-angle-down dropdown-arrow"></i>
                    
                    <div class="user-dropdown" id="userDropdown">
                        <a href="/account" class="dropdown-item">
                            <i class="uil uil-user"></i>
                            <span>Account</span>
                        </a>
                        <a href="/logout.php" class="dropdown-item">
                            <i class="uil uil-signout"></i>
                            <span>Log out</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-area">
                <!-- Coming Soon Message -->
                <div style="display: flex; align-items: center; justify-content: center; min-height: 60vh; text-align: center;">
                    <div style="max-width: 500px;">
                        <div style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
                            <i class="uil uil-server" style="font-size: 60px; color: white;"></i>
                        </div>
                        <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-primary);">Soon</h1>
                        <p style="font-size: 1.125rem; color: var(--text-secondary); margin-bottom: 2rem; line-height: 1.6;">
                            The Server Overview dashboard is currently under development. 
                            <br>Check back soon for comprehensive server monitoring and analytics.
                        </p>
                        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                            <a href="/admin" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: var(--primary); color: white; text-decoration: none; border-radius: 8px; font-weight: 500; transition: all 0.2s ease;">
                                <i class="uil uil-arrow-left"></i>
                                Back to Dashboard
                            </a>
                            <a href="/admin/website/settings" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: transparent; color: var(--text-primary); text-decoration: none; border: 1px solid var(--border); border-radius: 8px; font-weight: 500; transition: all 0.2s ease;">
                                <i class="uil uil-setting"></i>
                                Website Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            const userMenu = document.querySelector('.user-menu');
            
            dropdown.classList.toggle('show');
            userMenu.classList.toggle('active');
        }

        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.getElementById('userDropdown');
            
            if (!userMenu.contains(event.target)) {
                dropdown.classList.remove('show');
                userMenu.classList.remove('active');
            }
        });

        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('open');
        }

        if (window.innerWidth <= 768) {
            const header = document.querySelector('.header');
            const menuButton = document.createElement('button');
            menuButton.innerHTML = '<i class="uil uil-bars"></i>';
            menuButton.style.cssText = 'background: none; border: none; color: var(--text-primary); font-size: 1.5rem; cursor: pointer; margin-right: 1rem;';
            menuButton.onclick = toggleSidebar;
            header.insertBefore(menuButton, header.firstChild);
        }

        // Initialize Activity Chart
        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['6h ago', '5h ago', '4h ago', '3h ago', '2h ago', '1h ago', 'Now'],
                datasets: [
                    {
                        label: 'Screenshots',
                        data: [65, 78, 66, 89, 72, 85, 92],
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Connections',
                        data: [45, 52, 48, 61, 55, 67, 73],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Bans',
                        data: [8, 12, 7, 15, 9, 6, 4],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#f8fafc',
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#334155'
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    },
                    x: {
                        grid: {
                            color: '#334155'
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Auto-refresh data every 30 seconds
        setInterval(function() {
            console.log('Auto-refresh triggered');
            // Here you would typically make AJAX calls to update the data
        }, 30000);

        // Animate numbers on page load
        function animateNumber(element, target, duration = 1000) {
            const start = 0;
            const increment = target / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current).toLocaleString();
            }, 16);
        }

        // Animate all stat numbers on load
        document.addEventListener('DOMContentLoaded', function() {
            const statElements = document.querySelectorAll('.stat-content h3');
            
            statElements.forEach(element => {
                const originalText = element.textContent;
                const number = parseInt(originalText.replace(/,/g, ''));
                if (!isNaN(number)) {
                    element.textContent = '0';
                    animateNumber(element, number);
                }
            });
        });

        // Add real-time server status updates
        function updateServerStatus() {
            const serverRows = document.querySelectorAll('.server-table tbody tr');
            
            serverRows.forEach(row => {
                const statusElement = row.querySelector('.server-status');
                if (statusElement && Math.random() > 0.95) {
                    // Simulate occasional status changes
                    const isOnline = statusElement.classList.contains('status-online');
                    statusElement.className = `server-status ${isOnline ? 'status-offline' : 'status-online'}`;
                    statusElement.innerHTML = `
                        <div class="status-dot"></div>
                        ${isOnline ? 'Offline' : 'Online'}
                    `;
                }
            });
        }

        // Update server status every 15 seconds
        setInterval(updateServerStatus, 15000);

        // Add smooth transitions for cards
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.stat-card, .overview-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });

        // Legacy function for compatibility
        function updateNumRows(elementId, type) {
            console.log(`Legacy update function called: ${elementId} - ${type}`);
        }

        // Simulate the original script behavior
        window.onload = function () {
            updateNumRows("liveScreens", "totalscreenshots");
            updateNumRows("liveAuth", "totalconnections");
            updateNumRows("liveBans", "totalbans");
            updateNumRows("liveJoins", "totaljoins");
        }
    </script>
</body>
</html>