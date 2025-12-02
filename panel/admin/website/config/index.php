<?php
session_start();
include('../../../func.php');
include('../../../config.php');
$site_name = $website_config["site_name"];

if (!isAdmin()) {
  header("Location: " . $website_config['site_url'] . "/");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $config_file = dirname(dirname(dirname(__DIR__))) . '/config.php';
 
    $config_content = "<?php\n\n";
    
    // License configuration
    $config_content .= "// License configuration section\n";
    $config_content .= "// Contains the license key for Auth-Solutions authentication ( For DMCA Protection - cp.auth-solutions.de )\n";
    $config_content .= "\$license_config = [\n";
    $config_content .= "    'license_key' => '" . addslashes($_POST['license_config_license_key']) . "' // The unique license key for the Panel\n";
    $config_content .= "];\n\n";
    
    // Download configuration
    $config_content .= "// Download configuration section\n";
    $config_content .= "// Controls the file download settings and paths for the anticheat system\n";
    $config_content .= "\$download_config = [\n";
    $config_content .= "    'base_path' => dirname(__FILE__) . '/api/download/[Anticheat]',         // Base directory path for anticheat files\n";
    $config_content .= "    'config_path' => dirname(__FILE__) . '/api/download/[Anticheat]/FiveSecurity/config/Config.lua', // Path to the configuration file\n";
    $config_content .= "    'zip_filename' => '" . addslashes($_POST['download_config_zip_filename']) . "',                                           // Name of the zip file for downloads\n";
    $config_content .= "    'temp_prefix' => '" . addslashes($_POST['download_config_temp_prefix']) . "',                                          // Prefix for temporary files\n";
    $config_content .= "    'config_file_path' => '" . addslashes($_POST['download_config_config_file_path']) . "',       // Relative path to the configuration file\n";
    $config_content .= "    'license_key_name' => '" . addslashes($_POST['download_config_license_key_name']) . "',                          // Variable name for the license key in config\n";
    $config_content .= "    'headers' => [                                                          // HTTP headers for file downloads\n";
    $config_content .= "        'content_type' => 'application/zip',                                // Content type for zip files\n";
    $config_content .= "        'cache_control' => 'no-store, no-cache, must-revalidate, max-age=0', // Cache control settings\n";
    $config_content .= "        'pragma' => 'no-cache',                                            // Additional cache control\n";
    $config_content .= "        'expires' => '0'                                                    // Expiration time for cache\n";
    $config_content .= "    ]\n";
    $config_content .= "];\n\n";
    
    // Database configuration
    $config_content .= "// Database configuration section\n";
    $config_content .= "// Contains all database connection settings and database names\n";
    $config_content .= "\$database_config = [\n";
    $config_content .= "    'host' => '" . addslashes($_POST['database_config_host']) . "',                    // Database server hostname\n";
    $config_content .= "    'username' => '" . addslashes($_POST['database_config_username']) . "',                     // Database username\n";
    $config_content .= "    'password' => '" . addslashes($_POST['database_config_password']) . "',                 // Database password\n";
    $config_content .= "    'databases' => [                          // List of different databases used by the system\n";
    $config_content .= "        'panel' => '" . addslashes($_POST['database_config_databases_panel']) . "',                   // Database for panel functionality\n";
    $config_content .= "        'counter' => '" . addslashes($_POST['database_config_databases_counter']) . "',               // Database for counting/statistics\n";
    $config_content .= "        'logs' => '" . addslashes($_POST['database_config_databases_logs']) . "',                     // Database for storing logs\n";
    $config_content .= "        'serverbans' => '" . addslashes($_POST['database_config_databases_serverbans']) . "',         // Database for server bans\n";
    $config_content .= "        'auth' => '" . addslashes($_POST['database_config_databases_auth']) . "'                      // Database for authentication\n";
    $config_content .= "    ]\n";
    $config_content .= "];\n\n";
    
    // Website configuration
    $config_content .= "// Website configuration section\n";
    $config_content .= "// Contains all website-related settings and integrations\n";
    $config_content .= "\$website_config = [\n";
    $config_content .= "    // Main website settings\n";
    $config_content .= "    'site_name' => '" . addslashes($_POST['website_config_site_name']) . "',              // Name of the website\n";
    $config_content .= "    'site_logo' => '" . addslashes($_POST['website_config_site_logo']) . "', // URL to the site logo\n";
    $config_content .= "    'site_domain' => '" . addslashes($_POST['website_config_site_domain']) . "',           // Main domain of the website\n";
    $config_content .= "    'site_cdn_domain' => '" . addslashes($_POST['website_config_site_cdn_domain']) . "', // CDN domain for static content\n";
    $config_content .= "    'site_description' => '" . addslashes($_POST['website_config_site_description']) . "', // Meta description for SEO\n";
    $config_content .= "    'site_keywords' => '" . addslashes($_POST['website_config_site_keywords']) . "', // Meta keywords for SEO\n";
    $config_content .= "    'site_author' => '" . addslashes($_POST['website_config_site_author']) . "',                // Author of the website\n";
    $config_content .= "    'site_favicon' => '" . addslashes($_POST['website_config_site_favicon']) . "', // Path to the favicon\n";
    $config_content .= "    'site_copyright' => '" . addslashes($_POST['website_config_site_copyright']) . "',    // Copyright information\n\n";
    
    $config_content .= "    // Cloudflare Turnstile settings for bot protection  - https://dash.cloudflare.com/\n";
    $config_content .= "    'turnstile_site_key' => '" . addslashes($_POST['website_config_turnstile_site_key']) . "',     // Public key for Turnstile integration\n";
    $config_content .= "    'turnstile_secret_key' => '" . addslashes($_POST['website_config_turnstile_secret_key']) . "', // Secret key for Turnstile integration\n\n";
    
    $config_content .= "    // Discord integration settings - https://discord.com/developers/applications\n";
    $config_content .= "    'discord_client_id' => '" . addslashes($_POST['website_config_discord_client_id']) . "',               // Discord application client ID\n";
    $config_content .= "    'discord_client_secret' => '" . addslashes($_POST['website_config_discord_client_secret']) . "',           // Discord application client secret\n";
    $config_content .= "    'discord_redirect_uri' => '" . addslashes($_POST['website_config_discord_redirect_uri']) . "', // OAuth2 redirect URI\n";
    $config_content .= "    'discord_bot_token' => '" . addslashes($_POST['website_config_discord_bot_token']) . "',               // Discord bot token for bot functionality\n";
    $config_content .= "    'discord_webhook_url' => '" . addslashes($_POST['website_config_discord_webhook_url']) . "',             // Discord webhook URL for notifications\n\n";
    
    $config_content .= "    // Additional URLs\n";
    $config_content .= "    'docs_url' => '" . addslashes($_POST['website_config_docs_url']) . "',  // Documentation URL\n";
    $config_content .= "    'discord_url' => '" . addslashes($_POST['website_config_discord_url']) . "', // Discord server invite URL\n";
    $config_content .= "];\n";
    
    if (file_put_contents($config_file, $config_content) === false) {
        $error_message = "Error saving configuration!";
    } else {
        $success_message = "All configurations updated successfully!";
        echo "<script>
            setTimeout(function() { 
                window.location.href = '" . $_SERVER['PHP_SELF'] . "'; 
            }, 2000);
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $website_config['site_name']; ?> | Config Editor</title>
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
          flex: 1;
        }

        /* Config Grid */
        .config-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
          gap: 1.5rem;
          margin-bottom: 2rem;
        }

        .config-card {
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 12px;
          overflow: hidden;
          transition: all 0.2s ease;
        }

        .config-card:hover {
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

        .form-control:hover {
          border-color: rgba(59, 130, 246, 0.5);
        }

        textarea.form-control {
          min-height: 100px;
          resize: vertical;
        }

        /* Specific card colors */
        .database-card .card-header { background: rgba(16, 185, 129, 0.1); }
        .database-card .card-title i { color: var(--success); }

        .discord-card .card-header { background: rgba(91, 101, 242, 0.1); }
        .discord-card .card-title i { color: #5b65f2; }

        .security-card .card-header { background: rgba(245, 158, 11, 0.1); }
        .security-card .card-title i { color: var(--warning); }

        .seo-card .card-header { background: rgba(6, 182, 212, 0.1); }
        .seo-card .card-title i { color: var(--info); }

        /* Action Bar */
        .action-bar {
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 12px;
          padding: 1.5rem;
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-top: 2rem;
        }

        .action-info {
          display: flex;
          align-items: center;
          gap: 1rem;
        }

        .action-icon {
          width: 50px;
          height: 50px;
          background: rgba(59, 130, 246, 0.1);
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 24px;
          color: var(--primary);
        }

        .action-text h3 {
          font-size: 1.125rem;
          font-weight: 600;
          margin-bottom: 0.25rem;
          color: var(--text-primary);
        }

        .action-text p {
          font-size: 0.875rem;
          color: var(--text-secondary);
          margin: 0;
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
          transform: translateY(-1px);
          box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        /* Alert Messages */
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

          .config-grid {
            grid-template-columns: 1fr;
          }

          .action-bar {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
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
                        <a href="/admin/website/settings" class="nav-link">
                            <i class="uil uil-setting"></i>
                            <span>Settings</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/admin/website/config" class="nav-link active">
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
                <div class="header-title">Configuration Editor</div>
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

                <form method="POST" action="">
                    <div class="config-grid">
                        <!-- Database Configuration -->
                        <div class="config-card database-card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="uil uil-database"></i>
                                    Database Configuration
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">Host</label>
                                    <input type="text" class="form-control" name="database_config_host" value="<?php echo htmlspecialchars($database_config['host']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="database_config_username" value="<?php echo htmlspecialchars($database_config['username']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="database_config_password" value="<?php echo htmlspecialchars($database_config['password']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Panel Database</label>
                                    <input type="text" class="form-control" name="database_config_databases_panel" value="<?php echo htmlspecialchars($database_config['databases']['panel']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Counter Database</label>
                                    <input type="text" class="form-control" name="database_config_databases_counter" value="<?php echo htmlspecialchars($database_config['databases']['counter']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Logs Database</label>
                                    <input type="text" class="form-control" name="database_config_databases_logs" value="<?php echo htmlspecialchars($database_config['databases']['logs']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Serverbans Database</label>
                                    <input type="text" class="form-control" name="database_config_databases_serverbans" value="<?php echo htmlspecialchars($database_config['databases']['serverbans']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Auth Database</label>
                                    <input type="text" class="form-control" name="database_config_databases_auth" value="<?php echo htmlspecialchars($database_config['databases']['auth']); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Website Configuration -->
                        <div class="config-card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="uil uil-globe"></i>
                                    Website Settings
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">Site Name</label>
                                    <input type="text" class="form-control" name="website_config_site_name" value="<?php echo htmlspecialchars($website_config['site_name']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Site Logo</label>
                                    <input type="text" class="form-control" name="website_config_site_logo" value="<?php echo htmlspecialchars($website_config['site_logo']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Site Domain</label>
                                    <input type="text" class="form-control" name="website_config_site_domain" value="<?php echo htmlspecialchars($website_config['site_domain']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">CDN Domain</label>
                                    <input type="text" class="form-control" name="website_config_site_cdn_domain" value="<?php echo htmlspecialchars($website_config['site_cdn_domain']); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Discord Integration -->
                        <div class="config-card discord-card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="uil uil-discord"></i>
                                    Discord Integration
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">Client ID</label>
                                    <input type="text" class="form-control" name="website_config_discord_client_id" value="<?php echo htmlspecialchars($website_config['discord_client_id']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Client Secret</label>
                                    <input type="password" class="form-control" name="website_config_discord_client_secret" value="<?php echo htmlspecialchars($website_config['discord_client_secret']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Redirect URI</label>
                                    <input type="text" class="form-control" name="website_config_discord_redirect_uri" value="<?php echo htmlspecialchars($website_config['discord_redirect_uri']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Bot Token</label>
                                    <input type="password" class="form-control" name="website_config_discord_bot_token" value="<?php echo htmlspecialchars($website_config['discord_bot_token']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Webhook URL</label>
                                    <input type="text" class="form-control" name="website_config_discord_webhook_url" value="<?php echo htmlspecialchars($website_config['discord_webhook_url']); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div class="config-card security-card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="uil uil-shield"></i>
                                    Security & Protection
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">License Key</label>
                                    <input type="text" class="form-control" name="license_config_license_key" value="<?php echo htmlspecialchars($license_config['license_key']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Turnstile Site Key</label>
                                    <input type="text" class="form-control" name="website_config_turnstile_site_key" value="<?php echo htmlspecialchars($website_config['turnstile_site_key']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Turnstile Secret Key</label>
                                    <input type="password" class="form-control" name="website_config_turnstile_secret_key" value="<?php echo htmlspecialchars($website_config['turnstile_secret_key']); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="config-card seo-card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="uil uil-search"></i>
                                    SEO & Meta Information
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">Site Description</label>
                                    <textarea class="form-control" name="website_config_site_description" rows="3"><?php echo htmlspecialchars($website_config['site_description']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Site Keywords</label>
                                    <input type="text" class="form-control" name="website_config_site_keywords" value="<?php echo htmlspecialchars($website_config['site_keywords']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Site Author</label>
                                    <input type="text" class="form-control" name="website_config_site_author" value="<?php echo htmlspecialchars($website_config['site_author']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Site Favicon</label>
                                    <input type="text" class="form-control" name="website_config_site_favicon" value="<?php echo htmlspecialchars($website_config['site_favicon']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Copyright Text</label>
                                    <input type="text" class="form-control" name="website_config_site_copyright" value="<?php echo htmlspecialchars($website_config['site_copyright']); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Download Configuration -->
                        <div class="config-card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="uil uil-download-alt"></i>
                                    Download Configuration
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">ZIP Filename</label>
                                    <input type="text" class="form-control" name="download_config_zip_filename" value="<?php echo htmlspecialchars($download_config['zip_filename']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Temp Prefix</label>
                                    <input type="text" class="form-control" name="download_config_temp_prefix" value="<?php echo htmlspecialchars($download_config['temp_prefix']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Config File Path</label>
                                    <input type="text" class="form-control" name="download_config_config_file_path" value="<?php echo htmlspecialchars($download_config['config_file_path']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">License Key Name</label>
                                    <input type="text" class="form-control" name="download_config_license_key_name" value="<?php echo htmlspecialchars($download_config['license_key_name']); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Additional URLs -->
                        <div class="config-card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="uil uil-link"></i>
                                    External Links
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">Documentation URL</label>
                                    <input type="text" class="form-control" name="website_config_docs_url" value="<?php echo htmlspecialchars($website_config['docs_url']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Discord Invite URL</label>
                                    <input type="text" class="form-control" name="website_config_discord_url" value="<?php echo htmlspecialchars($website_config['discord_url']); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="action-bar">
                        <div class="action-info">
                            <div class="action-icon">
                                <i class="uil uil-save"></i>
                            </div>
                            <div class="action-text">
                                <h3>Save Configuration</h3>
                                <p>Apply all changes to the system configuration</p>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="uil uil-check"></i>
                            Save All Changes
                        </button>
                    </div>
                </form>
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

        // Form validation and auto-save draft (optional)
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input, textarea');
        
        // Add visual feedback for form changes
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = 'var(--warning)';
                setTimeout(() => {
                    this.style.borderColor = '';
                }, 1000);
            });
        });

        // Show confirmation before leaving page with unsaved changes
        let formChanged = false;
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                formChanged = true;
            });
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        form.addEventListener('submit', function() {
            formChanged = false;
        });
    </script>
</body>
</html>