<?php
session_start();
include('../../../func.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $website_config['site_description']; ?>">
    <meta name="keywords" content="<?php echo $website_config['site_keywords']; ?>">
    <meta name="author" content="<?php echo $website_config['site_author']; ?>">
    <link rel="icon" href="<?php echo $website_config['site_favicon']; ?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo $website_config['site_favicon']; ?>" type="image/x-icon">
    <title><?php echo $website_config['site_name']; ?> | Admin Dashboard</title>
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

        .sidebar-logo img {
          max-width: 100%;
          max-height: 100%;
          border-radius: 4px;
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

        .dropdown-item:last-child {
          border-bottom: none;
        }

        .dropdown-arrow {
          margin-left: 0.5rem;
          transition: transform 0.3s ease;
        }

        .content-area {
          padding: 2rem;
          flex: 1;
        }

        /* Statistics Cards */
        .stats-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
        }

        .stat-card.cpu::before { background: var(--primary); }
        .stat-card.ram::before { background: var(--success); }
        .stat-card.storage::before { background: var(--warning); }
        .stat-card.screens::before { background: var(--info); }

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

        .stat-icon.cpu { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .stat-icon.ram { background: linear-gradient(135deg, #10b981, #047857); }
        .stat-icon.storage { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-icon.screens { background: linear-gradient(135deg, #06b6d4, #0891b2); }

        .stat-content {
          flex: 1;
        }

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

        .stat-chart {
          width: 60px;
          height: 40px;
        }

        .mobile-menu-toggle {
          display: none;
          background: none;
          border: none;
          color: var(--text-primary);
          font-size: 1.5rem;
          cursor: pointer;
        }

        .footer {
          background: var(--bg-card);
          border-top: 1px solid var(--border);
          padding: 1rem 2rem;
          text-align: center;
          color: var(--text-secondary);
          font-size: 0.875rem;
        }

        .live-indicator {
          display: inline-flex;
          align-items: center;
          gap: 0.5rem;
          font-size: 0.75rem;
          color: var(--success);
          margin-bottom: 0.5rem;
        }

        .live-dot {
          width: 8px;
          height: 8px;
          background: var(--success);
          border-radius: 50%;
          animation: pulse 2s infinite;
        }

        @keyframes pulse {
          0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
          }
          70% {
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
          }
          100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
          }
        }

        .refresh-time {
          font-size: 0.75rem;
          color: var(--text-secondary);
          margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
          .mobile-menu-toggle {
            display: block;
          }

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

          .header {
            padding: 1rem;
          }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="<?php echo $website_config['site_logo']; ?>" alt="Logo">
                </div>
                <div class="sidebar-brand"><?php echo $website_config['site_name']; ?></div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">General</div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>" class="nav-link">
                            <i class="uil uil-estate"></i>
                            <span>Home</span>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Information</div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/tos/" class="nav-link">
                            <i class="uil uil-file-alt"></i>
                            <span>T.O.S</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/faq/" class="nav-link">
                            <i class="uil uil-question-circle"></i>
                            <span>FAQ</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="" class="nav-link">
                            <i class="uil uil-book-open"></i>
                            <span>Docs</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/discord" class="nav-link">
                            <i class="uil uil-discord"></i>
                            <span>Discord</span>
                        </a>
                    </div>
                </div>

                <?php if ($_SESSION["group"] == "admin"): ?>
                <div class="nav-section">
                    <div class="nav-section-title">Admin</div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/server/overview/" class="nav-link">
                            <i class="uil uil-server"></i>
                            <span>Server Overview</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/server/servertable.php" class="nav-link">
                            <i class="uil uil-table"></i>
                            <span>Server Table</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/logs/authlogs/" class="nav-link">
                            <i class="uil uil-file-search-alt"></i>
                            <span>Auth Logs</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/logs/serverlogs/" class="nav-link">
                            <i class="uil uil-terminal"></i>
                            <span>Server Logs</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/auth/overview" class="nav-link">
                            <i class="uil uil-key-skeleton"></i>
                            <span>Key Overview</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/auth/keygenerator" class="nav-link">
                            <i class="uil uil-plus-circle"></i>
                            <span>Key Creator</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/website/settings/" class="nav-link">
                            <i class="uil uil-setting"></i>
                            <span>Settings</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/website/config/" class="nav-link">
                            <i class="uil uil-edit"></i>
                            <span>Config</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                        <i class="uil uil-bars"></i>
                    </button>
                    <div class="header-title">Admin Dashboard</div>
                </div>
                <div class="user-menu" onclick="toggleUserDropdown()">
                    <img class="user-avatar" src="<?php echo $avatar; ?>" alt="Avatar">
                    <div class="user-info">
                        <div class="user-name"><?php echo $_SESSION["username"]; ?></div>
                        <div class="user-role"><?php echo $_SESSION["group"]; ?></div>
                    </div>
                    <i class="uil uil-angle-down dropdown-arrow"></i>
                    
                    <div class="user-dropdown" id="userDropdown">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/account" class="dropdown-item">
                            <i class="uil uil-user"></i>
                            <span>Account</span>
                        </a>
                        <a href="https://<?php echo $website_config['site_domain']; ?>/account/" class="dropdown-item">
                            <i class="uil uil-setting"></i>
                            <span>Settings</span>
                        </a>
                        <a href="https://<?php echo $website_config['site_domain']; ?>/logout.php" class="dropdown-item">
                            <i class="uil uil-signout"></i>
                            <span>Log out</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Live Indicator -->
                <div class="live-indicator">
                    <div class="live-dot"></div>
                    <span>Live Monitoring</span>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card cpu">
                        <div class="stat-icon cpu">
                            <i class="uil uil-processor"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="cpu">Loading...</h3>
                            <p>CPU Usage</p>
                            <div class="refresh-time">Auto-refreshes every second</div>
                        </div>
                        <div class="stat-chart">
                            <canvas id="cpuChart" width="60" height="40"></canvas>
                        </div>
                    </div>

                    <div class="stat-card ram">
                        <div class="stat-icon ram">
                            <i class="uil uil-microchip"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="ram">Loading...</h3>
                            <p>RAM Usage</p>
                            <div class="refresh-time">Auto-refreshes every second</div>
                        </div>
                        <div class="stat-chart">
                            <canvas id="ramChart" width="60" height="40"></canvas>
                        </div>
                    </div>

                    <div class="stat-card storage">
                        <div class="stat-icon storage">
                            <i class="uil uil-hard-drive"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="size">Loading...</h3>
                            <p>Screenshots Storage</p>
                            <div class="refresh-time">Auto-refreshes every second</div>
                        </div>
                        <div class="stat-chart">
                            <canvas id="storageChart" width="60" height="40"></canvas>
                        </div>
                    </div>

                    <div class="stat-card screens">
                        <div class="stat-icon screens">
                            <i class="uil uil-camera"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="count">Loading...</h3>
                            <p>Total Screenshots</p>
                            <div class="refresh-time">Auto-refreshes every second</div>
                        </div>
                        <div class="stat-chart">
                            <canvas id="screensChart" width="60" height="40"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12 footer-copyright text-center">
                            <p class="mb-0">2022-<script>document.write(new Date().getFullYear())</script> <?php echo $website_config['site_copyright']; ?></p> 
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Original PHP functionality preserved
        window.onload = function () {
            updateNumRows("liveScreens", "totalscreenshots");
            updateNumRows("liveAuth", "totalconnections");
            updateNumRows("liveBans", "totalbans");
            updateNumRows("liveJoins", "totaljoins");
        }

        // Auto-refresh every second (original functionality)
        setInterval(function () {
            updateNumRows("cpu");
            updateNumRows("ram");
            updateNumRows("size");
            updateNumRows("count");
        }, 1000);

        function updateNumRows(idName) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById(idName).innerHTML = this.responseText;
                    
                    // Update charts when data is received
                    updateChart(idName, this.responseText);
                }
            };
            xhttp.open("GET", "getNumRows.php?id=" + idName, true);
            xhttp.send(idName);
        }

        // Toggle Sidebar for Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }

        // Toggle User Dropdown
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            const arrow = document.querySelector('.dropdown-arrow');
            
            dropdown.classList.toggle('show');
            arrow.style.transform = dropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.getElementById('userDropdown');
            
            if (!userMenu.contains(event.target)) {
                dropdown.classList.remove('show');
                document.querySelector('.dropdown-arrow').style.transform = 'rotate(0deg)';
            }
        });

        // Charts for visual representation
        let cpuChart, ramChart, storageChart, screensChart;
        let chartData = {
            cpu: [],
            ram: [],
            size: [],
            count: []
        };

        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });

        function initializeCharts() {
            const chartConfig = {
                type: 'line',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { display: false },
                        y: { display: false }
                    },
                    plugins: {
                        legend: { display: false }
                    },
                    elements: {
                        point: { radius: 0 },
                        line: { tension: 0.4 }
                    }
                }
            };

            // CPU Chart
            cpuChart = new Chart(document.getElementById('cpuChart'), {
                ...chartConfig,
                data: {
                    labels: Array(10).fill(''),
                    datasets: [{
                        data: Array(10).fill(0),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true
                    }]
                }
            });

            // RAM Chart
            ramChart = new Chart(document.getElementById('ramChart'), {
                ...chartConfig,
                data: {
                    labels: Array(10).fill(''),
                    datasets: [{
                        data: Array(10).fill(0),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true
                    }]
                }
            });

            // Storage Chart
            storageChart = new Chart(document.getElementById('storageChart'), {
                ...chartConfig,
                data: {
                    labels: Array(10).fill(''),
                    datasets: [{
                        data: Array(10).fill(0),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        fill: true
                    }]
                }
            });

            // Screenshots Chart
            screensChart = new Chart(document.getElementById('screensChart'), {
                ...chartConfig,
                data: {
                    labels: Array(10).fill(''),
                    datasets: [{
                        data: Array(10).fill(0),
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        fill: true
                    }]
                }
            });
        }

        function updateChart(metric, value) {
            let chart;
            const numValue = parseFloat(value) || 0;

            switch(metric) {
                case 'cpu':
                    chart = cpuChart;
                    break;
                case 'ram':
                    chart = ramChart;
                    break;
                case 'size':
                    chart = storageChart;
                    break;
                case 'count':
                    chart = screensChart;
                    break;
                default:
                    return;
            }

            if (chart && chart.data.datasets[0]) {
                // Keep only last 10 data points
                if (chartData[metric].length >= 10) {
                    chartData[metric].shift();
                }
                chartData[metric].push(numValue);

                chart.data.datasets[0].data = [...chartData[metric]];
                chart.update('none');
            }
        }

        // Add some visual feedback when data updates
        function highlightUpdate(elementId) {
            const element = document.getElementById(elementId);
            if (element) {
                element.style.transition = 'color 0.3s ease';
                element.style.color = '#10b981';
                setTimeout(() => {
                    element.style.color = '';
                }, 300);
            }
        }

        // Enhanced updateNumRows with visual feedback
        const originalUpdateNumRows = updateNumRows;
        updateNumRows = function(idName) {
            const xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById(idName).innerHTML = this.responseText;
                    highlightUpdate(idName);
                    updateChart(idName, this.responseText);
                }
            };
            xhttp.open("GET", "getNumRows.php?id=" + idName, true);
            xhttp.send(idName);
        };
    </script>
</body>
</html>