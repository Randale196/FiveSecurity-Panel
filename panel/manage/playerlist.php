<?php
session_start();
include('../func.php');

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

if (isset($_POST['kickBtn'])) {
  $ip = $_SESSION['ip'];
  $id = $_POST['itemid'];
  $id2 = "kicked";
  $sql = "INSERT INTO playerlist (id,reason,ip) VALUES ('$id','$id2','$ip')";

  if (mysqli_query($link, $sql)) {
    echo '<script>
      Swal.fire({
        icon: "success",
        title: "Kick System",
        text: "Player kicked successfully"
      });
    </script>';
  } else {
    echo '<script>
      Swal.fire({
        icon: "error",
        title: "Kick System",
        text: "ERROR! Code: [66]"
      });
    </script>';
  }
  mysqli_close($link);
}

if (isset($_POST['addBtn'])) {
  $ip = $_SESSION['ip'];
  $id = $_POST['itemid'];
  $id2 = "banned";
  $sql = "INSERT INTO playerlist (id,reason,ip) VALUES ('$id','$id2','$ip')";

  if (mysqli_query($link, $sql)) {
    echo '<script>
      Swal.fire({
        icon: "success",
        title: "Panel System",
        text: "Player banned successfully"
      });
    </script>';
  } else {
    echo '<script>
      Swal.fire({
        icon: "error",
        title: "Panel System",
        text: "ERROR! Code: [67]"
      });
    </script>';
  }
  mysqli_close($link);
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
  <title><?php echo $website_config['site_name']; ?> | Playerlist</title>
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

    /* Server Status */
    .server-status {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
      padding: 1rem;
      background: rgba(16, 185, 129, 0.1);
      border: 1px solid rgba(16, 185, 129, 0.2);
      border-radius: 8px;
    }

    .status-dot {
      width: 12px;
      height: 12px;
      background: var(--success);
      border-radius: 50%;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0% { opacity: 1; }
      50% { opacity: 0.5; }
      100% { opacity: 1; }
    }

    .status-text {
      color: var(--success);
      font-weight: 600;
    }

    .server-offline {
      background: rgba(239, 68, 68, 0.1);
      border-color: rgba(239, 68, 68, 0.2);
    }

    .server-offline .status-dot {
      background: var(--danger);
    }

    .server-offline .status-text {
      color: var(--danger);
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
      min-width: 900px;
    }

    .table thead th {
      background: var(--bg-secondary);
      color: var(--text-primary);
      font-weight: 600;
      padding: 1rem 0.75rem;
      text-align: left;
      border-bottom: 1px solid var(--border);
      font-size: 0.875rem;
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

    /* Player ID Styling */
    .player-id {
      background: rgba(59, 130, 246, 0.1);
      color: var(--primary);
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      font-weight: 600;
      font-size: 0.75rem;
      min-width: 40px;
      text-align: center;
      display: inline-block;
    }

    /* Player Name Styling */
    .player-name {
      font-weight: 600;
      color: var(--text-primary);
    }

    /* Ping Indicator */
    .ping-indicator {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .ping-value {
      font-family: 'SF Mono', 'Monaco', 'Inconsolata', monospace;
      font-weight: 600;
    }

    .ping-good {
      color: var(--success);
    }

    .ping-medium {
      color: var(--warning);
    }

    .ping-bad {
      color: var(--danger);
    }

    .ping-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
    }

    /* Identifier Styling */
    .identifier {
      font-family: 'SF Mono', 'Monaco', 'Inconsolata', monospace;
      font-size: 0.8rem;
      color: var(--text-secondary);
      background: rgba(255, 255, 255, 0.05);
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
    }

    .identifier.not-found {
      color: var(--text-muted);
      font-style: italic;
    }

    /* Action Buttons */
    .action-btn {
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
      display: inline-flex;
      align-items: center;
      gap: 0.375rem;
    }

    .action-btn:hover {
      transform: translateY(-1px);
    }

    .action-btn.ban {
      background: var(--danger);
    }

    .action-btn.ban:hover {
      background: #dc2626;
    }

    .action-btn.kick {
      background: var(--warning);
    }

    .action-btn.kick:hover {
      background: #d97706;
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
          <a href="https://<?php echo $website_config['site_domain']; ?>/manage/banlist.php" class="nav-link">
            <i class="uil uil-ban"></i>
            <span>Banlist</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="https://<?php echo $website_config['site_domain']; ?>/manage/playerlist.php" class="nav-link active">
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
          <span>Playerlist</span>
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
            <h1 class="card-title">Online Players</h1>
            <p class="card-subtitle">Manage connected players on your server</p>
          </div>
          
          <div class="card-body">
            <?php
            include '../database.php';
            $ip = $_SESSION['ip'];
            $stmt = mysqli_prepare($link, "SELECT server.port FROM `server` WHERE server.serverip = ?");
            mysqli_stmt_bind_param($stmt, "s", $_SESSION['ip']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $sv = mysqli_fetch_array($result);
            $port = $sv['port'];

            $serverOnline = false;
            $players = array();
            $infos = array();

            if (!$fp = fsockopen($ip, $port, $errno, $errstr, 1)) {
              // server offline
              $serverOnline = false;
            } else {
              $players_json = file_get_contents("http://" . $ip . ":" . $port . "/players.json");
              $infos_json = file_get_contents("http://" . $ip . ":" . $port . "/info.json");
              $players = json_decode($players_json, true);
              $infos = json_decode($infos_json, true);
              $serverOnline = true;
              fclose($fp);
            }

            mysqli_close($link);
            ?>

            <!-- Server Status -->
            <div class="server-status <?php echo !$serverOnline ? 'server-offline' : ''; ?>">
              <div class="status-dot"></div>
              <div class="status-text">
                <?php if ($serverOnline): ?>
                  Server Online - <?php echo count($players); ?> Player(s) Connected
                <?php else: ?>
                  Server Offline - Unable to retrieve player data
                <?php endif; ?>
              </div>
            </div>

            <?php if ($serverOnline && !empty($players)): ?>
              <div class="search-container">
                <div class="search-wrapper">
                  <input type="text" id="searchInput" class="search-input" placeholder="Search by ID or Name...">
                  <i class="uil uil-search search-icon"></i>
                </div>
              </div>

              <div class="table-container">
                <table class="table" id="playerTable">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Steam</th>
                      <th>License</th>
                      <th>Ping</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($players as $player) { ?>
                      <tr>
                        <td>
                          <span class="player-id"><?php echo htmlspecialchars($player['id']); ?></span>
                        </td>
                        <td>
                          <span class="player-name"><?php echo htmlspecialchars($player['name']); ?></span>
                        </td>
                        <td>
                          <?php
                          $steamFound = false;
                          foreach ($player['identifiers'] as $identifier) {
                            if (str_starts_with($identifier, 'steam:')) {
                              $steamFound = true;
                              echo '<span class="identifier">' . htmlspecialchars($identifier) . '</span>';
                              break;
                            }
                          }
                          if (!$steamFound) {
                            echo '<span class="identifier not-found">not found</span>';
                          }
                          ?>
                        </td>
                        <td>
                          <?php
                          $licenseFound = false;
                          foreach ($player['identifiers'] as $identifier) {
                            if (str_starts_with($identifier, 'license:')) {
                              $licenseFound = true;
                              echo '<span class="identifier">' . htmlspecialchars($identifier) . '</span>';
                              break;
                            }
                          }
                          if (!$licenseFound) {
                            echo '<span class="identifier not-found">not found</span>';
                          }
                          ?>
                        </td>
                        <td>
                          <div class="ping-indicator">
                            <?php
                            $ping = $player['ping'];
                            $pingClass = 'ping-good';
                            if ($ping > 100) $pingClass = 'ping-medium';
                            if ($ping > 200) $pingClass = 'ping-bad';
                            ?>
                            <div class="ping-dot <?php echo $pingClass; ?>" style="background: var(--<?php echo str_replace('ping-', '', $pingClass) === 'good' ? 'success' : (str_replace('ping-', '', $pingClass) === 'medium' ? 'warning' : 'danger'); ?>);"></div>
                            <span class="ping-value <?php echo $pingClass; ?>"><?php echo $ping; ?>ms</span>
                          </div>
                        </td>
                        <td>
                          <form style="display: inline;" method="post" onsubmit="return confirmAction(event, 'ban', '<?php echo htmlspecialchars($player['name']); ?>')">
                            <input type="hidden" name="itemid" value="<?php echo htmlspecialchars($player['id']); ?>">
                            <button type="submit" name="addBtn" class="action-btn ban">
                              <i class="uil uil-ban"></i>
                              Ban
                            </button>
                          </form>
                          <form style="display: inline;" method="post" onsubmit="return confirmAction(event, 'kick', '<?php echo htmlspecialchars($player['name']); ?>')">
                            <input type="hidden" name="itemid" value="<?php echo htmlspecialchars($player['id']); ?>">
                            <button type="submit" name="kickBtn" class="action-btn kick">
                              <i class="uil uil-signout"></i>
                              Kick
                            </button>
                          </form>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php elseif ($serverOnline && empty($players)): ?>
              <div class="empty-state">
                <i class="uil uil-users-alt"></i>
                <h3>No Players Online</h3>
                <p>There are currently no players connected to the server.</p>
              </div>
            <?php else: ?>
              <div class="empty-state">
                <i class="uil uil-wifi-slash"></i>
                <h3>Server Offline</h3>
                <p>Unable to connect to the server. Please check if the server is running.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <footer class="footer">
        <div>
          2022 - <script>document.write(new Date().getFullYear())</script> <?php echo $website_config['site_copyright']; ?>
        </div>
      </footer>
    </main>
  </div>

  <script>
    // Enhanced search functionality
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
      const table = document.getElementById('playerTable');
      const tbody = table.getElementsByTagName('tbody')[0];
      
      searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const rows = tbody.getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
          const row = rows[i];
          const cells = row.getElementsByTagName('td');
          let found = false;
          
          // Search through ID and Name columns primarily
          if (cells.length > 1) {
            const id = cells[0].textContent || cells[0].innerText;
            const name = cells[1].textContent || cells[1].innerText;
            
            if (id.toLowerCase().indexOf(searchTerm) > -1 || 
                name.toLowerCase().indexOf(searchTerm) > -1) {
              found = true;
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
    }

    // Confirmation dialogs for actions
    function confirmAction(event, action, playerName) {
      event.preventDefault();
      
      const actionText = action === 'ban' ? 'ban' : 'kick';
      const actionColor = action === 'ban' ? '#ef4444' : '#f59e0b';
      
      Swal.fire({
        title: `Confirm ${actionText.charAt(0).toUpperCase() + actionText.slice(1)}`,
        text: `Are you sure you want to ${actionText} player "${playerName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: actionColor,
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Yes, ${actionText} player`,
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          event.target.closest('form').submit();
        }
      });
      
      return false;
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

    // Auto-refresh player list every 10 seconds
    let autoRefreshInterval;
    function startAutoRefresh() {
      autoRefreshInterval = setInterval(() => {
        // Only refresh if no active search
        if (!searchInput || searchInput.value === '') {
          console.log('Auto-refreshing player list...');
          // In a real implementation, you'd use AJAX to refresh the table data
          // For now, we'll just reload the page
          if (window.location.pathname.includes('playerlist.php')) {
            window.location.reload();
          }
        }
      }, 10000); // Refresh every 10 seconds
    }

    function stopAutoRefresh() {
      if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
      }
    }

    // Start auto-refresh on page load (only if server is online)
    document.addEventListener('DOMContentLoaded', function() {
      const serverStatus = document.querySelector('.server-status');
      if (serverStatus && !serverStatus.classList.contains('server-offline')) {
        startAutoRefresh();
      }
    });

    // Stop auto-refresh when page is hidden
    document.addEventListener('visibilitychange', function() {
      if (document.hidden) {
        stopAutoRefresh();
      } else {
        const serverStatus = document.querySelector('.server-status');
        if (serverStatus && !serverStatus.classList.contains('server-offline')) {
          startAutoRefresh();
        }
      }
    });

    // Ping color animation
    document.addEventListener('DOMContentLoaded', function() {
      const pingDots = document.querySelectorAll('.ping-dot');
      pingDots.forEach(dot => {
        // Add a subtle pulse animation to ping indicators
        setInterval(() => {
          dot.style.transform = 'scale(1.2)';
          setTimeout(() => {
            dot.style.transform = 'scale(1)';
          }, 200);
        }, 2000);
      });
    });

    // Enhanced table interactions
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

      // Add hover effect to action buttons
      const actionButtons = document.querySelectorAll('.action-btn');
      actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-2px) scale(1.05)';
        });
        
        button.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0) scale(1)';
        });
      });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
      // Ctrl/Cmd + F to focus search
      if ((e.ctrlKey || e.metaKey) && e.key === 'f' && searchInput) {
        e.preventDefault();
        searchInput.focus();
      }
      
      // Escape to clear search
      if (e.key === 'Escape' && searchInput) {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('keyup'));
      }
    });

    // Prevent right-click and text selection
    document.addEventListener('contextmenu', function(e) {
      e.preventDefault();
    });

    document.addEventListener('selectstart', function(e) {
      e.preventDefault();
    });

    // Live server status check (optional)
    function checkServerStatus() {
      // This would be implemented with AJAX in a real scenario
      // For now, just update the pulse animation
      const statusDot = document.querySelector('.status-dot');
      if (statusDot) {
        statusDot.style.animation = 'none';
        setTimeout(() => {
          statusDot.style.animation = 'pulse 2s infinite';
        }, 100);
      }
    }

    // Check server status every 30 seconds
    setInterval(checkServerStatus, 30000);

    // Player count animation
    document.addEventListener('DOMContentLoaded', function() {
      const statusText = document.querySelector('.status-text');
      if (statusText && statusText.textContent.includes('Player(s) Connected')) {
        // Animate the player count number
        const text = statusText.textContent;
        const match = text.match(/(\d+) Player\(s\) Connected/);
        if (match) {
          const count = parseInt(match[1]);
          let currentCount = 0;
          const increment = Math.ceil(count / 20);
          
          const countInterval = setInterval(() => {
            currentCount += increment;
            if (currentCount >= count) {
              currentCount = count;
              clearInterval(countInterval);
            }
            statusText.textContent = text.replace(/\d+ Player\(s\) Connected/, `${currentCount} Player(s) Connected`);
          }, 50);
        }
      }
    });
  </script>
</body>
</html>