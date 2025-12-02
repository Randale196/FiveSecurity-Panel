<?php
session_start();
include('../func.php');

if (empty($_SESSION['ip'])) {
  session_destroy();
  header("Location: https://" . $website_config['site_domain'] . "/login");
}

$stmt = $conn->prepare("SELECT server.serverip,users.userid FROM server JOIN users_server ON users_server.serverid = server.serverid JOIN users ON users.userid = users_server.userid");
$stmt->execute();
$result = $stmt->get_result();

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

  $query = "SELECT redem_license.license 
              FROM users_server 
              JOIN server ON users_server.serverid = server.serverid
              JOIN redem_license ON redem_license.serverid = server.serverid
              WHERE users_server.userid = ? AND server.serverip = ?";

  $stmt = $conn->prepare($query);
  $stmt->bind_param("ss", $_SESSION["id"], $_SESSION["ip"]);
  $stmt->execute();
  $resultt = $stmt->get_result();
  $row = $resultt->fetch_row();

  if ($row) {
    $t = $row[0];

    $sql = "DELETE FROM `$t` WHERE id = ?";
    $deleteStmt = $svbans->prepare($sql);
    $deleteStmt->bind_param("i", $id);

    if ($deleteStmt->execute()) {
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

    $deleteStmt->close();
  } else {
    echo '<script>
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "License not found"
      });
    </script>';
  }

  $stmt->close();
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
      overflow-x: auto;
      border: 1px solid var(--border);
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      margin: 0;
      min-width: 1200px;
    }

    .table thead th {
      background: var(--bg-secondary);
      color: var(--text-primary);
      font-weight: 600;
      padding: 1rem 0.75rem;
      text-align: left;
      border-bottom: 1px solid var(--border);
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      white-space: nowrap;
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
      padding: 1rem 0.75rem;
      color: var(--text-primary);
      font-size: 0.875rem;
      vertical-align: middle;
      max-width: 150px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .table tbody td:first-child {
      font-weight: 600;
      color: var(--primary);
    }

    /* Action Buttons */
    .action-btn {
      background: var(--primary);
      border: none;
      border-radius: 6px;
      padding: 0.5rem 1rem;
      font-family: inherit;
      font-size: 0.75rem;
      font-weight: 600;
      color: white;
      cursor: pointer;
      transition: all 0.2s ease;
      margin: 0.125rem;
      white-space: nowrap;
    }

    .action-btn:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
    }

    .action-btn.danger {
      background: var(--danger);
    }

    .action-btn.danger:hover {
      background: #dc2626;
    }

    .action-btn.screenshot {
      background: var(--info);
    }

    .action-btn.screenshot:hover {
      background: #0891b2;
    }

    /* ID Badge */
    .ban-id {
      background: rgba(59, 130, 246, 0.1);
      color: var(--primary);
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      font-weight: 600;
      font-size: 0.75rem;
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

    /* Popup Styles */
    .popup-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.8);
      z-index: 9999;
      display: none;
      animation: popup-fadein 0.3s ease-in-out forwards;
    }

    .popup-window {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: var(--bg-card);
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 20px 25px rgba(0, 0, 0, 0.3);
      text-align: center;
      border: 1px solid var(--border);
      max-width: 90vw;
      max-height: 90vh;
    }

    .popup-window img {
      max-width: 100%;
      max-height: 70vh;
      border-radius: 8px;
    }

    @keyframes popup-fadein {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    .close {
      position: absolute;
      top: 1rem;
      right: 1rem;
      cursor: pointer;
      color: var(--danger);
      font-size: 2rem;
      font-weight: bold;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background: rgba(239, 68, 68, 0.1);
      transition: all 0.2s ease;
    }

    .close:hover {
      background: rgba(239, 68, 68, 0.2);
      transform: scale(1.1);
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
        padding: 0.5rem 0.25rem;
        font-size: 0.75rem;
      }

      .search-input {
        max-width: 100%;
      }

      .action-btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.7rem;
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
          <a href="https://<?php echo $website_config['site_domain']; ?>/manage/banlist.php" class="nav-link active">
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
          <span>/</span>
          <span>Banlist</span>
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
            <h1 class="card-title">Server Banlist</h1>
            <p class="card-subtitle">Manage banned players on your server</p>
          </div>
          
          <div class="card-body">
            <div class="search-container">
              <div class="search-wrapper">
                <input type="text" id="searchInput" class="search-input" placeholder="Search by ID or Name...">
                <i class="uil uil-search search-icon"></i>
              </div>
            </div>

            <div class="table-container">
              <table class="table" id="banTable">
                <caption style="color: var(--text-secondary); margin-bottom: 1rem;">List of users</caption>
                <thead>
                  <tr>
                    <th>Ban ID</th>
                    <th>Name</th>
                    <th>Steam</th>
                    <th>License</th>
                    <th>XBL</th>
                    <th>Live ID</th>
                    <th>Discord ID</th>
                    <th>IP</th>
                    <th>Reason</th>
                    <th>Screenshot</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  include("../database.php");

                  $pageLimit = 200;
                  $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                  $offset = ($page - 1) * $pageLimit;

                  $query = "SELECT redem_license.license FROM users_server 
                  JOIN server ON users_server.serverid = server.serverid
                  JOIN redem_license ON redem_license.serverid = server.serverid
                  WHERE users_server.userid = ? AND server.serverip = ?";

                  if ($stmt = $conn->prepare($query)) {
                    $stmt->bind_param('ss', $_SESSION["id"], $_SESSION["ip"]);
                    $stmt->execute();

                    $result = $stmt->get_result();
                    $row = $result->fetch_row();
                    $tableName = $row[0];
                    $stmt->close();
                  } else {
                    echo "Error: " . $conn->error;
                  }

                  if (isset($_POST['search'])) {
                    $search = "%" . $_POST['search'] . "%";
                    $query = "SELECT * FROM `$tableName` WHERE name LIKE ? OR steam LIKE ? OR license LIKE ? OR xbl LIKE ? OR live LIKE ? OR discord LIKE ? OR playerip LIKE ? OR reason LIKE ? OR id LIKE ? ORDER BY id DESC LIMIT ?, ?";

                    if ($stmt = $svbans->prepare($query)) {
                      $stmt->bind_param('ssssssssssi', $search, $search, $search, $search, $search, $search, $search, $search, $search, $offset, $pageLimit);
                    }
                  } else {
                    $query = "SELECT * FROM `$tableName` ORDER BY id DESC LIMIT ?, ?";

                    if ($stmt = $svbans->prepare($query)) {
                      $stmt->bind_param('ii', $offset, $pageLimit);
                    }
                  }

                  $fetchData = fetch_data($svbans, $stmt, ['id', 'name', 'steam', 'license', 'xbl', 'live', 'discord', 'playerip', 'hwid', 'reason']);

                  function fetch_data($svbans, $stmt, $columns)
                  {
                    if (empty($svbans)) {
                      return "No database connection.";
                    } elseif (empty($columns) || !is_array($columns)) {
                      return "Invalid columns specified.";
                    } elseif (empty($stmt)) {
                      return "Invalid query statement.";
                    } else {
                      $stmt->execute();
                      $result = $stmt->get_result();

                      if ($result->num_rows > 0) {
                        $row = $result->fetch_all(MYSQLI_ASSOC);
                        $msg = $row;
                      } else {
                        $msg = "No current bans.";
                      }
                      $stmt->close();
                    }
                    return $msg;
                  }

                  if (is_array($fetchData)) {
                    $sn = 1;
                    foreach ($fetchData as $data) {
                      ?>
                      <tr>
                        <td>
                          <span class="ban-id">#<?php echo htmlspecialchars($data['id'] ?? ''); ?></span>
                        </td>
                        <td title="<?php echo htmlspecialchars($data['name'] ?? ''); ?>">
                          <?php echo htmlspecialchars($data['name'] ?? ''); ?>
                        </td>
                        <td title="<?php echo htmlspecialchars($data['steam'] ?? ''); ?>">
                          <?php echo htmlspecialchars($data['steam'] ?? ''); ?>
                        </td>
                        <td title="<?php echo htmlspecialchars($data['license'] ?? ''); ?>">
                          <?php echo htmlspecialchars($data['license'] ?? ''); ?>
                        </td>
                        <td title="<?php echo htmlspecialchars($data['xbl'] ?? ''); ?>">
                          <?php echo htmlspecialchars($data['xbl'] ?? ''); ?>
                        </td>
                        <td title="<?php echo htmlspecialchars($data['live'] ?? ''); ?>">
                          <?php echo htmlspecialchars($data['live'] ?? ''); ?>
                        </td>
                        <td title="<?php echo htmlspecialchars($data['discord'] ?? ''); ?>">
                          <?php echo htmlspecialchars($data['discord'] ?? ''); ?>
                        </td>
                        <td title="<?php echo htmlspecialchars($data['playerip'] ?? ''); ?>">
                          <?php echo htmlspecialchars($data['playerip'] ?? ''); ?>
                        </td>
                        <td title="<?php echo htmlspecialchars($data['reason'] ?? ''); ?>">
                          <?php echo htmlspecialchars($data['reason'] ?? ''); ?>
                        </td>
                        <td>
                          <button class="action-btn screenshot" onclick='openScreenshotPopup("<?php echo htmlspecialchars($data["screen"] ?? ''); ?>")'>
                            See Screen
                          </button>
                        </td>
                        <td>
                          <form name='unban' action='banlist.php' method='post' style="display: inline;">
                            <input type='hidden' name='banid' value='<?php echo htmlspecialchars($data['id'] ?? ''); ?>'>
                            <button class="action-btn danger" type='submit' name='unban' onclick="return confirm('Are you sure you want to unban this player?')">
                              Unban
                            </button>
                          </form>
                        </td>
                      </tr>
                      <?php
                      $sn++;
                    }
                  } else { ?>
                    <tr>
                      <td colspan="11">
                        <div class="empty-state">
                          <i class="uil uil-ban"></i>
                          <h3>No Bans Found</h3>
                          <p><?php echo $fetchData; ?></p>
                        </div>
                      </td>
                    </tr>
                    <?php
                  } ?>
                </tbody>
              </table>
              
              <?php if (!empty($pagination)) { ?>
                <div style="text-align: center; padding: 1rem;">
                  <ul style="list-style: none; display: inline-flex; gap: 0.5rem; margin: 0; padding: 0;">
                    <?php echo $pagination; ?>
                  </ul>
                </div>
              <?php } ?>
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
    // Enhanced search functionality - EXACT ORIGINAL FUNCTIONALITY
    const banTable = document.querySelector('.table');
    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('keyup', function () {
      const searchString = searchInput.value.toLowerCase();
      const rows = banTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      for (let row of rows) {
        const id = row.getElementsByTagName('td')[0].innerText.toLowerCase();
        const name = row.getElementsByTagName('td')[1].innerText.toLowerCase();
        if (id.includes(searchString) || name.includes(searchString)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      }
    });

    // Screenshot popup functionality - EXACT ORIGINAL FUNCTIONALITY
    function openScreenshotPopup(url) {
      if (!url || url.trim() === '') {
        alert('No screenshot available for this ban.');
        return;
      }

      var overlay = document.createElement('div');
      overlay.className = 'popup-overlay';
      
      var window = document.createElement('div');
      window.className = 'popup-window';
      
      var image = document.createElement('img');
      image.src = url;
      image.style.maxWidth = '90%';
      
      var close = document.createElement('span');
      close.className = 'close';
      close.innerHTML = '&times;';
      
      window.appendChild(image);
      window.appendChild(close);
      overlay.appendChild(window);
      document.body.appendChild(overlay);
      overlay.style.display = 'block';
      
      close.addEventListener('click', function () {
        overlay.style.display = 'none';
        document.body.removeChild(overlay);
      });

      // Close on overlay click
      overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
          overlay.style.display = 'none';
          document.body.removeChild(overlay);
        }
      });
    }

    // Add loading animation on page load
    document.addEventListener('DOMContentLoaded', function() {
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

    // Auto-refresh functionality (optional)
    let autoRefreshInterval;
    function startAutoRefresh() {
      autoRefreshInterval = setInterval(() => {
        // Only refresh if no active search
        if (searchInput.value === '') {
          console.log('Auto-refreshing banlist...');
          // In a real implementation, you'd use AJAX to refresh the table data
        }
      }, 60000); // Refresh every minute
    }

    function stopAutoRefresh() {
      if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
      }
    }

    // Start auto-refresh on page load
    startAutoRefresh();

    // Stop auto-refresh when page is hidden
    document.addEventListener('visibilitychange', function() {
      if (document.hidden) {
        stopAutoRefresh();
      } else {
        startAutoRefresh();
      }
    });

    // Table row hover effects
    document.addEventListener('DOMContentLoaded', function() {
      const tableRows = document.querySelectorAll('.table tbody tr');
      tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
          this.style.backgroundColor = 'rgba(59, 130, 246, 0.08)';
        });
        
        row.addEventListener('mouseleave', function() {
          this.style.backgroundColor = '';
        });
      });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
      // Ctrl/Cmd + F to focus search
      if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        searchInput.focus();
      }
      
      // Escape to clear search
      if (e.key === 'Escape') {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('keyup'));
      }
    });

    // ORIGINAL WRAPPER CLASS FUNCTION
    function setWrapperClass() {
      // This maintains compatibility with original theme system
      if (typeof $ !== 'undefined') {
        $(".page-wrapper").attr("class", "page-wrapper horizontal-wrapper");
      }
    }

    // Call the original function
    setWrapperClass();

    // ORIGINAL ALERT TIMEOUT (maintaining compatibility)
    setTimeout(function () {
      var customAlert = document.getElementById('custom-alert');
      if (customAlert) {
        customAlert.style.display = 'none';
      }
    }, 3000);
  </script>
</body>
</html>