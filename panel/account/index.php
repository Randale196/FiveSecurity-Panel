<?php
session_start();
require_once '../config.php';
require_once '../database.php';
require_once '../func.php';

// Handle form submissions
if (isset($_POST['emailchange'])) {
  $id = $_SESSION['id'];
  $email = $_POST['newmail'];
  $password = base64_encode($_POST['password']);

  if (empty($email) || empty($_POST['password'])) {
    echo '<script>Swal.fire({icon: "error", title: "Error", text: "Please fill in all fields"});</script>';
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo '<script>Swal.fire({icon: "error", title: "Error", text: "Invalid email address"});</script>';
  } else {
    $checkSql = "SELECT COUNT(*) FROM `users` WHERE `email` = ? AND `userid` != ?";
    $checkStmt = mysqli_prepare($link, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "si", $email, $id);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_bind_result($checkStmt, $count);
    mysqli_stmt_fetch($checkStmt);
    mysqli_stmt_close($checkStmt);

    if ($count > 0) {
      echo '<script>Swal.fire({icon: "error", title: "Error", text: "Email already in use"});</script>';
    } else {
      $sql = "UPDATE `users` SET `email` = ? WHERE `userid` = ? AND `password` = ?";
      $stmt = mysqli_prepare($link, $sql);
      mysqli_stmt_bind_param($stmt, "sis", $email, $id, $password);
      if (mysqli_stmt_execute($stmt)) {
        echo '<script>Swal.fire({icon: "success", title: "Success", text: "Email updated successfully"});</script>';
      } else {
        echo '<script>Swal.fire({icon: "error", title: "Error", text: "Wrong password"});</script>';
      }
      mysqli_stmt_close($stmt);
    }
  }
}

if (isset($_POST['passswordchange'])) {
  $id = $_SESSION['id'];
  $currentPassword = base64_encode($_POST['currentpass']);
  $password = base64_encode($_POST['newpass']);

  $sql = "UPDATE `users` SET `password` = ? WHERE `userid` = ? AND `password` = ?";
  $stmt = mysqli_prepare($link, $sql);
  mysqli_stmt_bind_param($stmt, "sis", $password, $id, $currentPassword);
  if (mysqli_stmt_execute($stmt)) {
    echo '<script>Swal.fire({icon: "success", title: "Success", text: "Password updated successfully"});</script>';
  } else {
    echo '<script>Swal.fire({icon: "error", title: "Error", text: "Current password wrong"});</script>';
  }
  mysqli_stmt_close($stmt);
}

if (isset($_POST['usernamechange'])) {
  $id = $_SESSION['id'];
  $username = $_POST['newusername'];
  $password = base64_encode($_POST['password']);

  $checkSql = "SELECT COUNT(*) FROM `users` WHERE `username` = ?";
  $checkStmt = mysqli_prepare($link, $checkSql);
  mysqli_stmt_bind_param($checkStmt, "s", $username);
  mysqli_stmt_execute($checkStmt);
  mysqli_stmt_bind_result($checkStmt, $count);
  mysqli_stmt_fetch($checkStmt);
  mysqli_stmt_close($checkStmt);

  if ($count == 0) {
    $sql = "UPDATE `users` SET `username` = ? WHERE `userid` = ? AND `password` = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "sis", $username, $id, $password);
    if (mysqli_stmt_execute($stmt)) {
      echo '<script>Swal.fire({icon: "success", title: "Success", text: "Username updated successfully"});</script>';
      $_SESSION["username"] = $username;
    } else {
      echo '<script>Swal.fire({icon: "error", title: "Error", text: "Wrong password"});</script>';
    }
    mysqli_stmt_close($stmt);
  } else {
    echo '<script>Swal.fire({icon: "error", title: "Error", text: "Username already exists"});</script>';
  }
}

// Handle 2FA toggle
if (isset($_POST['toggle2fa'])) {
  $id = $_SESSION['id'];
  $password = base64_encode($_POST['twofa_password']);
  
  if (isset($_POST['enable2fa'])) {
    $secret = $_POST['secret'];
    $sql = "UPDATE `users` SET `2fa_secret` = ? WHERE `userid` = ? AND `password` = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "sis", $secret, $id, $password);
    if (mysqli_stmt_execute($stmt)) {
      echo '<script>Swal.fire({icon: "success", title: "Success", text: "2FA enabled successfully"});</script>';
    } else {
      echo '<script>Swal.fire({icon: "error", title: "Error", text: "Wrong password"});</script>';
    }
    mysqli_stmt_close($stmt);
  } else {
    $sql = "UPDATE `users` SET `2fa_secret` = NULL WHERE `userid` = ? AND `password` = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "is", $id, $password);
    if (mysqli_stmt_execute($stmt)) {
      echo '<script>Swal.fire({icon: "success", title: "Success", text: "2FA disabled successfully"});</script>';
    } else {
      echo '<script>Swal.fire({icon: "error", title: "Error", text: "Wrong password"});</script>';
    }
    mysqli_stmt_close($stmt);
  }
}

// Handle account deletion
if (isset($_POST['delete_account'])) {
  $id = $_SESSION['id'];
  $password = base64_encode($_POST['delete_password']);
  
  $sql = "DELETE FROM `users` WHERE `userid` = ? AND `password` = ?";
  $stmt = mysqli_prepare($link, $sql);
  mysqli_stmt_bind_param($stmt, "is", $id, $password);
  if (mysqli_stmt_execute($stmt)) {
    session_destroy();
    echo '<script>
      Swal.fire({
        icon: "success", 
        title: "Account Deleted", 
        text: "Your account has been permanently deleted."
      }).then(() => {
        window.location.href = "/";
      });
    </script>';
  } else {
    echo '<script>Swal.fire({icon: "error", title: "Error", text: "Wrong password"});</script>';
  }
  mysqli_stmt_close($stmt);
}

require_once '../vendor/autoload.php';
use PHPGangsta_GoogleAuthenticator;
$ga = new PHPGangsta_GoogleAuthenticator();

$id = $_SESSION['id'];
$secret_sql = "SELECT 2fa_secret FROM users WHERE userid = ? AND 2fa_secret IS NOT NULL";
$secret_stmt = mysqli_prepare($link, $secret_sql);
mysqli_stmt_bind_param($secret_stmt, "i", $id);
mysqli_stmt_execute($secret_stmt);
mysqli_stmt_bind_result($secret_stmt, $existing_secret);
mysqli_stmt_fetch($secret_stmt);
mysqli_stmt_close($secret_stmt);

$secret = $existing_secret ?: $ga->createSecret();
$qrCodeUrl = $ga->getQRCodeGoogleUrl($website_config['site_name'], $secret);

// Get user data
$idddd = $_SESSION["id"];
$query = "SELECT * FROM `users` WHERE `userid` = ?";
$stmt = $link->prepare($query);
$stmt->bind_param("i", $idddd);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$dc = $row['discord'];
$em = $row['email'];
$usn = $row['username'];
$stmt->close();

// Get login history (if table exists)
$login_history = [];
$check_table = "SHOW TABLES LIKE 'login_logs'";
$table_result = mysqli_query($link, $check_table);
if (mysqli_num_rows($table_result) > 0) {
  $login_query = "SELECT * FROM `login_logs` WHERE `userid` = ? ORDER BY `login_time` DESC LIMIT 10";
  $login_stmt = $link->prepare($login_query);
  $login_stmt->bind_param("i", $idddd);
  $login_stmt->execute();
  $login_result = $login_stmt->get_result();
  $login_history = $login_result->fetch_all(MYSQLI_ASSOC);
  $login_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $website_config['site_name']; ?> | Account Settings</title>
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

        /* Main Content */
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

        .user-menu.active .dropdown-arrow {
          transform: rotate(180deg);
        }

        /* Content Area */
        .content-area {
          padding: 2rem;
          overflow-x: auto;
        }

        .settings-row {
          display: flex;
          gap: 1.5rem;
          min-width: 2000px;
          padding-bottom: 1rem;
        }

        .settings-card {
          flex: 1;
          min-width: 300px;
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 12px;
          overflow: hidden;
          height: 500px;
          display: flex;
          flex-direction: column;
        }

        .card-header {
          padding: 1.5rem;
          border-bottom: 1px solid var(--border);
          flex-shrink: 0;
        }

        .card-title {
          font-size: 1.125rem;
          font-weight: 600;
          display: flex;
          align-items: center;
          gap: 0.75rem;
          color: var(--text-primary);
        }

        .card-title i {
          color: var(--primary);
          font-size: 1.25rem;
        }

        .card-body {
          padding: 1.5rem;
          flex: 1;
          overflow-y: auto;
        }

        .form-group {
          margin-bottom: 1rem;
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

        .form-control:read-only {
          background: rgba(100, 116, 139, 0.1);
          color: var(--text-secondary);
          cursor: not-allowed;
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
        }

        .btn-danger {
          background: var(--danger);
          color: white;
        }

        .btn-danger:hover {
          background: #dc2626;
        }

        /* Discord Card Special Styling */
        .discord-card {
          background: linear-gradient(135deg, #5865f2 0%, #7289da 100%);
          border: none;
          color: white;
        }

        .discord-card .card-title {
          color: white;
        }

        .discord-card .card-title i {
          color: white;
        }

        .discord-info {
          background: rgba(255, 255, 255, 0.1);
          border-radius: 8px;
          padding: 1rem;
          margin-bottom: 1rem;
          font-size: 0.875rem;
          line-height: 1.5;
        }

        .discord-buttons {
          display: flex;
          flex-direction: column;
          gap: 0.75rem;
        }

        .btn-discord {
          background: rgba(255, 255, 255, 0.2);
          color: white;
          border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-discord:hover {
          background: rgba(255, 255, 255, 0.3);
          color: white;
        }

        /* 2FA Styling */
        .twofa-qr {
          text-align: center;
          margin: 1rem 0;
        }

        .twofa-qr img {
          max-width: 160px;
          border-radius: 8px;
          border: 1px solid var(--border);
          padding: 0.5rem;
          background: white;
        }

        .twofa-secret {
          background: var(--bg-dark);
          border-radius: 8px;
          padding: 1rem;
          margin: 1rem 0;
          text-align: center;
        }

        .twofa-secret code {
          font-family: monospace;
          color: var(--primary);
          word-break: break-all;
          font-size: 0.75rem;
        }

        .twofa-status {
          padding: 0.75rem;
          border-radius: 8px;
          margin-bottom: 1rem;
          font-size: 0.875rem;
          display: flex;
          align-items: center;
          gap: 0.5rem;
        }

        .twofa-enabled {
          background: rgba(16, 185, 129, 0.1);
          color: var(--success);
          border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .twofa-disabled {
          background: rgba(239, 68, 68, 0.1);
          color: var(--danger);
          border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Login History */
        .login-item {
          padding: 1rem;
          border-bottom: 1px solid var(--border);
          display: flex;
          align-items: center;
          gap: 1rem;
        }

        .login-item:last-child {
          border-bottom: none;
        }

        .login-icon {
          width: 40px;
          height: 40px;
          background: rgba(16, 185, 129, 0.1);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          flex-shrink: 0;
        }

        .login-icon i {
          color: var(--success);
          font-size: 1.125rem;
        }

        .login-details h4 {
          font-size: 0.875rem;
          margin-bottom: 0.25rem;
          font-weight: 600;
        }

        .login-details p {
          font-size: 0.75rem;
          color: var(--text-secondary);
          margin: 0;
        }

        .empty-state {
          text-align: center;
          padding: 2rem;
          color: var(--text-secondary);
        }

        .empty-state i {
          font-size: 3rem;
          margin-bottom: 1rem;
          opacity: 0.5;
        }

        /* Mobile Responsive */
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

          .settings-row {
            flex-direction: column;
            min-width: auto;
          }

          .settings-card {
            min-width: auto;
            height: auto;
          }
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
                        <a href="/" class="nav-link">
                            <i class="uil uil-estate"></i>
                            <span>Home</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/account" class="nav-link active">
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
                <div class="header-title">Account Settings</div>
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
                        <a href="/account" class="dropdown-item">
                            <i class="uil uil-setting"></i>
                            <span>Settings</span>
                        </a>
                        <a href="/logout.php" class="dropdown-item">
                            <i class="uil uil-signout"></i>
                            <span>Log out</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="settings-row">
                    <!-- Email Card -->
                    <div class="settings-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="uil uil-envelope"></i>
                                Change Email
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="form-group">
                                    <label class="form-label">Current Email</label>
                                    <input type="email" class="form-control" value="<?php echo $em; ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">New Email</label>
                                    <input type="email" class="form-control" name="newmail" required>
                                </div>
                                <div class="form-group">
                                <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <button type="submit" name="emailchange" class="btn btn-primary">
                                    <i class="uil uil-check"></i>
                                    Update Email
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Password Card -->
                    <div class="settings-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="uil uil-lock"></i>
                                Change Password
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="form-group">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-control" name="currentpass" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="newpass" required>
                                </div>
                                <button type="submit" name="passswordchange" class="btn btn-primary">
                                    <i class="uil uil-check"></i>
                                    Update Password
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Username Card -->
                    <div class="settings-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="uil uil-user"></i>
                                Change Username
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="form-group">
                                    <label class="form-label">Current Username</label>
                                    <input type="text" class="form-control" value="<?php echo $usn; ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">New Username</label>
                                    <input type="text" class="form-control" name="newusername" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <button type="submit" name="usernamechange" class="btn btn-primary">
                                    <i class="uil uil-check"></i>
                                    Update Username
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Discord Card -->
                    <div class="settings-card discord-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="uil uil-discord"></i>
                                Discord Integration
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="discord-info">
                                <p><strong>Connect your Discord account</strong> to get the Customer role on our Discord server.</p>
                                <?php if ($dc): ?>
                                <p><strong>Connected ID:</strong> <?php echo $dc; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="discord-buttons">
                                <a href="https://<?php echo $website_config['site_domain']; ?>/discord/link.php" class="btn btn-discord">
                                    <i class="uil uil-link"></i>
                                    <?php echo $dc ? 'Reconnect' : 'Connect'; ?>
                                </a>
                                <?php if ($dc): ?>
                                <a href="https://<?php echo $website_config['site_domain']; ?>/discord/unlink.php" class="btn btn-discord">
                                    <i class="uil uil-unlink"></i>
                                    Disconnect
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 2FA Card -->
                    <div class="settings-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="uil uil-shield-check"></i>
                                Two-Factor Authentication
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($existing_secret): ?>
                                <div class="twofa-status twofa-enabled">
                                    <i class="uil uil-check-circle"></i>
                                    2FA is enabled and active
                                </div>
                                <form method="post">
                                    <div class="form-group">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" name="twofa_password" required>
                                    </div>
                                    <button type="submit" name="toggle2fa" class="btn btn-danger">
                                        <i class="uil uil-times"></i>
                                        Disable 2FA
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="twofa-status twofa-disabled">
                                    <i class="uil uil-times-circle"></i>
                                    2FA is currently disabled
                                </div>
                                <div class="twofa-qr">
                                    <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code">
                                </div>
                                <div class="twofa-secret">
                                    <p><strong>Secret Key:</strong></p>
                                    <code><?php echo $secret; ?></code>
                                </div>
                                <form method="post">
                                    <input type="hidden" name="secret" value="<?php echo $secret; ?>">
                                    <div class="form-group">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" name="twofa_password" required>
                                    </div>
                                    <button type="submit" name="toggle2fa" class="btn btn-primary">
                                        <i class="uil uil-check"></i>
                                        Enable 2FA
                                    </button>
                                    <input type="hidden" name="enable2fa" value="1">
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Login History Card -->
                    <div class="settings-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="uil uil-history"></i>
                                Recent Login Activity
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($login_history)): ?>
                                <div class="empty-state">
                                    <i class="uil uil-history"></i>
                                    <p>No recent login activity found</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($login_history as $login): ?>
                                    <div class="login-item">
                                        <div class="login-icon">
                                            <i class="uil uil-signin"></i>
                                        </div>
                                        <div class="login-details">
                                            <h4><?php echo isset($login['ip_address']) ? $login['ip_address'] : 'Unknown IP'; ?></h4>
                                            <p><?php echo isset($login['user_agent']) ? substr($login['user_agent'], 0, 50) . '...' : 'Unknown Browser'; ?></p>
                                            <p><?php echo isset($login['login_time']) ? date('M j, Y g:i A', strtotime($login['login_time'])) : 'Unknown Time'; ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Account Danger Zone -->
                    <div class="settings-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="uil uil-exclamation-triangle"></i>
                                Danger Zone
                            </div>
                        </div>
                        <div class="card-body">
                            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                                <h4 style="color: var(--danger); margin-bottom: 0.5rem; font-size: 0.875rem;">Delete Account</h4>
                                <p style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0;">Once you delete your account, there is no going back. Please be certain.</p>
                            </div>
                            <button onclick="confirmAccountDeletion()" class="btn btn-danger">
                                <i class="uil uil-trash-alt"></i>
                                Delete Account
                            </button>
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

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.getElementById('userDropdown');
            
            if (!userMenu.contains(event.target)) {
                dropdown.classList.remove('show');
                userMenu.classList.remove('active');
            }
        });

        function confirmAccountDeletion() {
            Swal.fire({
                title: 'Are you absolutely sure?',
                text: "This action cannot be undone. This will permanently delete your account and remove all associated data.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete my account',
                cancelButtonText: 'Cancel',
                input: 'password',
                inputPlaceholder: 'Enter your password to confirm',
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to enter your password!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form to submit the deletion request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.style.display = 'none';
                    
                    const deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'delete_account';
                    deleteInput.value = '1';
                    
                    const passwordInput = document.createElement('input');
                    passwordInput.type = 'hidden';
                    passwordInput.name = 'delete_password';
                    passwordInput.value = result.value;
                    
                    form.appendChild(deleteInput);
                    form.appendChild(passwordInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

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
            menuButton.style.cssText = 'background: none; border: none; color: var(--text-primary); font-size: 1.5rem; cursor: pointer; margin-right: 1rem;';
            menuButton.onclick = toggleSidebar;
            header.insertBefore(menuButton, header.firstChild);
        }
    </script>
</body>
</html>