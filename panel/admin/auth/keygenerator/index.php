<?php
session_start();
include('../../../func.php');
$site_name = $website_config["site_name"];

if (!isAdmin()) {
  header("Location: " . $website_config['site_domain'] . "/");
  exit;
}

include('../../../database.php');

$created_keys = array();
$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    
    if (isset($_POST['key_type'])) {
        $key_type = $_POST['key_type'];
        $key_prefix = $site_name . "_";
        $current_date = date('Y-m-d H:i:s');

        switch($key_type) {
            case "normal":
                $amount = (isset($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0 && $_POST['amount'] < 150) ? $_POST['amount'] : 1;
                $key_length = (isset($_POST['key_length']) && is_numeric($_POST['key_length']) && $_POST['key_length'] > 0 && $_POST['key_length'] < 150) ? $_POST['key_length'] : 14;

                if (!isset($_POST['duration'])) {
                    $error_message = "Duration must be specified";
                    break;
                }

                $duration = $_POST['duration'];
                for ($i = 0; $i < $amount; $i++) {
                    $key = $key_prefix;
                    for ($j = 0; $j < $key_length; $j++) {
                        $key .= $chars[rand(0, strlen($chars) - 1)];
                    }
                    $created_keys[] = $key;
                    
                    $stmt = $conn->prepare("INSERT INTO `keys` (license, exp) VALUES (?, ?)");
                    $stmt->bind_param("ss", $key, $duration);
                    if (!$stmt->execute()) {
                        $error_message = "Database error: " . $stmt->error;
                        break;
                    }
                    $stmt->close();
                }
                
                if (empty($error_message)) {
                    $success_message = "Successfully generated " . count($created_keys) . " key(s)";
                }
                break;

            case "special":
                if (!isset($_POST['special_duration'])) {
                    $error_message = "Special duration must be specified";
                    break;
                }

                $special_type = $_POST['special_duration'];
                $key = $key_prefix . "SPECIAL_" . strtoupper($special_type) . "_" . substr(md5(uniqid()), 0, 8);
                $duration = ($special_type === 'partner') ? 'partner' : '1 day';
                
                $stmt = $conn->prepare("INSERT INTO `keys` (license, exp) VALUES (?, ?)");
                $stmt->bind_param("ss", $key, $duration);
                if ($stmt->execute()) {
                    $created_keys[] = $key;
                    $success_message = "Successfully generated special key";
                } else {
                    $error_message = "Database error: " . $stmt->error;
                }
                $stmt->close();
                break;

            default:
                $error_message = "Invalid key type";
                break;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $website_config['site_name']; ?> | Key Generator</title>
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

        .generator-grid {
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 2rem;
          max-width: 1200px;
        }

        .generator-card {
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 12px;
          overflow: hidden;
          transition: all 0.2s ease;
        }

        .generator-card:hover {
          transform: translateY(-2px);
          box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .card-header {
          padding: 1.5rem;
          border-bottom: 1px solid var(--border);
          background: rgba(59, 130, 246, 0.1);
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

        .card-body {
          padding: 1.5rem;
        }

        .form-group {
          margin-bottom: 1rem;
        }

        .form-group:last-child {
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
          box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-success {
          background: var(--success);
          color: white;
        }

        .btn-success:hover {
          background: #059669;
        }

        .key-type-selector {
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 1rem;
          margin-bottom: 1.5rem;
        }

        .key-type-option {
          padding: 1rem;
          background: rgba(255, 255, 255, 0.05);
          border: 2px solid var(--border);
          border-radius: 8px;
          cursor: pointer;
          transition: all 0.2s ease;
          text-align: center;
        }

        .key-type-option:hover {
          border-color: var(--primary);
          background: rgba(59, 130, 246, 0.1);
        }

        .key-type-option.active {
          border-color: var(--primary);
          background: rgba(59, 130, 246, 0.2);
        }

        .key-type-icon {
          font-size: 2rem;
          margin-bottom: 0.5rem;
          color: var(--primary);
        }

        .key-type-title {
          font-size: 1rem;
          font-weight: 600;
          margin-bottom: 0.25rem;
          color: var(--text-primary);
        }

        .key-type-desc {
          font-size: 0.75rem;
          color: var(--text-secondary);
        }

        .generated-keys {
          grid-column: 1 / -1;
          margin-top: 2rem;
        }

        .key-list {
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 8px;
          padding: 1rem;
          max-height: 300px;
          overflow-y: auto;
        }

        .key-item {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 0.75rem;
          background: rgba(255, 255, 255, 0.05);
          border-radius: 6px;
          margin-bottom: 0.5rem;
          font-family: monospace;
          font-size: 0.875rem;
        }

        .key-item:last-child {
          margin-bottom: 0;
        }

        .copy-btn {
          padding: 0.25rem 0.5rem;
          background: var(--primary);
          color: white;
          border: none;
          border-radius: 4px;
          font-size: 0.75rem;
          cursor: pointer;
        }

        .copy-btn:hover {
          background: var(--primary-dark);
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

        .form-section {
          display: none;
        }

        .form-section.active {
          display: block;
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

          .generator-grid {
            grid-template-columns: 1fr;
          }

          .key-type-selector {
            grid-template-columns: 1fr;
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
                        <a href="/admin/auth/keygenerator" class="nav-link active">
                            <i class="uil uil-plus-circle"></i>
                            <span>Key Generator</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/admin/website/settings" class="nav-link">
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

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-title">Key Generator</div>
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
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <i class="uil uil-check-circle"></i>
                    <?php echo $success_message; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="uil uil-times-circle"></i>
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>

                <div class="generator-grid">
                    <!-- Key Generator Form -->
                    <div class="generator-card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="uil uil-key-skeleton"></i>
                                Generate License Keys
                            </h4>
                        </div>
                        <div class="card-body">
                            <form method="post" action="">
                                <!-- Key Type Selector -->
                                <div class="key-type-selector">
                                    <div class="key-type-option active" data-type="normal">
                                        <div class="key-type-icon">
                                            <i class="uil uil-key-skeleton"></i>
                                        </div>
                                        <div class="key-type-title">Normal Keys</div>
                                        <div class="key-type-desc">Standard license keys with duration</div>
                                    </div>
                                    <div class="key-type-option" data-type="special">
                                        <div class="key-type-icon">
                                            <i class="uil uil-star"></i>
                                        </div>
                                        <div class="key-type-title">Special Keys</div>
                                        <div class="key-type-desc">Partner and test phase keys</div>
                                    </div>
                                </div>

                                <input type="hidden" name="key_type" id="key_type" value="normal">

                                <!-- Normal Key Section -->
                                <div class="form-section active" id="normal-section">
                                    <div class="form-group">
                                        <label class="form-label">Amount</label>
                                        <input type="number" class="form-control" name="amount" min="1" max="149" value="1" placeholder="Number of keys to generate">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Key Length</label>
                                        <input type="number" class="form-control" name="key_length" min="8" max="32" value="14" placeholder="Length of each key">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Duration</label>
                                        <select class="form-control" name="duration">
                                            <option value="1 day">1 Day</option>
                                            <option value="3 day">3 Days</option>
                                            <option value="14 day">2 Weeks</option>
                                            <option value="1 month" selected>1 Month</option>
                                            <option value="3 month">3 Months</option>
                                            <option value="lifetime">Lifetime</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Special Key Section -->
                                <div class="form-section" id="special-section">
                                    <div class="form-group">
                                        <label class="form-label">Special Type</label>
                                        <select class="form-control" name="special_duration">
                                            <option value="partner">Partner Key</option>
                                            <option value="testphase">Test Phase Key</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="uil uil-plus"></i>
                                        Generate Keys
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Key Preview -->
                    <div class="generator-card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="uil uil-clipboard-notes"></i>
                                Key Preview
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($created_keys)): ?>
                                <div class="key-list">
                                    <?php foreach ($created_keys as $key): ?>
                                        <div class="key-item">
                                            <span class="key-value"><?php echo htmlspecialchars($key); ?></span>
                                            <button class="copy-btn" onclick="copyToClipboard('<?php echo htmlspecialchars($key); ?>')">
                                                Copy
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div style="margin-top: 1rem;">
                                    <button class="btn btn-success" onclick="copyAllKeys()">
                                        <i class="uil uil-copy"></i>
                                        Copy All Keys
                                    </button>
                                </div>
                            <?php else: ?>
                                <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                    <i class="uil uil-key-skeleton" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                                    <p>Generated keys will appear here</p>
                                </div>
                            <?php endif; ?>
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

        // Key type selection
        document.querySelectorAll('.key-type-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                document.querySelectorAll('.key-type-option').forEach(opt => opt.classList.remove('active'));
                document.querySelectorAll('.form-section').forEach(section => section.classList.remove('active'));
                
                // Add active class to clicked option
                this.classList.add('active');
                
                // Get the type and update hidden input
                const type = this.dataset.type;
                document.getElementById('key_type').value = type;
                
                // Show corresponding section
                document.getElementById(type + '-section').classList.add('active');
            });
        });

        // Copy functions
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Key copied to clipboard',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Key copied to clipboard',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        }

        function copyAllKeys() {
            const keys = [];
            document.querySelectorAll('.key-value').forEach(keyElement => {
                keys.push(keyElement.textContent);
            });
            
            const allKeysText = keys.join('\n');
            
            navigator.clipboard.writeText(allKeysText).then(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'All Keys Copied!',
                    text: `${keys.length} keys copied to clipboard`,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = allKeysText;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                Swal.fire({
                    icon: 'success',
                    title: 'All Keys Copied!',
                    text: `${keys.length} keys copied to clipboard`,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const keyType = document.getElementById('key_type').value;
            
            if (keyType === 'normal') {
                const amount = document.querySelector('input[name="amount"]').value;
                const keyLength = document.querySelector('input[name="key_length"]').value;
                
                if (!amount || amount < 1 || amount > 149) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Amount',
                        text: 'Amount must be between 1 and 149'
                    });
                    return;
                }
                
                if (!keyLength || keyLength < 8 || keyLength > 32) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Key Length',
                        text: 'Key length must be between 8 and 32 characters'
                    });
                    return;
                }
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="uil uil-spinner-alt" style="animation: spin 1s linear infinite;"></i> Generating...';
            submitBtn.disabled = true;
            
            // Re-enable after form submission (in case of validation errors)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });

        // Add spin animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);

        // Auto-focus on amount input when normal key type is selected
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('.key-type-option[data-type="normal"]').classList.contains('active')) {
                document.querySelector('input[name="amount"]').focus();
            }
        });

        // Add tooltips for better UX
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', function() {
                const value = parseInt(this.value);
                const min = parseInt(this.min);
                const max = parseInt(this.max);
                
                if (value < min || value > max) {
                    this.style.borderColor = 'var(--danger)';
                } else {
                    this.style.borderColor = 'var(--success)';
                    setTimeout(() => {
                        this.style.borderColor = '';
                    }, 1000);
                }
            });
        });

        // Animate generated keys
        if (document.querySelectorAll('.key-item').length > 0) {
            document.querySelectorAll('.key-item').forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                item.style.transition = 'all 0.3s ease';
                
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });
        }
    </script>
</body>
</html>