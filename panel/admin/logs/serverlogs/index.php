<?php
session_start();
include('../../../func.php');

if (!isAdmin()) {
  header("Location: " . $website_config['site_url'] . "/");
  exit;
}

include("../../../database.php");

$tableName = "logs";
$columns = ['id','reason','date','ip'];
$fetchData = fetch_data($logs, $tableName, $columns);

function fetch_data($logs, $tableName, $columns) {
  if (empty($logs)) {
    
  } elseif (empty($columns) || !is_array($columns)) {
    
  } elseif (empty($tableName)) {
    
  } else {
    $columnName = implode(", ", $columns);
    $query = "SELECT * FROM `logs` ORDER by date DESC limit 150";
    $result = $logs->query($query);

    if ($result == true) {
      if ($result->num_rows > 0) {
        $row = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $msg = $row;
      } else {
        $msg = "no current logs";
      }
    } else {
      $msg = "logs not found";
      $err = mysqli_error($logs);
    }
  }
  return $msg;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $website_config['site_description']; ?>">
    <meta name="keywords" content="<?php echo $website_config['site_keywords']; ?>">
    <meta name="author" content="<?php echo $website_config['site_author']; ?>">
    <link rel="icon" href="https://<?php echo $website_config['site_domain']; ?>/assets/images/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="https://<?php echo $website_config['site_domain']; ?>/assets/images/favicon.png" type="image/x-icon">
    <title><?php echo $website_config['site_name']; ?> | Server Logs</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.min.js"></script>
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

        .stat-icon {
          width: 60px;
          height: 60px;
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 24px;
          color: white;
          background: linear-gradient(135deg, #3b82f6, #1d4ed8);
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

        /* Logs Table */
        .logs-table-card {
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 12px;
          overflow: hidden;
        }

        .card-header {
          padding: 1.5rem;
          border-bottom: 1px solid var(--border);
          display: flex;
          justify-content: space-between;
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

        .table-responsive {
          overflow-x: auto;
        }

        .table {
          width: 100%;
          border-collapse: collapse;
          margin: 0;
        }

        .table th {
          background: rgba(59, 130, 246, 0.1);
          border-bottom: 1px solid var(--border);
          padding: 1rem;
          text-align: left;
          font-weight: 600;
          color: var(--text-primary);
          white-space: nowrap;
        }

        .table td {
          padding: 1rem;
          border-bottom: 1px solid var(--border);
          color: var(--text-secondary);
        }

        .table tr:hover {
          background: rgba(59, 130, 246, 0.05);
        }

        .ip-address {
          font-family: monospace;
          font-size: 0.875rem;
          background: rgba(255, 255, 255, 0.05);
          padding: 0.25rem 0.5rem;
          border-radius: 4px;
          display: inline-block;
        }

        .log-reason {
          max-width: 300px;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }

        .log-timestamp {
          font-family: monospace;
          font-size: 0.875rem;
          color: var(--text-secondary);
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

        .btn {
          display: inline-flex;
          align-items: center;
          gap: 0.5rem;
          padding: 0.5rem 1rem;
          border: none;
          border-radius: 6px;
          font-size: 0.75rem;
          font-weight: 500;
          cursor: pointer;
          transition: all 0.2s ease;
          text-decoration: none;
        }

        .btn-primary {
          background: var(--primary);
          color: white;
        }

        .btn-primary:hover {
          background: var(--primary-dark);
        }

        .btn-info {
          background: var(--info);
          color: white;
        }

        .btn-info:hover {
          background: #0891b2;
        }

        .alert {
          padding: 1rem 1.5rem;
          border-radius: 8px;
          margin-bottom: 1.5rem;
          display: flex;
          align-items: center;
          gap: 0.75rem;
        }

        .alert-info {
          background: rgba(6, 182, 212, 0.1);
          border: 1px solid rgba(6, 182, 212, 0.2);
          color: var(--info);
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

          .table {
            font-size: 0.875rem;
          }

          .card-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
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
                        <a href="<?php echo $website_config['docs_url']; ?>" class="nav-link">
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
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/logs/serverlogs/" class="nav-link active">
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
                    <div class="header-title">Server Logs</div>
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
                <!-- Statistics Card -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="uil uil-terminal"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo is_array($fetchData) ? count($fetchData) : 0; ?></h3>
                            <p>Server Log Entries</p>
                        </div>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="alert alert-info">
                    <i class="uil uil-info-circle"></i>
                    <span>Showing the latest 150 server logs ordered by date. These logs contain server activity and system events.</span>
                </div>

                <!-- Logs Table -->
                <div class="logs-table-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="uil uil-list-ul"></i>
                            Server Logs
                        </h3>
                        <button class="btn btn-info" onclick="refreshLogs()">
                            <i class="uil uil-refresh"></i>
                            Refresh
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <caption>Logs of all Servers</caption>
                            <thead>
                                <tr>
                                    <th>Reason</th>
                                    <th>Date</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if(is_array($fetchData)){      
                                    $sn=1;
                                    foreach($fetchData as $data){
                                ?>
                                <tr>
                                    <td>
                                        <span class="log-reason" title="<?php echo htmlspecialchars($data['reason']??''); ?>">
                                            <?php echo htmlspecialchars($data['reason']??''); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="log-timestamp"><?php echo $data['date']??''; ?></span>
                                    </td>
                                    <td>
                                        <span class="ip-address"><?php echo $data['ip']??''; ?></span>
                                    </td>
                                </tr>
                                <?php
                                    $sn++;
                                    }
                                } else { 
                                ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                        <?php echo $fetchData; ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
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

        // Show Toast Notification
        function showToast(message, type) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                icon: type === 'success' ? 'success' : type === 'error' ? 'error' : 'info',
                title: message,
                background: '#1e293b',
                color: '#f8fafc',
                iconColor: type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#06b6d4'
            });
        }

        // Refresh Logs
        function refreshLogs() {
            showToast('Server logs refreshed successfully!', 'success');
            // Here you would typically reload the data from the server
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    </script>
</body>
</html>