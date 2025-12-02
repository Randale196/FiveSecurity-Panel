<?php
session_start();
include('../../../func.php');
$site_name = $website_config["site_name"];

if (!isAdmin()) {
  header("Location: " . $website_config['site_url'] . "/");
  exit;
}

$stmt = $conn->prepare("SELECT maintenance FROM `system` WHERE id = 1");
$stmt->execute();
$result = $stmt->get_result();
$maintenance_status = $result->fetch_assoc()['maintenance'] ?? 0;
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['maintenance_status'])) {
    $maintenance_status = isset($_POST['maintenance_status']) ? intval($_POST['maintenance_status']) : 0;
    $stmt = $conn->prepare("UPDATE `system` SET maintenance = ? WHERE id = 1");
    $stmt->bind_param("i", $maintenance_status);
    
    if ($stmt->execute()) {
        $success_message = "Maintenance settings updated successfully";
    } else {
        $error_message = "Error updating maintenance settings";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['news_title'])) {
    $news_title = trim($_POST['news_title']);
    $news_content = trim($_POST['news_content']);
    
    if (empty($news_title) || empty($news_content)) {
        $error_message = "Title and content are required";
    } else {
        $stmt = $conn->prepare("INSERT INTO news (text, date, user) VALUES (?, NOW(), ?)");
        $news_text = $news_title . " - " . $news_content;
        $stmt->bind_param("ss", $news_text, $_SESSION['username']);
        
        if ($stmt->execute()) {
            $success_message = "News posted successfully";
        } else {
            $error_message = "Error posting news";
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_identifier = trim($_POST['user_id']);
    $ban_reason = trim($_POST['ban_reason']);
    
    if (empty($user_identifier) || empty($ban_reason)) {
        $error_message = "User identifier and reason are required";
    } else {
        $user_id = $user_identifier;
        if (!is_numeric($user_identifier)) {
            $stmt = $conn->prepare("SELECT userid FROM users WHERE username = ?");
            $stmt->bind_param("s", $user_identifier);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $user_id = $row['userid'];
            } else {
                $error_message = "User not found";
            }
            $stmt->close();
        }

        if (!isset($error_message)) {
            $stmt = $conn->prepare("INSERT INTO panelbans (userid, reason) VALUES (?, ?)");
            $stmt->bind_param("ss", $user_id, $ban_reason);
            
            if ($stmt->execute()) {
                $success_message = "User banned successfully";
            } else {
                $error_message = "Error banning user";
            }
            $stmt->close();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_news'])) {
    $news_id = intval($_POST['news_id']);
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $news_id);
    
    if ($stmt->execute()) {
        $success_message = "News deleted successfully";
    } else {
        $error_message = "Error deleting news";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $website_config['site_name']; ?> | Website Settings</title>
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

        .settings-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
          gap: 1.5rem;
          margin-bottom: 2rem;
        }

        .settings-card {
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 12px;
          overflow: hidden;
          transition: all 0.2s ease;
          height: 100%;
        }

        .settings-card:hover {
          transform: translateY(-2px);
          box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .card-header {
          padding: 1.5rem;
          border-bottom: 1px solid var(--border);
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
          font-size: 1.25rem;
        }

        .card-body {
          padding: 1.5rem;
          height: calc(100% - 80px);
          display: flex;
          flex-direction: column;
        }

        .form-group {
          margin-bottom: 1rem;
          flex-grow: 0;
        }

        .form-group:last-child {
          margin-top: auto;
          margin-bottom: 0;
        }

        .form-label {
          display: block;
          font-size: 0.875rem;
          font-weight: 500;
          margin-bottom: 0.5rem;
          color: var(--text-primary);
        }

        .form-control {
          width: 100%;
          padding: 0.75rem;
          background: rgba(255, 255, 255, 0.05);
          border: 1px solid var(--border);
          border-radius: 8px;
          color: var(--text-primary);
          font-size: 0.875rem;
          transition: all 0.2s ease;
        }

        .form-control:focus {
          outline: none;
          border-color: var(--primary);
          box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        textarea.form-control {
          min-height: 100px;
          resize: vertical;
        }

        .btn {
          display: inline-flex;
          align-items: center;
          gap: 0.5rem;
          padding: 0.75rem 1.5rem;
          border: none;
          border-radius: 8px;
          font-size: 0.875rem;
          font-weight: 500;
          cursor: pointer;
          transition: all 0.2s ease;
          width: 100%;
          justify-content: center;
          text-decoration: none;
        }

        .btn-primary {
          background: var(--primary);
          color: white;
        }

        .btn-primary:hover {
          background: var(--primary-dark);
          transform: translateY(-1px);
        }

        .btn-danger {
          background: var(--danger);
          color: white;
        }

        .btn-danger:hover {
          background: #dc2626;
          transform: translateY(-1px);
        }

        .btn-warning {
          background: var(--warning);
          color: #1f2937;
        }

        .btn-warning:hover {
          background: #d97706;
        }

        .maintenance-card .card-header { background: rgba(245, 158, 11, 0.1); }
        .maintenance-card .card-title i { color: var(--warning); }

        .news-card .card-header { background: rgba(59, 130, 246, 0.1); }
        .news-card .card-title i { color: var(--primary); }

        .ban-card .card-header { background: rgba(239, 68, 68, 0.1); }
        .ban-card .card-title i { color: var(--danger); }

        .news-table-card {
          grid-column: 1 / -1;
          margin-top: 1rem;
        }

        .table-responsive {
          background: var(--bg-card);
          border-radius: 8px;
          overflow: hidden;
        }

        .table {
          width: 100%;
          margin: 0;
          color: var(--text-primary);
        }

        .table th {
          background: rgba(59, 130, 246, 0.1);
          border-bottom: 1px solid var(--border);
          padding: 1rem;
          font-weight: 600;
          color: var(--text-primary);
        }

        .table td {
          padding: 1rem;
          border-bottom: 1px solid var(--border);
          color: var(--text-secondary);
        }

        .table tr:hover {
          background: rgba(59, 130, 246, 0.05);
        }

        .btn-sm {
          padding: 0.5rem 1rem;
          font-size: 0.75rem;
          width: auto;
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

        .status-indicator {
          display: inline-flex;
          align-items: center;
          gap: 0.5rem;
          padding: 0.5rem 1rem;
          border-radius: 6px;
          font-size: 0.875rem;
          font-weight: 500;
          margin-bottom: 1rem;
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

          .settings-grid {
            grid-template-columns: 1fr;
          }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="uil uil-shield-check" style="font-size: 20px; color: white;"></i>
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
                        <a href="/admin/website/settings" class="nav-link active">
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
                <div class="header-title">Website Settings</div>
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
                <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="uil uil-check-circle"></i>
                    <?php echo $success_message; ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="uil uil-times-circle"></i>
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>

                <div class="settings-grid">
                    <!-- Maintenance System -->
                    <div class="settings-card maintenance-card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="uil uil-constructor"></i>
                                Maintenance System
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="status-indicator <?php echo $maintenance_status ? 'status-offline' : 'status-online'; ?>">
                                <i class="uil uil-<?php echo $maintenance_status ? 'times-circle' : 'check-circle'; ?>"></i>
                                <?php echo $maintenance_status ? 'Maintenance Mode ON' : 'System Online'; ?>
                            </div>

                            <form method="post">
                                <div class="form-group">
                                    <label class="form-label">Maintenance Status</label>
                                    <select class="form-control" name="maintenance_status">
                                        <option value="0" <?php echo $maintenance_status == 0 ? 'selected' : ''; ?>>Off</option>
                                        <option value="1" <?php echo $maintenance_status == 1 ? 'selected' : ''; ?>>On</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Maintenance Reason</label>
                                    <textarea class="form-control" name="maintenance_reason" rows="3" placeholder="Enter maintenance reason..."></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="uil uil-wrench"></i>
                                        Update Maintenance
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- News Management -->
                    <div class="settings-card news-card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="uil uil-newspaper"></i>
                                News Management
                            </h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="form-group">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="news_title" placeholder="Enter news title">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Content</label>
                                    <textarea class="form-control" name="news_content" rows="4" placeholder="Enter news content..."></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="uil uil-plus"></i>
                                        Post News
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Ban Account -->
                    <div class="settings-card ban-card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="uil uil-ban"></i>
                                Ban Management
                            </h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="form-group">
                                    <label class="form-label">Username or User ID</label>
                                    <input type="text" class="form-control" name="user_id" placeholder="Enter username or ID">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Ban Reason</label>
                                    <textarea class="form-control" name="ban_reason" rows="3" placeholder="Enter ban reason..."></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="uil uil-user-times"></i>
                                        Ban User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- News Table -->
                <div class="news-table-card">
                    <div class="settings-card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="uil uil-list-ul"></i>
                                Recent News
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Text</th>
                                            <th>Date</th>
                                            <th>User</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $conn->prepare("SELECT * FROM news ORDER BY date DESC LIMIT 10");
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>#" . htmlspecialchars($row['id']) . "</td>";
                                                echo "<td>" . htmlspecialchars(substr($row['text'], 0, 80)) . (strlen($row['text']) > 80 ? '...' : '') . "</td>";
                                                echo "<td>" . date('M j, Y g:i A', strtotime($row['date'])) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['user']) . "</td>";
                                                echo "<td>
                                                        <form method='post' style='display: inline;' onsubmit='return confirm(\"Are you sure?\")'>
                                                          <input type='hidden' name='news_id' value='" . $row['id'] . "'>
                                                          <button type='submit' name='delete_news' class='btn btn-danger btn-sm'>
                                                            <i class='uil uil-trash-alt'></i>
                                                            Delete
                                                          </button>
                                                        </form>
                                                      </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='5' style='text-align: center; padding: 2rem; color: var(--text-secondary);'>No news found</td></tr>";
                                        }
                                        $stmt->close();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
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

        // Auto-clear forms after successful submission
        <?php if (isset($success_message)): ?>
        setTimeout(() => {
            document.querySelectorAll('form').forEach(form => {
                if (!form.querySelector('select[name="maintenance_status"]')) {
                    form.reset();
                }
            });
        }, 2000);
        <?php endif; ?>
    </script>
</body>
</html>