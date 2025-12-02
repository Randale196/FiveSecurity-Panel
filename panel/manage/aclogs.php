<?php
session_start();
include('../func.php');

if(empty($_SESSION['ip'])) {
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

if (isset($_POST['unban']) && !empty($_POST['banid'])) {
  $id = $_POST['banid'];

  include '../database.php';

  $query = "SELECT redem_license.license FROM users_server 
  JOIN server ON users_server.serverid = server.serverid
  JOIN redem_license ON redem_license.serverid = server.serverid
  WHERE users_server.userid = ? AND server.serverip = ?";

  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, "ss", $_SESSION["id"], $_SESSION["ip"]);
  mysqli_stmt_execute($stmt);
  $resultt = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_row($resultt);
  $t = $row[0];

  $sql = "DELETE FROM `$t` WHERE id = ?";
  $stmt = mysqli_prepare($svbans, $sql);
  mysqli_stmt_bind_param($stmt, "i", $id);
  
  if (mysqli_stmt_execute($stmt)) {
    echo '<script>
      Swal.fire({
        icon: "success",
        title: "Success",
        text: "Player was unbanned successfully"
      });
    </script>';
  } else {
    echo '<script>
      Swal.fire({
        icon: "error", 
        title: "Panel System",
        text: "ERROR! Code: [693]"
      });
    </script>';
  }
  mysqli_stmt_close($stmt);
  mysqli_close($svbans);
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
  <title><?php echo $website_config['site_name']; ?> | Anticheat Logs</title>
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

    /* Content Card */
    .content-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 2rem;
    }

    .card-header {
      padding: 1.5rem 2rem;
      border-bottom: 1px solid var(--border);
      background: linear-gradient(90deg, var(--bg-card) 0%, rgba(59, 130, 246, 0.05) 100%);
    }

    .card-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
    }

    .card-subtitle {
      font-size: 0.875rem;
      color: var(--text-secondary);
      margin: 0;
    }

    .card-body {
      padding: 1.5rem 2rem;
    }

    /* Search Box */
    .search-container {
      margin-bottom: 1.5rem;
    }

    .search-input {
      width: 100%;
      max-width: 400px;
      padding: 0.875rem 1rem 0.875rem 3rem;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--border);
      border-radius: 8px;
      color: var(--text-primary);
      font-size: 0.95rem;
      font-family: inherit;
      transition: all 0.2s ease;
      position: relative;
    }

    .search-input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
      background: rgba(255, 255, 255, 0.08);
    }

    .search-input::placeholder {
      color: var(--text-secondary);
    }

    .search-wrapper {
      position: relative;
      display: inline-block;
    }

    .search-icon {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-secondary);
      font-size: 16px;
      pointer-events: none;
    }

    /* Table Styles */
    .table-container {
      background: rgba(255, 255, 255, 0.02);
      border-radius: 8px;
      overflow: hidden;
      border: 1px solid var(--border);
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      margin: 0;
    }

    .table thead th {
      background: var(--bg-secondary);
      color: var(--text-primary);
      font-weight: 600;
      padding: 1rem 1.5rem;
      text-align: left;
      border-bottom: 1px solid var(--border);
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .table tbody tr {
      border-bottom: 1px solid var(--border);
      transition: all 0.2s ease;
    }

    .table tbody tr:hover {
      background: rgba(59, 130, 246, 0.05);
    }

    .table tbody tr:last-child {
      border-bottom: none;
    }

    .table tbody td {
      padding: 1rem 1.5rem;
      color: var(--text-primary);
      font-size: 0.875rem;
      vertical-align: middle;
    }

    .table tbody td:first-child {
      font-weight: 500;
    }

    /* Log Entry Styling */
    .log-reason {
      color: var(--success);
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .log-reason::before {
      content: '';
      width: 8px;
      height: 8px;
      background: var(--success);
      border-radius: 50%;
      flex-shrink: 0;
    }

    .log-date {
      color: var(--text-secondary);
      font-family: 'SF Mono', 'Monaco', 'Inconsolata', monospace;
      font-size: 0.8rem;
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 3rem 2rem;
      color: var(--text-secondary);
    }

    .empty-state i {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: var(--text-muted);
    }

    .empty-state h3 {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: var(--text-primary);
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

    .dmca-badge {
      display: inline-block;
      margin-left: 1rem;
    }

    .dmca-badge img {
      height: 20px;
    }

    /* Loading Animation */
    .loading {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(59, 130, 246, 0.3);
      border-radius: 50%;
      border-top-color: var(--primary);
      animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
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

      .header {
        padding: 1rem;
      }

      .content {
        padding: 1rem;
      }

      .card-header,
      .card-body {
        padding: 1rem;
      }

      .table thead th,
      .table tbody td {
        padding: 0.75rem;
      }

      .search-input {
        max-width: 100%;
      }
    }

    /* Table responsive on mobile */
    @media (max-width: 640px) {
      .table-container {
        overflow-x: auto;
      }
      
      .table {
        min-width: 500px;
      }
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
          <a href="https://<?php echo $website_config['site_domain']; ?>/manage/" class="nav-link">
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
          <a href="https://<?php echo $website_config['site_domain']; ?>/manage/aclogs.php" class="nav-link active">
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
          <span>/</span>
          <span>Anticheat Logs</span>
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
        <div class="content-card">
          <div class="card-header">
            <h1 class="card-title">Anticheat Logs</h1>
            <p class="card-subtitle">The last 1000 Anticheat Logs of your Server</p>
          </div>
          
          <div class="card-body">
            <div class="search-container">
              <div class="search-wrapper">
                <input type="text" id="searchInput" class="search-input" placeholder="Search logs...">
                <i class="uil uil-search search-icon"></i>
              </div>
            </div>

            <div class="table-container">
              <table class="table" id="logsTable">
                <thead>
                  <tr>
                    <th>Reason</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  include("../database.php");

                  $getlicense = "SELECT redem_license.license FROM users_server 
                  JOIN server ON users_server.serverid = server.serverid
                  JOIN redem_license ON redem_license.serverid = server.serverid
                  WHERE users_server.userid = ? AND server.serverip = ?";

                  if ($stmt = $link->prepare($getlicense)) {
                      $stmt->bind_param('ss', $_SESSION["id"], $_SESSION["ip"]);
                      
                      $stmt->execute();
                      
                      $result = $stmt->get_result();
                      $row = $result->fetch_assoc();
                      $license = $row['license'];
                      
                      $tableName = "logs";
                      $columns = ['id', 'reason', 'date', 'ip'];
                      $fetchData = fetch_data($logs, $tableName, $columns, $license);
                      
                      $stmt->close();
                  } else {
                      echo "Error: " . $link->error;
                  }

                  function fetch_data($logs, $tableName, $columns, $license) {
                      if (empty($logs)) {
                          return "Logs connection is missing.";
                      } elseif (empty($columns) || !is_array($columns)) {
                          return "Invalid columns specified.";
                      } elseif (empty($tableName)) {
                          return "Table name is missing.";
                      } else {
                          $columnName = implode(", ", $columns);
                          $query = "SELECT * FROM `$tableName` WHERE license = ? ORDER BY date DESC LIMIT 1000";
                          
                          if ($stmt = $logs->prepare($query)) {
                              $stmt->bind_param('s', $license);
                              $stmt->execute();
                              
                              $result = $stmt->get_result();
                              
                              if ($result->num_rows > 0) {
                                  $msg = $result->fetch_all(MYSQLI_ASSOC);
                              } else {
                                  $msg = "No current Anticheat Logs.";
                              }
                              $stmt->close();
                          } else {
                              $msg = "Anticheat Logs not found, contact our team.";
                              $err = $logs->error;
                          }
                      }
                      return $msg;
                  }

                  if(is_array($fetchData)){      
                    $sn=1;
                    foreach($fetchData as $data){
                  ?>
                    <tr>
                      <td>
                        <div class="log-reason"><?php echo htmlspecialchars($data['reason']??''); ?></div>
                      </td>
                      <td>
                        <div class="log-date"><?php echo htmlspecialchars($data['date']??''); ?></div>
                      </td>
                    </tr>
                  <?php
                    $sn++;}}else{ ?>
                    <tr>
                      <td colspan="2">
                        <div class="empty-state">
                          <i class="uil uil-document-info"></i>
                          <h3>No Logs Found</h3>
                          <p><?php echo $fetchData; ?></p>
                        </div>
                      </td>
                    </tr>
                  <?php
                  }?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <footer class="footer">
        <div>
          2022 - <script>document.write(new Date().getFullYear())</script> <?php echo $website_config['site_copyright']; ?>
          <a href="https://www.dmca.com/r/wrejk1x" title="DMCA.com Protection Status" class="dmca-badge">
            <img src="https://images.dmca.com/Badges/dmca-badge-w100-5x1-11.png?ID=9c9de7b3-a4ce-4ec0-9d39-8072e9ad971a" alt="DMCA.com Protection Status" />
          </a>
          <script src="https://images.dmca.com/Badges/DMCABadgeHelper.min.js"></script>
        </div>
      </footer>
    </main>
  </div>

  <script>
    // Enhanced search functionality
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('logsTable');
    const tbody = table.getElementsByTagName('tbody')[0];
    
    searchInput.addEventListener('keyup', function() {
      const searchTerm = this.value.toLowerCase().trim();
      const rows = tbody.getElementsByTagName('tr');
      
      for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        
        // Search through all cells in the row
        for (let j = 0; j < cells.length; j++) {
          const cellText = cells[j].textContent || cells[j].innerText;
          if (cellText.toLowerCase().indexOf(searchTerm) > -1) {
            found = true;
            break;
          }
        }
        
        // Show/hide row based on search result
        if (found || searchTerm === '') {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      }
    });

    // Add loading animation on page load
    document.addEventListener('DOMContentLoaded', function() {
      // Simulate loading for visual effect
      const tableRows = document.querySelectorAll('.table tbody tr');
      tableRows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
          row.style.opacity = '1';
          row.style.transform = 'translateY(0)';
        }, index * 50);
      });
    });

    // Mobile sidebar toggle
    function toggleSidebar() {
      const sidebar = document.querySelector('.sidebar');
      sidebar.classList.toggle('open');
    }

    // Prevent right-click and text selection
    document.addEventListener('contextmenu', function(e) {
      e.preventDefault();
    });

    document.addEventListener('selectstart', function(e) {
      e.preventDefault();
    });

    // Auto-refresh logs every 30 seconds
    setInterval(function() {
      // Add a subtle indicator that logs might be refreshing
      const searchInput = document.getElementById('searchInput');
      if (searchInput && searchInput.value === '') {
        // Only refresh if no active search
        // In a real implementation, you'd use AJAX to refresh the table data
        console.log('Auto-refresh logs...');
      }
    }, 30000);
  </script>
</body>
</html>