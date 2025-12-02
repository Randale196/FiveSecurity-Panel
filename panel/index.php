<?php
require_once __DIR__ . '/debug.php'; // Dont remove it!
session_start();
include('func.php'); // Dont remove it!
include('config.php'); // Dont remove it!

if (!$conn) {
    die("Error: " . mysqli_connect_error());
}

// Server status check logic
$sql = "SELECT server.serverip, server.latestres_name AS resname, server.port 
FROM server 
JOIN users_server ON users_server.userid = ? 
WHERE server.serverid = users_server.serverid";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $_SESSION["id"]);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
  $ip = $row['serverip'];
  $port = $row['port'];
  $resname = $row['resname'];
  $url = "http://" . $ip . ":" . $port . "/info.json";
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
  curl_setopt($curl, CURLOPT_TIMEOUT, 2);
  curl_setopt($curl, CURLOPT_HEADER, false);
  $data = curl_exec($curl);
  $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  curl_close($curl);
  if ($httpcode >= 200 && $httpcode < 400) {
    $data = json_decode($data, true);
    if (array_key_exists("resources", $data)) {
      if (in_array($resname, $data["resources"])) {
        $query = "UPDATE server SET server.status = '1' WHERE serverip = '" . $ip . "'";
        mysqli_query($conn, $query);
      } else {
        $query = "UPDATE server SET server.status = '0' WHERE serverip = '" . $ip . "'";
        mysqli_query($conn, $query);
      }
    }
  } else {
    $query2 = "UPDATE server SET server.status = '0' WHERE serverip = '" . $ip . "'";
    mysqli_query($conn, $query2);
  }
}

// IP Reset Logic
if (isset($_POST['license'], $_POST['ip']) && !empty($_POST['license']) && !empty($_POST['ip'])) {
    $id = $_POST['license'];
    $ip = $_POST['ip'];
    include 'database.php';

    if (!isset($_SESSION['id'])) {
        header('Location: login/');
        exit();
    }

    $sql = "SELECT lastreset FROM redem_license
            JOIN server ON server.serverid = redem_license.serverid
            WHERE redem_license.license = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $id);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $dbDate = $row['lastreset'];
        $tage = date('Y-m-d', strtotime('-30 days'));

        if ($dbDate < $tage) {
            $updateSql = "UPDATE redem_license SET lastreset = NOW() WHERE license = ?";
            $updateStmt = mysqli_prepare($link, $updateSql);
            mysqli_stmt_bind_param($updateStmt, "s", $id);

            if (mysqli_stmt_execute($updateStmt)) {
                $sql2 = "DELETE FROM server WHERE serverip = ?";
                $stmt2 = mysqli_prepare($link, $sql2);
                mysqli_stmt_bind_param($stmt2, "s", $ip);

                if (mysqli_stmt_execute($stmt2)) {
                    $site_name = $website_config["site_name"];
                    echo "<script>
                      Swal.fire({
                        title: 'Panel System',
                        text: 'IP has been reset successfully. You can now restart $site_name',
                        icon: 'success'
                      });
                    </script>";
                } else {
                    echo "<script>
                      Swal.fire({
                        title: 'Panel System',
                        text: 'ERROR [691]',
                        icon: 'error'
                      });
                    </script>";
                }
            } else {
                echo "<script>
                  Swal.fire({
                    title: 'Panel System',
                    text: 'Error [692]', 
                    icon: 'error'
                  });
                </script>";
            }

            mysqli_stmt_close($updateStmt);
        } else {
            echo "<script>
              Swal.fire({
                title: 'Panel System',
                text: 'IP reset COOLDOWN',
                icon: 'error'
              });
            </script>";
        }
    } else {
        echo "<script>
          Swal.fire({
            title: 'Panel System',
            text: 'Error [694]',
            icon: 'error'
          });
        </script>";
    }

    mysqli_stmt_close($stmt);
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
    <link rel="icon" href="<?php echo $website_config['site_favicon']; ?>" type="image/x-icon">
    <title><?php echo $website_config['site_name']; ?> | Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.min.css">
    <style>
        :root {
          --primary: #3b82f6;
          --primary-dark: #2563eb;
          --bg-dark: #0f172a;
          --bg-card: #1e293b;
          --bg-sidebar: #111827;
          --text-primary: #f8fafc;
          --text-secondary: #94a3b8;
          --text-muted: #64748b;
          --border: #334155;
          --border-light: #475569;
          --success: #10b981;
          --warning: #f59e0b;
          --danger: #ef4444;
          --info: #06b6d4;
        }

        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }

        body {
          font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
          background: var(--bg-dark);
          color: var(--text-primary);
          line-height: 1.6;
        }

        /* Layout Structure */
        .dashboard-container {
          display: flex;
          min-height: 100vh;
        }

        /* Sidebar */
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
          color: var(--text-muted);
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

        /* Main Content */
        .main-content {
          margin-left: 280px;
          flex: 1;
          display: flex;
          flex-direction: column;
        }

        /* Header */
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

        .dropdown-item i {
          font-size: 16px;
          width: 16px;
        }

        .dropdown-arrow {
          margin-left: 0.5rem;
          transition: transform 0.3s ease;
        }

        .user-menu.active .dropdown-arrow {
          transform: rotate(180deg);
        }

        /* Dashboard Content */
        .dashboard-content {
          padding: 2rem;
          flex: 1;
        }

        .breadcrumb {
          display: flex;
          align-items: center;
          gap: 0.5rem;
          margin-bottom: 2rem;
          font-size: 0.875rem;
        }

        .breadcrumb-item {
          color: var(--text-secondary);
        }

        .breadcrumb-item.active {
          color: var(--text-primary);
          font-weight: 500;
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
          transition: all 0.3s ease;
        }

        .stat-card:hover {
          border-color: var(--primary);
          transform: translateY(-2px);
        }

        .stat-header {
          display: flex;
          justify-content: between;
          align-items: flex-start;
          margin-bottom: 1rem;
        }

        .stat-icon {
          width: 48px;
          height: 48px;
          background: rgba(59, 130, 246, 0.1);
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          margin-bottom: 1rem;
        }

        .stat-icon i {
          font-size: 24px;
          color: var(--primary);
        }

        .stat-value {
          font-size: 2rem;
          font-weight: 700;
          color: var(--text-primary);
          margin-bottom: 0.25rem;
        }

        .stat-label {
          font-size: 0.875rem;
          color: var(--text-secondary);
          font-weight: 500;
        }

        /* Content Grid */
        .content-grid {
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 2rem;
          margin-bottom: 2rem;
        }

        /* Cards */
        .card {
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
          color: var(--text-primary);
        }

        .card-body {
          padding: 1.5rem;
        }

        /* News Items */
        .news-item {
          padding: 1rem 0;
          border-bottom: 1px solid var(--border);
        }

        .news-item:last-child {
          border-bottom: none;
        }

        .news-date {
          font-size: 0.75rem;
          color: var(--info);
          font-weight: 500;
        }

        .news-text {
          margin: 0.5rem 0;
          color: var(--text-secondary);
          line-height: 1.5;
        }

        .news-author {
          font-size: 0.75rem;
          color: var(--text-muted);
        }

        /* Server Table */
        .table-container {
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 12px;
          overflow: hidden;
        }

        .table {
          width: 100%;
          border-collapse: collapse;
        }

        .table th {
          background: var(--bg-sidebar);
          color: var(--text-primary);
          font-weight: 600;
          padding: 1rem;
          text-align: center;
          border-bottom: 1px solid var(--border);
          font-size: 0.875rem;
        }

        .table td {
          padding: 1rem;
          text-align: center;
          border-bottom: 1px solid var(--border);
          color: var(--text-secondary);
        }

        .table tbody tr:hover {
          background: rgba(59, 130, 246, 0.05);
        }

        /* Badges */
        .badge {
          display: inline-flex;
          align-items: center;
          padding: 0.375rem 0.75rem;
          border-radius: 6px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          letter-spacing: 0.5px;
        }

        .badge-success {
          background: rgba(16, 185, 129, 0.1);
          color: var(--success);
          border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .badge-danger {
          background: rgba(239, 68, 68, 0.1);
          color: var(--danger);
          border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .badge-warning {
          background: rgba(245, 158, 11, 0.1);
          color: var(--warning);
          border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .badge-info {
          background: rgba(6, 182, 212, 0.1);
          color: var(--info);
          border: 1px solid rgba(6, 182, 212, 0.2);
        }

        .badge-secondary {
          background: rgba(100, 116, 139, 0.1);
          color: var(--text-muted);
          border: 1px solid rgba(100, 116, 139, 0.2);
        }

        /* Buttons */
        .btn {
          display: inline-flex;
          align-items: center;
          gap: 0.5rem;
          padding: 0.5rem 1rem;
          border: none;
          border-radius: 8px;
          font-size: 0.875rem;
          font-weight: 500;
          text-decoration: none;
          cursor: pointer;
          transition: all 0.2s ease;
        }

        .btn-primary {
          background: var(--primary);
          color: white;
        }

        .btn-primary:hover {
          background: var(--primary-dark);
          transform: translateY(-1px);
        }

        .btn-outline-primary {
          background: transparent;
          color: var(--primary);
          border: 1px solid var(--primary);
        }

        .btn-outline-primary:hover {
          background: var(--primary);
          color: white;
        }

        .btn-outline-warning {
          background: transparent;
          color: var(--warning);
          border: 1px solid var(--warning);
        }

        .btn-outline-warning:hover {
          background: var(--warning);
          color: white;
        }

        /* Footer */
        .footer {
          background: var(--bg-card);
          border-top: 1px solid var(--border);
          padding: 1.5rem 2rem;
          text-align: center;
          margin-top: auto;
        }

        .footer-text {
          color: var(--text-muted);
          font-size: 0.875rem;
        }

        /* Responsive */
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

          .content-grid {
            grid-template-columns: 1fr;
          }

          .stats-grid {
            grid-template-columns: 1fr;
          }

          .dashboard-content {
            padding: 1rem;
          }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
          width: 8px;
        }

        ::-webkit-scrollbar-track {
          background: var(--bg-dark);
        }

        ::-webkit-scrollbar-thumb {
          background: var(--border);
          border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
          background: var(--border-light);
        }

        /* Loading state */
        .loading {
          color: var(--text-muted) !important;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
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
                        <a href="/" class="nav-link active">
                            <i class="uil uil-estate"></i>
                            <span>Home</span>
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

                <?php if ($_SESSION["group"] == "admin"): ?>
                <div class="nav-section">
                    <div class="nav-section-title">Admin</div>
                    <div class="nav-item">
                        <a href="/admin/server/overview" class="nav-link">
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
                </div>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-title">Dashboard</div>
                <div class="user-menu" onclick="toggleUserDropdown()">
                    <img src="<?php echo $avatar; ?>" alt="User Avatar" class="user-avatar">
                    <div class="user-info">
                        <div class="user-name"><?php echo $_SESSION["username"]; ?></div>
                        <div class="user-role"><?php echo $_SESSION["group"]; ?></div>
                    </div>
                    <i class="uil uil-angle-down dropdown-arrow"></i>
                    
                    <div class="user-dropdown" id="userDropdown">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/account/" class="dropdown-item">
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

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <span class="breadcrumb-item"><i class="uil uil-estate"></i></span>
                    <span class="breadcrumb-item active">Home</span>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="uil uil-users-alt"></i>
                        </div>
                        <div class="stat-value loading" id="liveJoins">-</div>
                        <div class="stat-label">Total Joins</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="uil uil-ban"></i>
                        </div>
                        <div class="stat-value loading" id="liveBans">-</div>
                        <div class="stat-label">Total Bans</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="uil uil-camera"></i>
                        </div>
                        <div class="stat-value loading" id="liveScreens">-</div>
                        <div class="stat-label">Total Screenshots</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="uil uil-shield-check"></i>
                        </div>
                        <div class="stat-value loading" id="liveAuth">-</div>
                        <div class="stat-label">Total Auths</div>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="content-grid">
                    <!-- News Card -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Latest News</div>
                        </div>
                        <div class="card-body">
                            <?php
                            $query = "SELECT * FROM news ORDER BY date DESC LIMIT 3";
                            $result = $link->query($query);
                            while ($row = $result->fetch_array()) {
                                echo '<div class="news-item">';
                                echo '<div class="news-date">' . $row["date"] . '</div>';
                                echo '<div class="news-text">' . $row["text"] . '</div>';
                                echo '<div class="news-author">Posted by ' . $row["user"] . '</div>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Notifications Card -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Account Notifications</div>
                        </div>
                        <div class="card-body">
                            <?php
                            $query = "SELECT * FROM notifications WHERE userid = '" . $_SESSION["id"] . "' ORDER BY date DESC LIMIT 3";
                            $result = $link->query($query);
                            while ($row = $result->fetch_array()) {
                                echo '<div class="news-item">';
                                echo '<div class="news-date">' . $row["date"] . '</div>';
                                echo '<div class="news-text">' . $row["text"] . '</div>';
                                echo '<div class="news-author">System Notification</div>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Server Management Table -->
                <div class="table-container">
                    <div class="card-header">
                        <div class="card-title">Manage Servers</div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Server Name</th>
                                <th>License</th>
                                <th>Status</th>
                                <th>Expires</th>
                                <th>Role</th>
                                <th>Manage</th>
                                <th>Reset</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include('database.php');
                            $query = "SELECT server.serverip,server.status,server.name,server.port,users_server.is_owner,redem_license.expires,redem_license.license FROM users_server 
                            JOIN server ON users_server.serverid = server.serverid
                            JOIN redem_license ON redem_license.serverid = server.serverid
                            WHERE users_server.userid = '" . $_SESSION["id"] . "'";
                            $result = $link->query($query);
                            
                            while ($row = $result->fetch_array()) {
                                $statusBadge = $row["status"] == 1 ? "badge-success" : "badge-danger";
                                $statusText = $row["status"] == 1 ? "Online" : "Offline";
                                $roleBadge = $row["is_owner"] == 1 ? "badge-danger" : "badge-secondary";
                                $roleText = $row["is_owner"] == 1 ? "Owner" : "Member";
                                
                                echo '<tr>';
                                echo '<td><span class="badge badge-info">' . $row["serverip"] . '</span></td>';
                                echo '<td>' . $row["name"] . '</td>';
                                echo '<td><code>' . $row["license"] . '</code></td>';
                                echo '<td><span class="badge ' . $statusBadge . '">' . $statusText . '</span></td>';
                                echo '<td>' . $row["expires"] . '</td>';
                                echo '<td><span class="badge ' . $roleBadge . '">' . $roleText . '</span></td>';
                                
                                if ($row["is_owner"] == 1) {
                                    $encrypted = encrypt_string($row['serverip']);
                                    echo '<td>';
                                    echo '<a href="/manage" class="btn btn-outline-primary" onclick="setSessionIP(\'' . $encrypted . '\')">';
                                    echo '<i class="uil uil-setting"></i> Manage';
                                    echo '</a>';
                                    echo '</td>';
                                    echo '<td>';
                                    echo '<form method="POST" style="display: inline;">';
                                    echo '<input type="hidden" name="license" value="' . $row["license"] . '">';
                                    echo '<input type="hidden" name="ip" value="' . $row["serverip"] . '">';
                                    echo '<button type="submit" class="btn btn-outline-warning">';
                                    echo '<i class="uil uil-refresh"></i> Reset IP';
                                    echo '</button>';
                                    echo '</form>';
                                    echo '</td>';
                                } else {
                                    $encrypted = encrypt_string($row['serverip']);
                                    echo '<td>';
                                    echo '<a href="/manage" class="btn btn-outline-primary" onclick="setSessionIP(\'' . $encrypted . '\')">';
                                    echo '<i class="uil uil-eye"></i> View';
                                    echo '</a>';
                                    echo '</td>';
                                    echo '<td>-</td>';
                                }
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="footer-text">
                    © 2022 - <script>document.write(new Date().getFullYear())</script> <?php echo $website_config['site_copyright']; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // User dropdown toggle
        function toggleUserDropdown() {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.getElementById('userDropdown');
            
            userMenu.classList.toggle('active');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.getElementById('userDropdown');
            
            if (!userMenu.contains(e.target)) {
                userMenu.classList.remove('active');
                dropdown.classList.remove('show');
            }
        });

        // Live data updates
        function updateNumRows(idName, tableName) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const element = document.getElementById(idName);
                    element.innerHTML = this.responseText;
                    element.classList.remove('loading');
                }
            };
            xhttp.open("GET", "getNumRows.php?tableName=" + tableName, true);
            xhttp.send();
        }

        // Set session IP for management
        function setSessionIP(ip) {
            var xhr = new XMLHttpRequest();
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log('IP set successfully');
                } else {
                    console.error('Failed to set IP');
                }
            };
            xhr.open('POST', 'setip.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('ip=' + encodeURIComponent(ip));
        }

        // Update stats every 5 seconds
        setInterval(function () {
            updateNumRows("liveScreens", "totalscreenshots");
            updateNumRows("liveAuth", "totalauths");
            updateNumRows("liveBans", "totalbans");
            updateNumRows("liveJoins", "totaljoins");
        }, 5000);

        // Initial load
        document.addEventListener('DOMContentLoaded', function() {
            updateNumRows("liveScreens", "totalscreenshots");
            updateNumRows("liveAuth", "totalauths");
            updateNumRows("liveBans", "totalbans");
            updateNumRows("liveJoins", "totaljoins");
        });

        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('open');
        }

        // Add mobile menu button for responsive design
        if (window.innerWidth <= 768) {
            const header = document.querySelector('.header');
            const menuButton = document.createElement('button');
            menuButton.innerHTML = '<i class="uil uil-bars"></i>';
            menuButton.style.cssText = `
                background: transparent;
                border: 1px solid var(--border);
                color: var(--text-primary);
                padding: 0.5rem;
                border-radius: 8px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
            `;
            menuButton.onclick = toggleSidebar;
            header.insertBefore(menuButton, header.firstChild);
        }

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                const sidebar = document.querySelector('.sidebar');
                const isClickInsideSidebar = sidebar.contains(e.target);
                const isClickOnMenuButton = e.target.closest('button');
                
                if (!isClickInsideSidebar && !isClickOnMenuButton && sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                }
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading states to buttons
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="uil uil-spinner"></i> Processing...';
                    submitButton.style.opacity = '0.7';
                }
            });
        });

        // Enhanced hover effects for cards
        document.querySelectorAll('.stat-card, .card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
                this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.3)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });

        // Add ripple effect to buttons
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const rect = this.getBoundingClientRect();
                const ripple = document.createElement('span');
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.3);
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                `;
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .stat-card, .card {
                animation: fadeIn 0.6s ease-out;
            }
            
            .stat-card:nth-child(1) { animation-delay: 0.1s; }
            .stat-card:nth-child(2) { animation-delay: 0.2s; }
            .stat-card:nth-child(3) { animation-delay: 0.3s; }
            .stat-card:nth-child(4) { animation-delay: 0.4s; }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>