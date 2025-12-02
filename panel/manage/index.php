<?php
session_start();
include('../func.php');
$site_name = $website_config["site_name"];

if (empty($_SESSION['ip'])) {
  session_destroy();
  header("Location: https://" . $website_config['site_domain'] . "/login");
}

$result = $conn->query("SELECT server.serverip,users.userid FROM server JOIN users_server ON users_server.serverid = server.serverid JOIN users ON users.userid = users_server.userid");
$match_found = false;

while ($row = $result->fetch_assoc()) {
  if ($row['serverip'] == $_SESSION['ip'] && $row['userid'] == $_SESSION['id']) {
    $match_found = true;
    break;
  }
}

if ($match_found) {
} else {
  header("Location: https://" . $website_config['site_domain'] . "/login");
}

$query = "SELECT redem_license.license FROM users_server 
  JOIN server ON users_server.serverid = server.serverid
  JOIN redem_license ON redem_license.serverid = server.serverid
  WHERE users_server.userid = '" . $_SESSION["id"] . "' AND server.serverip = '" . $_SESSION["ip"] . "'";
$result = $link->query($query);
$row = $result->fetch_assoc();
$license = $row['license'];

if (isset($_POST['restart'])) {
  $id2 = "restart";
  $id = "5000";
  $ipi = $_SESSION['ip'];
  $sql = "INSERT INTO playerlist (id, reason, ip) VALUES (?, ?, ?)";
  $stmt = mysqli_prepare($link, $sql);
  mysqli_stmt_bind_param($stmt, "sss", $id, $id2, $ipi);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
  echo '<script type="text/javascript">';
  echo 'setTimeout(function () { Swal.fire({icon: "success", title: "Panel System", text: "Server restarted successfully"});';
  echo '}, 500);</script>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?php echo $website_config['site_description']; ?>">
  <meta name="keywords" content="<?php echo $website_config['site_keywords']; ?>">
  <meta name="author" content="<?php echo $website_config['site_author']; ?>">
  <link rel="icon" href="<?php echo $website_config['site_favicon']; ?>" type="image/x-icon">
  <link rel="shortcut icon" href="<?php echo $website_config['site_favicon']; ?>" type="image/x-icon">
  <title><?php echo $website_config['site_name']; ?> | Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.24/sweetalert2.all.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    
    :root {
      --primary: #3b82f6;
      --primary-dark: #2563eb;
      --primary-light: #60a5fa;
      --bg-dark: #0f172a;
      --bg-card: #1e293b;
      --bg-sidebar: #1e293b;
      --bg-secondary: #334155;
      --text-primary: #f8fafc;
      --text-secondary: #94a3b8;
      --text-muted: #64748b;
      --border: #334155;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --info: #06b6d4;
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg-dark);
      color: var(--text-primary);
      line-height: 1.6;
      overflow-x: hidden;
    }

    .dashboard-container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar Styles */
    .sidebar {
      width: 280px;
      background: var(--bg-sidebar);
      border-right: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      position: fixed;
      height: 100vh;
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
      flex: 1;
      padding: 1rem 0;
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
      flex: 1;
      margin-left: 280px;
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

    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: var(--text-secondary);
      font-size: 0.875rem;
    }

    .breadcrumb a {
      color: var(--primary);
      text-decoration: none;
    }

    .user-profile {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      position: relative;
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
      font-weight: 600;
      font-size: 0.875rem;
    }

    .user-role {
      font-size: 0.75rem;
      color: var(--text-secondary);
    }

    /* Content Area */
    .content {
      flex: 1;
      padding: 2rem;
      background: var(--bg-dark);
      background-image: 
        linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
      background-size: 15px 15px;
    }

    /* Stats Grid */
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
      position: relative;
      overflow: hidden;
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

    .stat-header {
      display: flex;
      justify-content: space-between;
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
      color: var(--primary);
      font-size: 20px;
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
      gap: 1.5rem;
    }

    .content-card {
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
      color: var(--text-primary);
    }

    .card-body {
      padding: 1.5rem;
    }

    /* Activity Timeline */
    .activity-item {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      align-items: flex-start;
    }

    .activity-item:last-child {
      margin-bottom: 0;
    }

    .activity-dot {
      width: 12px;
      height: 12px;
      background: var(--primary);
      border-radius: 50%;
      margin-top: 0.25rem;
      flex-shrink: 0;
    }

    .activity-content {
      flex: 1;
    }

    .activity-text {
      color: var(--success);
      font-weight: 500;
      margin-bottom: 0.25rem;
    }

    .activity-date {
      font-size: 0.75rem;
      color: var(--text-muted);
    }

    /* Control Panel */
    .control-section {
      text-align: center;
    }

    .control-button {
      background: var(--primary);
      border: none;
      border-radius: 8px;
      padding: 0.875rem 2rem;
      font-family: inherit;
      font-size: 0.875rem;
      font-weight: 600;
      color: white;
      cursor: pointer;
      transition: all 0.2s ease;
      margin-bottom: 2rem;
    }

    .control-button:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
    }

    .info-list {
      list-style: none;
      padding: 0;
      text-align: left;
    }

    .info-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.75rem 0;
      border-bottom: 1px solid var(--border);
      font-size: 0.875rem;
    }

    .info-item:last-child {
      border-bottom: none;
    }

    .info-label {
      color: var(--text-secondary);
    }

    .info-value {
      color: var(--text-primary);
      font-weight: 500;
    }

    .info-value.success {
      color: var(--success);
    }

    .info-value.danger {
      color: var(--danger);
    }

    /* Footer */
    .footer {
      background: var(--bg-card);
      border-top: 1px solid var(--border);
      padding: 1.5rem 2rem;
      text-align: center;
      font-size: 0.875rem;
      color: var(--text-secondary);
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

      .header {
        padding: 1rem;
      }

      .content {
        padding: 1rem;
      }
    }

    /* Loading Animation */
    @keyframes pulse {
      0% { opacity: 1; }
      50% { opacity: 0.5; }
      100% { opacity: 1; }
    }

    .loading {
      animation: pulse 2s infinite;
    }
  </style>
</head>

<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo">
          <i class="uil uil-shield-check"></i>
        </div>
        <div class="sidebar-brand"><?php echo $website_config['site_name']; ?></div>
      </div>
      
      <nav class="sidebar-nav">
        <div class="nav-item">
          <a href="https://<?php echo $website_config['site_domain']; ?>" class="nav-link">
            <i class="uil uil-estate"></i>
            <span>Home</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="https://<?php echo $website_config['site_domain']; ?>/manage/" class="nav-link active">
            <i class="uil uil-desktop"></i>
            <span>Overview</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="https://<?php echo $website_config['site_domain']; ?>/manage/banlist.php" class="nav-link">
            <i class="uil uil-ban"></i>
            <span>Banlist</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="https://<?php echo $website_config['site_domain']; ?>/manage/playerlist.php" class="nav-link">
            <i class="uil uil-users-alt"></i>
            <span>Playerlist</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="https://config.<?php echo str_replace('panel.', '', $website_config['site_domain']); ?>/" class="nav-link">
            <i class="uil uil-edit"></i>
            <span>Config</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="https://<?php echo $website_config['site_domain']; ?>/manage/aclogs.php" class="nav-link">
            <i class="uil uil-list-ul"></i>
            <span>Anticheat Logs</span>
          </a>
        </div>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Header -->
      <header class="header">
        <div class="breadcrumb">
          <a href="https://<?php echo $website_config['site_domain']; ?>">
            <i class="uil uil-estate"></i>
          </a>
          <span>/</span>
          <span>Manage</span>
        </div>
        
        <div class="user-profile">
          <img src="<?php echo $avatar ?>" alt="User Avatar" class="user-avatar">
          <div class="user-info">
            <div class="user-name"><?php echo $_SESSION["username"]; ?></div>
            <div class="user-role"><?php echo $_SESSION["group"]; ?></div>
          </div>
          <i class="uil uil-angle-down"></i>
        </div>
      </header>

      <!-- Content -->
      <div class="content">
        <!-- Stats Grid -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-header">
              <div>
                <div class="stat-value">
                  <?php
                  $result = mysqli_query($stats, "select count(1) FROM `totaljoins` WHERE license = '" . $license . "' ");
                  $row = mysqli_fetch_array($result);
                  echo $row[0];
                  ?>
                </div>
                <div class="stat-label">Total Joins</div>
              </div>
              <div class="stat-icon">
                <i class="uil uil-signin"></i>
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <div>
                <div class="stat-value">
                  <?php
                  $result = mysqli_query($stats, "select count(1) FROM `totalbans` WHERE license = '" . $license . "' ");
                  $row = mysqli_fetch_array($result);
                  echo $row[0];
                  ?>
                </div>
                <div class="stat-label">Total Bans</div>
              </div>
              <div class="stat-icon">
                <i class="uil uil-ban"></i>
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <div>
                <div class="stat-value">
                  <?php
                  $result = mysqli_query($stats, "select count(1) FROM `totalauths` WHERE license = '" . $license . "' ");
                  $row = mysqli_fetch_array($result);
                  echo $row[0];
                  ?>
                </div>
                <div class="stat-label">Total Auth</div>
              </div>
              <div class="stat-icon">
                <i class="uil uil-shield-check"></i>
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <div>
                <div class="stat-value">
                  <?php
                  $query = "SELECT count(1) FROM `botlogs` WHERE license = ?";
                  $stmt = $logs->prepare($query);
                  $stmt->bind_param('s', $license);
                  $stmt->execute();
                  $stmt->bind_result($count);
                  $stmt->fetch();
                  echo $count;
                  ?>
                </div>
                <div class="stat-label">Bot Commands Used</div>
              </div>
              <div class="stat-icon">
                <i class="uil uil-robot"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
          <!-- Server History -->
          <div class="content-card">
            <div class="card-header">
              <h3 class="card-title">Latest Server History</h3>
            </div>
            <div class="card-body">
              <div class="activity-timeline">
                <?php
                include('../database.php');

                $query = "SELECT * FROM logs WHERE license = ? ORDER BY date DESC LIMIT 6";
                $stmt = $logs->prepare($query);
                $stmt->bind_param('s', $license);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_array()) {
                  echo '<div class="activity-item">';
                  echo '<div class="activity-dot"></div>';
                  echo '<div class="activity-content">';
                  echo '<div class="activity-text">' . htmlspecialchars($row["reason"]) . '</div>';
                  echo '<div class="activity-date">' . htmlspecialchars($row["date"]) . '</div>';
                  echo '</div>';
                  echo '</div>';
                }

                $stmt->close();
                ?>
              </div>
            </div>
          </div>

          <!-- Control Panel -->
          <div class="content-card">
            <div class="card-header">
              <h3 class="card-title"><?php echo $website_config['site_name']; ?> Control</h3>
            </div>
            <div class="card-body">
              <div class="control-section">
                <form method="POST" action="index.php">
                  <button type="submit" name="restart" class="control-button">
                    <i class="uil uil-redo"></i>
                    Restart Server
                  </button>
                </form>

                <h4 style="margin-bottom: 1rem; color: var(--text-primary);">Server Information</h4>
                
                <ul class="info-list">
                  <li class="info-item">
                    <span class="info-label">License Key:</span>
                    <span class="info-value success"><?php echo $license; ?></span>
                  </li>
                  <li class="info-item">
                    <span class="info-label">IP Reset Available:</span>
                    <span class="info-value">
                      <?php
                      $sql = "SELECT lastreset FROM redem_license
                      JOIN server ON server.serverid = redem_license.serverid
                      WHERE server.serverip = ?";
                      $stmt = mysqli_prepare($link, $sql);
                      if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "s", $_SESSION["ip"]);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        if ($result) {
                          $row = mysqli_fetch_assoc($result);

                          if ($row) {
                            $dbDate = strtotime($row['lastreset']);

                            if ($dbDate !== false) {
                              $newDate = strtotime('+30 days', $dbDate);

                              if ($newDate !== false) {
                                echo date("Y-m-d H:i:s", $newDate);
                              }
                            }
                          }
                        }

                        mysqli_stmt_close($stmt);
                      }
                      ?>
                    </span>
                  </li>
                  <li class="info-item">
                    <span class="info-label">Current Server IP:</span>
                    <span class="info-value success"><?php echo $_SESSION["ip"]; ?></span>
                  </li>
                  <li class="info-item">
                    <span class="info-label">License Status:</span>
                    <span class="info-value <?php 
                      $blacklistCheck = $conn->prepare('SELECT server.is_blacklisted FROM server 
                        JOIN redem_license ON redem_license.serverid = server.serverid 
                        WHERE redem_license.license = ?');
                      $blacklistCheck->bind_param('s', $license);
                      $blacklistCheck->execute();
                      $blacklistResult = $blacklistCheck->get_result();
                      $row = $blacklistResult->fetch_assoc();
                      $isBlacklisted = $row && $row['is_blacklisted'] == 1;
                      $blacklistCheck->close();
                      echo $isBlacklisted ? 'danger' : 'success';
                    ?>">
                      <?php echo $isBlacklisted ? 'Blacklisted' : 'Active'; ?>
                    </span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <footer class="footer">
        <div>
          2022 - <script>document.write(new Date().getFullYear())</script> <?php echo $website_config['site_copyright']; ?>
      </footer>
    </main>
  </div>

  <script>
    // Mobile sidebar toggle
    function toggleSidebar() {
      const sidebar = document.querySelector('.sidebar');
      sidebar.classList.toggle('open');
    }

    // Smooth animations for stats
    document.addEventListener('DOMContentLoaded', function() {
      const statValues = document.querySelectorAll('.stat-value');
      
      statValues.forEach(stat => {
        const finalValue = parseInt(stat.textContent);
        let currentValue = 0;
        const increment = finalValue / 50;
        
        const timer = setInterval(() => {
          currentValue += increment;
          if (currentValue >= finalValue) {
            stat.textContent = finalValue;
            clearInterval(timer);
          } else {
            stat.textContent = Math.floor(currentValue);
          }
        }, 20);
      });
    });

    // Prevent right-click and text selection
    document.addEventListener('contextmenu', function(e) {
      e.preventDefault();
    });

    document.addEventListener('selectstart', function(e) {
      e.preventDefault();
    });
  </script>
</body>
</html>