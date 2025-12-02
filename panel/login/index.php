<?php
include('../config.php'); // Dont remove it!
session_start();
session_regenerate_id();
require_once '../vendor/autoload.php';
use PHPGangsta_GoogleAuthenticator;

$ga = new PHPGangsta_GoogleAuthenticator();

if (isset($_POST['btnLogin'])) {
  $turnstileResponse = $_POST['cf-turnstile-response'];
  $secretKey = $website_config['turnstile_secret_key'];
  $url = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
  
  $data = ['secret' => $secretKey, 'response' => $turnstileResponse];
  $options = ['http' => ['method' => 'POST', 'header' => 'Content-type: application/x-www-form-urlencoded', 'content' => http_build_query($data)]];
  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);
  $responseKeys = json_decode($response, true);

  if (!$responseKeys['success']) {
    echo '<script>Swal.fire({icon: "error", title: "Information", text: "Captcha verification failed"});</script>';
    return;
  }

  include('../database.php');
  $username = $_POST['username'];
  $password = base64_encode($_POST['password']);

  $stmt = $conn->prepare("SELECT * FROM `users` WHERE `username` = ? AND `password` = ?");
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $keysc = $result->fetch_assoc();
    $secret = $keysc['2fa_secret'];
    $enabled = $keysc['2fa_enabled'];

    if ($enabled == 1) {
      $code = $_POST['2fa_code'];
      if ($ga->verifyCode($secret, $code, 2)) {
        $_SESSION["id"] = $keysc['userid'];
        $_SESSION["email"] = $keysc['email'];
        $_SESSION["username"] = $keysc['username'];
        $_SESSION["group"] = $keysc['usergroup'];
        $_SESSION["2fa"] = 1;
        echo "<script>window.location.href='https://" . $website_config['site_domain'] . "/'</script>";
      } else {
        echo '<script>Swal.fire({icon: "error", title: "Information", text: "2FA verification failed"});</script>';
      }
    } else {
      $_SESSION["id"] = $keysc['userid'];
      $_SESSION["email"] = $keysc['email'];
      $_SESSION["username"] = $keysc['username'];
      $_SESSION["group"] = $keysc['usergroup'];
      $_SESSION["2fa"] = 0;
      echo "<script>window.location.href='https://" . $website_config['site_domain'] . "/'</script>";
    }

    // Login logging and Discord webhook
    $date = date('Y-m-d H:i:s');
    $log_stmt = $conn->prepare("INSERT INTO `loginlogs` (`userid`, `date`, `id`) VALUES (?, ?, NULL)");
    $log_stmt->bind_param("is", $keysc['userid'], $date);
    $log_stmt->execute();

    $webhook_data = array(
      'username' => $website_config['site_name'] . ' - FiveM Anticheat',
      'avatar_url' => $website_config['site_logo'],
      'embeds' => array(array(
        'title' => 'New Login',
        'description' => "User **{$username}** has logged in.",
        'color' => hexdec('00ff00'),
        'fields' => array(
          array('name' => 'Email', 'value' => $_SESSION["email"]),
          array('name' => 'Date', 'value' => $date),
          array('name' => 'User-Agent', 'value' => $_SERVER['HTTP_USER_AGENT']),
          array('name' => 'IP', 'value' => $_SERVER["HTTP_CF_CONNECTING_IP"])
        ),
        'timestamp' => date('Y-m-d\TH:i:s\Z')
      ))
    );
    
    $curl = curl_init($website_config['discord_webhook_url']);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($webhook_data));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($curl);
    curl_close($curl);

  } else {
    echo '<script>Swal.fire({icon: "error", title: "Information", text: "Wrong Username / Password"});</script>';
    
    // Failed login webhook
    $webhook_data = array(
      'username' => $website_config['site_name'] . ' - FiveM Anticheat',
      'avatar_url' => $website_config['site_logo'],
      'embeds' => array(array(
        'title' => 'Failed Login',
        'description' => "User **{$username}** has failed to login.",
        'color' => hexdec('ff0000'),
        'fields' => array(
          array('name' => 'Date', 'value' => date('Y-m-d H:i:s')),
          array('name' => 'User-Agent', 'value' => $_SERVER['HTTP_USER_AGENT']),
          array('name' => 'IP', 'value' => $_SERVER["HTTP_CF_CONNECTING_IP"])
        ),
        'timestamp' => date('Y-m-d\TH:i:s\Z')
      ))
    );
    
    $curl = curl_init($website_config['discord_webhook_url']);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($webhook_data));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($curl);
    curl_close($curl);
  }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $website_config['site_description']; ?>">
    <meta name="keywords" content="<?php echo $website_config['site_keywords']; ?>">
    <meta name="author" content="<?php echo $website_config['site_author']; ?>">
    <link rel="icon" href="https://<?php echo $website_config['site_cdn_domain']; ?>/assets/images/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="https://<?php echo $website_config['site_cdn_domain']; ?>/assets/images/favicon.png" type="image/x-icon">
    <title><?php echo $website_config['site_name']; ?> | Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.24/sweetalert2.all.js"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        :root {
          --primary: #3b82f6;
          --primary-dark: #2563eb;
          --bg-dark: #0f172a;
          --bg-card: #1e293b;
          --text-primary: #f8fafc;
          --text-secondary: #94a3b8;
          --border: #334155;
          --success: #10b981;
        }
        
        * {
          box-sizing: border-box;
        }
        
        body {
          margin: 0;
          padding: 0;
          font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
          background: var(--bg-dark);
          min-height: 100vh;
          color: var(--text-primary);
          background-image: 
            linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
          background-size: 15px 15px;
        }
        
        .login-container {
          min-height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
          padding: 2rem;
        }
        
        .login-card {
          width: 100%;
          max-width: 440px;
          background: var(--bg-card);
          border-radius: 12px;
          border: 1px solid var(--border);
          box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .login-header {
          text-align: center;
          padding: 2.5rem 2rem 1.5rem;
          border-bottom: 1px solid var(--border);
        }
        
        .brand-container {
          display: flex;
          flex-direction: column;
          align-items: center;
          margin-bottom: 1.5rem;
        }
        
        .brand-logo {
          width: 56px;
          height: 56px;
          background: var(--primary);
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          margin-bottom: 1rem;
        }
        
        .brand-logo i {
          font-size: 24px;
          color: white;
        }
        
        .brand-name {
          font-size: 1.5rem;
          font-weight: 600;
          margin: 0 0 0.25rem;
        }
        
        .brand-subtitle {
          font-size: 0.875rem;
          color: var(--text-secondary);
          margin: 0;
        }
        
        .login-title {
          font-size: 1.25rem;
          font-weight: 600;
          margin: 0 0 0.5rem;
        }
        
        .login-subtitle {
          color: var(--text-secondary);
          font-size: 0.95rem;
          margin: 0;
        }
        
        .login-content {
          padding: 1.5rem 2rem 2.5rem;
        }
        
        .form-group {
          margin-bottom: 1.25rem;
        }
        
        .form-group label {
          display: block;
          color: var(--text-primary);
          font-size: 0.875rem;
          font-weight: 500;
          margin-bottom: 0.5rem;
        }
        
        .input-wrapper {
          position: relative;
        }
        
        .input-wrapper i {
          position: absolute;
          left: 12px;
          top: 50%;
          transform: translateY(-50%);
          color: var(--text-secondary);
          font-size: 16px;
          transition: color 0.2s;
        }
        
        .form-control {
          width: 100%;
          padding: 0.875rem 0.875rem 0.875rem 2.75rem;
          background: rgba(255, 255, 255, 0.05);
          border: 1px solid var(--border);
          border-radius: 8px;
          color: var(--text-primary);
          font-size: 0.95rem;
          font-family: inherit;
          transition: all 0.2s ease;
        }
        
        .form-control:focus {
          outline: none;
          border-color: var(--primary);
          box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
          background: rgba(255, 255, 255, 0.08);
        }
        
        .form-control:focus + i {
          color: var(--primary);
        }
        
        .form-control::placeholder {
          color: var(--text-secondary);
        }
        
        .password-toggle {
          right: 12px !important;
          left: auto !important;
          cursor: pointer;
          color: var(--text-secondary);
          z-index: 10;
        }
        
        .password-toggle:hover {
          color: var(--primary);
        }
        
        .twofa-field {
          display: none;
          animation: slideIn 0.3s ease-out;
        }
        
        .twofa-field.active {
          display: block;
        }
        
        @keyframes slideIn {
          from {
            opacity: 0;
            transform: translateY(-10px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
        
        .captcha-container {
          margin-bottom: 1.5rem;
          text-align: center;
        }
        
        .login-button {
          width: 100%;
          background: var(--primary);
          border: none;
          border-radius: 8px;
          padding: 1rem;
          font-family: inherit;
          font-size: 1rem;
          font-weight: 600;
          color: white;
          cursor: pointer;
          transition: all 0.2s ease;
          margin-bottom: 1.5rem;
        }
        
        .login-button:hover:not(:disabled) {
          background: var(--primary-dark);
          transform: translateY(-1px);
        }
        
        .login-button:disabled {
          opacity: 0.6;
          cursor: not-allowed;
        }
        
        .login-button.loading {
          position: relative;
          color: transparent;
        }
        
        .login-button.loading::after {
          content: '';
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          width: 20px;
          height: 20px;
          border: 2px solid rgba(255, 255, 255, 0.3);
          border-top: 2px solid white;
          border-radius: 50%;
          animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
          0% { transform: translate(-50%, -50%) rotate(0deg); }
          100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .security-badges {
          display: flex;
          justify-content: center;
          gap: 0.75rem;
          margin-bottom: 1.5rem;
        }
        
        .security-badge {
          display: flex;
          align-items: center;
          padding: 0.5rem 0.75rem;
          background: rgba(59, 130, 246, 0.1);
          border: 1px solid var(--border);
          border-radius: 6px;
          font-size: 0.75rem;
          font-weight: 500;
          color: var(--primary);
        }
        
        .security-badge i {
          margin-right: 0.5rem;
          font-size: 0.875rem;
        }
        
        .additional-links {
          text-align: center;
          margin-bottom: 1rem;
          font-size: 0.875rem;
        }
        
        .additional-links p {
          margin: 0;
          color: var(--text-secondary);
        }
        
        .footer-links {
          text-align: center;
          margin-bottom: 1rem;
          font-size: 0.8rem;
        }
        
        .separator {
          color: var(--text-secondary);
          margin: 0 0.5rem;
        }
        
        .login-footer {
          text-align: center;
          font-size: 0.8rem;
          color: var(--text-secondary);
        }
        
        .footer-link {
          color: var(--primary);
          text-decoration: none;
          font-weight: 500;
        }

        .footer-link:hover {
          color: var(--primary-dark);
        }

        /* Form validation states */
        .form-group.error .form-control {
          border-color: #ef4444;
        }
        
        .form-group.success .form-control {
          border-color: var(--success);
        }

        /* Check if user has 2FA enabled - show field dynamically */
        .twofa-required {
          display: block !important;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="brand-container">
                    <div class="brand-logo">
                        <i class="uil uil-shield-check"></i>
                    </div>
                    <div class="brand-text">
                        <h1 class="brand-name"><?php echo $website_config['site_name']; ?></h1>
                        <p class="brand-subtitle">Server Protection</p>
                    </div>
                </div>
                
                <h2 class="login-title">Panel</h2>
                <p class="login-subtitle">Sign in to manage your settings</p>
            </div>
            
            <div class="login-content">
                <form id="loginForm" method="post" novalidate>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-wrapper">
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   class="form-control" 
                                   placeholder="Enter your username"
                                   value=""
                                   required>
                            <i class="uil uil-user"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control" 
                                   placeholder="Enter your password"
                                   value=""
                                   required>
                            <i class="uil uil-lock"></i>
                            <i class="uil uil-eye password-toggle" id="togglePassword"></i>
                        </div>
                    </div>

                    <div class="form-group twofa-field" id="twofaField">
                        <label for="twofa_code">Two-Factor Authentication</label>
                        <div class="input-wrapper">
                            <input type="text" 
                                   id="twofa_code" 
                                   name="2fa_code" 
                                   class="form-control" 
                                   placeholder="Enter 6-digit code"
                                   maxlength="6"
                                   pattern="[0-9]{6}">
                            <i class="uil uil-shield-check"></i>
                        </div>
                    </div>

                    <div class="captcha-container">
                        <div class="cf-turnstile" data-sitekey="<?php echo $website_config['turnstile_site_key']; ?>"></div>
                    </div>

                    <button type="submit" name="btnLogin" class="login-button" id="loginButton">
                        <span>Sign In</span>
                    </button>
                </form>

                <div class="security-badges">
                    <div class="security-badge">
                        <i class="uil uil-lock"></i>
                        <span>Encrypted</span>
                    </div>
                    <div class="security-badge">
                        <i class="uil uil-shield-check"></i>
                        <span>Secure</span>
                    </div>
                </div>
                
                <div class="additional-links">
                    <p>Don't have an account? <a href="https://<?php echo $website_config['site_domain']; ?>/register" class="footer-link">Create Account</a></p>
                </div>
                
                <div class="footer-links">
                    <a href="https://<?php echo $website_config['site_domain']; ?>/tos" class="footer-link">Terms</a>
                    <span class="separator">•</span>
                    <a href="https://<?php echo $website_config['site_domain']; ?>/privacy" class="footer-link">Privacy</a>
                    <span class="separator">•</span>
                    <a href="https://<?php echo $website_config['site_domain']; ?>/refund" class="footer-link">Refund</a>
                </div>
                
                <div class="login-footer">
                    <p>© <?php echo $website_config['site_name']; ?> <script>document.write(new Date().getFullYear())</script></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                this.classList.toggle('uil-eye');
                this.classList.toggle('uil-eye-slash');
            });
        }

        // Form submission - let PHP handle the processing
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');
        const usernameField = document.getElementById('username');
        const twofaField = document.getElementById('twofaField');

        // For demo purposes - in real implementation, you'd check via AJAX/PHP
        // Show 2FA field for admin user (you can remove this and handle it server-side)
        if (usernameField) {
            usernameField.addEventListener('blur', function() {
                // This would normally be handled server-side
                // For demo: show 2FA field for username "admin"
                if (this.value.toLowerCase() === 'admin') {
                    twofaField.classList.add('active');
                } else {
                    twofaField.classList.remove('active');
                }
            });
        }

        // Input validation styling
        const inputs = document.querySelectorAll('.form-control');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateInput(this);
            });

            input.addEventListener('focus', function() {
                this.closest('.form-group').classList.remove('error', 'success');
            });
        });

        function validateInput(input) {
            const formGroup = input.closest('.form-group');
            
            if (input.checkValidity() && input.value.trim() !== '') {
                formGroup.classList.add('success');
                formGroup.classList.remove('error');
            } else if (input.value.trim() !== '') {
                formGroup.classList.add('error');
                formGroup.classList.remove('success');
            }
        }

        // Prevent right-click and text selection (as in original code)
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
        });
    </script>
</body>
</html>