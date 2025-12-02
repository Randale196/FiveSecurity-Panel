<?php
session_start();
include('database.php'); // Dont remove it!
include('config.php'); // Dont remove it!

if (!isset($_SESSION['id']) || !isset($_SESSION['group'])) {
    session_destroy();
    header("Location: https://" . $website_config['site_domain'] . "/login");
    exit;
}

function is_maintenance(): bool
{
    global $link;

    $query = "SELECT maintenance FROM `system` WHERE maintenance = 1 LIMIT 1";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    return mysqli_stmt_num_rows($stmt) > 0;
}

if (is_maintenance() && $_SESSION["group"] !== "admin") {
    header('Location: https://' . $website_config['site_domain'] . '/maintenance.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $website_config['site_name']; ?> - Banned</title>
    <link rel="icon" type="image/x-icon" href="<?php echo $website_config['site_favicon']; ?>">
    <meta name="description" content="<?php echo $website_config['site_description']; ?>">
    <meta name="keywords" content="<?php echo $website_config['site_keywords']; ?>">
    <meta name="author" content="<?php echo $website_config['site_author']; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <style>
        :root {
          --primary: #3b82f6;
          --bg-dark: #0f172a;
          --bg-card: #1e293b;
          --bg-sidebar: #111827;
          --text-primary: #f8fafc;
          --text-secondary: #94a3b8;
          --border: #334155;
          --error-red: #ef4444;
          --error-bg: rgba(239, 68, 68, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
          font-family: 'Inter', sans-serif;
          background: var(--bg-dark);
          color: var(--text-primary);
          line-height: 1.6;
          min-height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
        }

        .banned-container {
          max-width: 600px;
          width: 90%;
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 12px;
          padding: 3rem 2rem;
          text-align: center;
          box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .banned-icon {
          width: 80px;
          height: 80px;
          background: var(--error-bg);
          border: 2px solid var(--error-red);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 0 auto 2rem;
          animation: pulse 2s infinite;
        }

        .banned-icon i {
          font-size: 2.5rem;
          color: var(--error-red);
        }

        @keyframes pulse {
          0% { transform: scale(1); }
          50% { transform: scale(1.05); }
          100% { transform: scale(1); }
        }

        .banned-title {
          font-size: 2rem;
          font-weight: 700;
          color: var(--error-red);
          margin-bottom: 1rem;
        }

        .banned-message {
          font-size: 1.1rem;
          color: var(--text-secondary);
          margin-bottom: 2rem;
          line-height: 1.6;
        }

        .banned-details {
          background: var(--error-bg);
          border: 1px solid var(--error-red);
          border-radius: 8px;
          padding: 1.5rem;
          margin-bottom: 2rem;
        }

        .banned-details h3 {
          color: var(--error-red);
          font-size: 1.2rem;
          font-weight: 600;
          margin-bottom: 1rem;
        }

        .banned-details p {
          color: #fecaca;
          margin-bottom: 0.5rem;
        }

        .contact-info {
          background: var(--bg-sidebar);
          border: 1px solid var(--border);
          border-radius: 8px;
          padding: 1.5rem;
          margin-bottom: 2rem;
        }

        .contact-info h4 {
          color: var(--text-primary);
          font-size: 1.1rem;
          font-weight: 600;
          margin-bottom: 1rem;
        }

        .contact-info p {
          color: var(--text-secondary);
          margin-bottom: 1rem;
        }

        .contact-link {
          color: var(--primary);
          text-decoration: none;
          font-weight: 500;
          transition: all 0.2s ease;
        }

        .contact-link:hover {
          text-decoration: underline;
          color: #60a5fa;
        }

        .back-button {
          display: inline-flex;
          align-items: center;
          gap: 0.5rem;
          background: var(--primary);
          color: white;
          padding: 0.75rem 1.5rem;
          border: none;
          border-radius: 8px;
          text-decoration: none;
          font-weight: 500;
          transition: all 0.2s ease;
          cursor: pointer;
        }

        .back-button:hover {
          background: #2563eb;
          transform: translateY(-1px);
        }

        .logout-button {
          display: inline-flex;
          align-items: center;
          gap: 0.5rem;
          background: var(--error-red);
          color: white;
          padding: 0.75rem 1.5rem;
          border: none;
          border-radius: 8px;
          text-decoration: none;
          font-weight: 500;
          transition: all 0.2s ease;
          cursor: pointer;
          margin-left: 1rem;
        }

        .logout-button:hover {
          background: #dc2626;
          transform: translateY(-1px);
        }

        .site-logo {
          width: 60px;
          height: 60px;
          background: var(--primary);
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 0 auto 1rem;
          color: white;
          font-weight: 700;
          font-size: 1.5rem;
        }

        .site-name {
          font-size: 1.5rem;
          font-weight: 600;
          color: var(--text-primary);
          margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
          .banned-container {
            padding: 2rem 1.5rem;
          }
          
          .banned-title {
            font-size: 1.5rem;
          }
          
          .banned-message {
            font-size: 1rem;
          }
          
          .back-button, .logout-button {
            display: block;
            margin: 0.5rem auto;
            width: 100%;
            text-align: center;
          }
        }
    </style>
</head>
<body>
    <div class="banned-container">
        <div class="site-logo">FS</div>
        <div class="site-name"><?php echo $website_config['site_name']; ?></div>
        
        <div class="banned-icon">
            <i class="uil uil-ban"></i>
        </div>
        
        <h1 class="banned-title">Account Permanently Banned</h1>
        
        <p class="banned-message">
            You are permanently banned from the <?php echo $website_config['site_name']; ?> panel.
            Your access to all services has been revoked.
        </p>
        
        <div class="banned-details">
            <h3>Ban Details</h3>
            <p><strong>Status:</strong> Permanently Banned</p>
            <p><strong>Reason:</strong> Terms of Service Violation</p>
            <p><strong>Appeal:</strong> This ban is permanent and cannot be appealed</p>
        </div>
        
        <div class="contact-info">
            <h4>Need Help?</h4>
            <p>If you believe this is an error, please contact our support team:</p>
            <p>
                <i class="uil uil-envelope"></i> 
                <a href="mailto:contact@FiveSecurity.net" class="contact-link">contact@FiveSecurity.net</a>
            </p>
            <p>
                <i class="uil uil-discord"></i> 
                <a href="https://<?php echo $website_config['site_domain']; ?>/discord" class="contact-link" target="_blank">Discord Support</a>
            </p>
        </div>
        
        <div>
            <a href="https://<?php echo $website_config['site_domain']; ?>" class="back-button">
                <i class="uil uil-estate"></i>
                Back to Homepage
            </a>
            <a href="https://<?php echo $website_config['site_domain']; ?>/logout.php" class="logout-button">
                <i class="uil uil-signout"></i>
                Logout
            </a>
        </div>
    </div>
</body>
</html>