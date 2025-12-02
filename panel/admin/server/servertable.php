<?php
session_start();
include('../../func.php');

if (!isAdmin()) {
  header("Location: " . $website_config['site_domain'] . "/");
  exit;
}

include '../database.php';

if (isset($_POST['delete_server']) && !empty($_POST['serverip'])) {
  $serverip = $_POST['serverip'];
  
  $deleteStmt = $conn->prepare("DELETE FROM server WHERE serverip = ?");
  $deleteStmt->bind_param("s", $serverip);
  $deleteStmt->execute();

  $deleteSuccess = false;
  $deleteError = false;

  if ($deleteStmt->affected_rows > 0) {
      $deleteSuccess = true;
  } else {
      $deleteError = true;
  }
  $deleteStmt->close();
}

$pageLimit = 200;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $pageLimit;

$query = "SELECT username, serverip, name, discord, is_blacklisted 
FROM users
JOIN users_server ON users_server.userid = users.userid
JOIN redem_license ON redem_license.serverid = users_server.serverid
JOIN server ON server.serverid = users_server.serverid";

if (isset($_POST['search']) && !empty($_POST['search'])) {
  $search = "%" . $_POST['search'] . "%";
  $query .= " WHERE username LIKE ? OR serverip LIKE ? OR name LIKE ? OR discord LIKE ? OR is_blacklisted LIKE ?";
  $stmt = $conn->prepare($query . " LIMIT ?, ?");
  $stmt->bind_param("ssssiii", $search, $search, $search, $search, $search, $offset, $pageLimit);
} else {
  $stmt = $conn->prepare($query . " LIMIT ?, ?");
  $stmt->bind_param("ii", $offset, $pageLimit);
}

$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$msg = $rows;

$stmt->close();
$conn->close();
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
    <title><?php echo $website_config['site_name']; ?> | Server Overview</title>
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

        /* Server Table */
        .server-table-card {
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

        .search-container {
          display: flex;
          align-items: center;
          gap: 1rem;
          margin-bottom: 1rem;
          padding: 0 1.5rem;
          padding-top: 1rem;
        }

        .search-input {
          flex: 1;
          padding: 0.75rem 1rem;
          border: 1px solid var(--border);
          border-radius: 8px;
          background: var(--bg-dark);
          color: var(--text-primary);
          font-size: 0.875rem;
        }

        .search-input:focus {
          outline: none;
          border-color: var(--primary);
          box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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

        .server-ip {
          font-family: monospace;
          font-size: 0.875rem;
          background: rgba(255, 255, 255, 0.05);
          padding: 0.25rem 0.5rem;
          border-radius: 4px;
          display: inline-block;
        }

        .blacklist-status {
          display: inline-flex;
          align-items: center;
          gap: 0.5rem;
          padding: 0.25rem 0.75rem;
          border-radius: 6px;
          font-size: 0.75rem;
          font-weight: 500;
        }

        .status-yes {
          background: rgba(239, 68, 68, 0.1);
          color: var(--danger);
          border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .status-no {
          background: rgba(16, 185, 129, 0.1);
          color: var(--success);
          border: 1px solid rgba(16, 185, 129, 0.2);
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

        .btn-danger {
          background: var(--danger);
          color: white;
        }

        .btn-danger:hover {
          background: #dc2626;
          transform: translateY(-1px);
        }

        .btn-primary {
          background: var(--primary);
          color: white;
        }

        .btn-primary:hover {
          background: var(--primary-dark);
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

        .alert {
          padding: 1rem 1.5rem;
          border-radius: 8px;
          margin-bottom: 1.5rem;
          display: flex;
          align-items: center;
          gap: 0.75rem;
        }

        .alert-success {
          background: rgba(16, 185, 129, 0.1);
          border: 1px solid rgba(16, 185, 129, 0.2);
          color: var(--success);
        }

        .alert-danger {
          background: rgba(239, 68, 68, 0.1);
          border: 1px solid rgba(239, 68, 68, 0.2);
          color: var(--danger);
        }

        .empty-state {
          text-align: center;
          padding: 3rem;
          color: var(--text-secondary);
        }

        .empty-state i {
          font-size: 4rem;
          margin-bottom: 1rem;
          opacity: 0.5;
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

          .search-container {
            flex-direction: column;
            align-items: stretch;
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
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/server/overview/" class="nav-link active">
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
                    <div class="header-title">Server Overview</div>
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
                <!-- Alerts for Success/Error -->
                <?php if (isset($deleteSuccess) && $deleteSuccess): ?>
                <div class="alert alert-success">
                    <i class="uil uil-check-circle"></i>
                    <span>Server successfully deleted</span>
                </div>
                <?php endif; ?>

                <?php if (isset($deleteError) && $deleteError): ?>
                <div class="alert alert-danger">
                    <i class="uil uil-times-circle"></i>
                    <span>Error deleting server</span>
                </div>
                <?php endif; ?>

                <!-- Statistics Card -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="uil uil-server"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo count($msg); ?></h3>
                            <p>Total Servers</p>
                        </div>
                    </div>
                </div>

                <!-- Server Table -->
                <div class="server-table-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="uil uil-list-ul"></i>
                            Servers
                        </h3>
                    </div>
                    
                    <div class="search-container">
                        <input type="text" id="searchInput" class="search-input" placeholder="Search servers...">
                    </div>

                    <div class="table-responsive">
                        <?php if (!empty($msg)): ?>
                        <table class="table">
                            <caption>List of users</caption>
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Server IP</th>
                                    <th>Server Name</th>
                                    <th>Discord</th>
                                    <th>Blacklisted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($msg as $data): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($data['username'] ?? ''); ?></td>
                                    <td>
                                        <span class="server-ip"><?php echo htmlspecialchars($data['serverip'] ?? ''); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($data['name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($data['discord'] ?? ''); ?></td>
                                    <td>
                                        <?php 
                                        $isBlacklisted = $data['is_blacklisted'] ?? '';
                                        if ($isBlacklisted == '1' || strtolower($isBlacklisted) == 'yes'):
                                        ?>
                                        <span class="blacklist-status status-yes">
                                            <i class="uil uil-times-circle"></i>
                                            Yes
                                        </span>
                                        <?php else: ?>
                                        <span class="blacklist-status status-no">
                                            <i class="uil uil-check-circle"></i>
                                            No
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action='' method='post' onsubmit="return confirmDelete(event)" style="display: inline;">
                                            <input type='hidden' name='serverip' value='<?php echo htmlspecialchars($data['serverip'] ?? ''); ?>'>
                                            <button class="btn btn-danger" type='submit' name='delete_server'>
                                                <i class="uil uil-trash-alt"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="uil uil-server"></i>
                            <h3>No Servers Found</h3>
                            <p>No server records are currently available in the system.</p>
                        </div>
                        <?php endif; ?>
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

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const table = document.querySelector('.table');
        
        if (searchInput && table) {
            searchInput.addEventListener('keyup', function() {
                const searchString = searchInput.value.toLowerCase();
                const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                
                for (let row of rows) {
                    const username = row.getElementsByTagName('td')[0]?.innerText.toLowerCase() || '';
                    const serverip = row.getElementsByTagName('td')[1]?.innerText.toLowerCase() || '';
                    const servername = row.getElementsByTagName('td')[2]?.innerText.toLowerCase() || '';
                    const discord = row.getElementsByTagName('td')[3]?.innerText.toLowerCase() || '';
                    
                    if (username.includes(searchString) || 
                        serverip.includes(searchString) || 
                        servername.includes(searchString) ||
                        discord.includes(searchString)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }

        // Confirm Delete with SweetAlert2
        function confirmDelete(event) {
            event.preventDefault();
            const form = event.target;
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                background: '#1e293b',
                color: '#f8fafc',
                iconColor: '#f59e0b'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            
            return false;
        }

        // Show Toast Notification
        function showToast(message, type) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                icon: type === 'success' ? 'success' : 'error',
                title: message,
                background: '#1e293b',
                color: '#f8fafc',
                iconColor: type === 'success' ? '#10b981' : '#ef4444'
            });
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });

        // Copy IP to clipboard
        function copyToClipboard(text, element) {
            navigator.clipboard.writeText(text).then(function() {
                showToast('Server IP copied to clipboard!', 'success');
                
                // Visual feedback
                const originalBg = element.style.background;
                element.style.background = 'rgba(16, 185, 129, 0.2)';
                setTimeout(() => {
                    element.style.background = originalBg;
                }, 200);
            }).catch(function(err) {
                showToast('Failed to copy IP address', 'error');
            });
        }

        // Add click to copy functionality to server IPs
        document.addEventListener('DOMContentLoaded', function() {
            const serverIPs = document.querySelectorAll('.server-ip');
            serverIPs.forEach(ip => {
                ip.style.cursor = 'pointer';
                ip.title = 'Click to copy IP address';
                ip.addEventListener('click', function() {
                    copyToClipboard(this.textContent, this);
                });
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            // Ctrl+F or Cmd+F for search
            if ((event.ctrlKey || event.metaKey) && event.key === 'f') {
                event.preventDefault();
                searchInput?.focus();
            }
            
            // Escape to clear search
            if (event.key === 'Escape') {
                if (searchInput && document.activeElement === searchInput) {
                    searchInput.value = '';
                    // Trigger search to show all rows
                    const event = new Event('keyup');
                    searchInput.dispatchEvent(event);
                }
            }
        });

        // Table row highlighting on hover
        document.addEventListener('DOMContentLoaded', function() {
            const tableRows = document.querySelectorAll('.table tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.background = 'rgba(59, 130, 246, 0.08)';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.background = '';
                });
            });
        });

        // Auto-refresh functionality (optional)
        let autoRefreshInterval;
        let isAutoRefreshEnabled = false;

        function toggleAutoRefresh() {
            if (isAutoRefreshEnabled) {
                clearInterval(autoRefreshInterval);
                isAutoRefreshEnabled = false;
                showToast('Auto-refresh disabled', 'info');
            } else {
                autoRefreshInterval = setInterval(() => {
                    location.reload();
                }, 30000); // Refresh every 30 seconds
                isAutoRefreshEnabled = true;
                showToast('Auto-refresh enabled (30s intervals)', 'success');
            }
        }

        // Add auto-refresh button if needed
        function addAutoRefreshButton() {
            const cardHeader = document.querySelector('.card-header');
            if (cardHeader) {
                const refreshBtn = document.createElement('button');
                refreshBtn.className = 'btn btn-primary';
                refreshBtn.onclick = toggleAutoRefresh;
                refreshBtn.innerHTML = '<i class="uil uil-sync"></i> Auto Refresh';
                cardHeader.appendChild(refreshBtn);
            }
        }

        // Initialize features
        document.addEventListener('DOMContentLoaded', function() {
            // Uncomment the line below if you want auto-refresh functionality
            // addAutoRefreshButton();
        });

        // PHP Success/Error handling with SweetAlert2
        <?php if (isset($deleteSuccess) && $deleteSuccess): ?>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Success!',
                text: 'Server successfully deleted',
                icon: 'success',
                confirmButtonText: 'OK',
                background: '#1e293b',
                color: '#f8fafc',
                confirmButtonColor: '#10b981'
            });
        });
        <?php endif; ?>

        <?php if (isset($deleteError) && $deleteError): ?>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Error!',
                text: 'Error deleting server',
                icon: 'error',
                confirmButtonText: 'OK',
                background: '#1e293b',
                color: '#f8fafc',
                confirmButtonColor: '#ef4444'
            });
        });
        <?php endif; ?>
    </script>

    <!-- Custom SweetAlert2 styles -->
    <style>
        .swal2-popup {
            background: #1e293b !important;
            color: #f8fafc !important;
        }
        
        .swal2-title {
            color: #f8fafc !important;
        }
        
        .swal2-content {
            color: #94a3b8 !important;
        }
    </style>
</body>
</html>