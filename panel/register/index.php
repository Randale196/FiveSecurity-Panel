<?php
include('../config.php'); // Dont remove it!
include('../license.php'); // Dont remove it!

session_start();

if (isset($_POST['btnRegister'])) {
  include('../database.php');

  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $key = mysqli_real_escape_string($conn, $_POST['license']);
  $password = base64_encode($_POST['password']);

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Information',
        text: 'Email already registered'
      });
    </script>
    <?php
  } else {
    if (!empty($username) && !empty($key) && !empty($email) && !empty($password)) {
      $stmt = $conn->prepare("SELECT * FROM `keys` WHERE license = ?");
      $stmt->bind_param("s", $key);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
          ?>
          <script>
            Swal.fire({
              icon: 'error',
              title: 'Information',
              text: 'Username already exists'
            });
          </script>
          <?php
        } else {
          ini_set('display_errors', '1');
          ini_set('display_startup_errors', '1');

          $date = date('d/m/y H:i:s');

          $stmt = $conn->prepare("SELECT * FROM `keys` WHERE license = ?");
          $stmt->bind_param("s", $key);
          $stmt->execute();
          $result = $stmt->get_result();
          $lc = $result->fetch_array();

          $stmt = $conn->prepare("INSERT INTO `users` (`username`, `email`, `password`, `created_since`) VALUES (?, ?, ?, ?)");
          $stmt->bind_param("ssss", $username, $email, $password, $date);
          $stmt->execute();

          $licenseid = $lc['licenseid'];
          $expire = $lc['exp'];
          $tp = null;

          $tp = new DateTime();
          if ($expire == "1 month") {
            $tp->modify('+1 month');
          } elseif ($expire == "3 month") {
            $tp->modify('+3 months');
          } elseif ($expire == "1 day") {
            $tp->modify('+1 day');
          } elseif ($expire == "3 day") {
            $tp->modify('+3 days');
          } elseif ($expire == "14day") {
            $tp->modify('+14 days');
          } elseif ($expire == "partner") {
            $tp = new DateTime('31.12.3000');
          } elseif ($expire == "lifetime") {
            $tp = new DateTime('31.12.3000');
          } else {
            $tp = new DateTime('31.12.3000');
          }
          $tp = $tp->format('d.m.Y');

          $stmt = $conn->prepare("SELECT * FROM `users` WHERE `email` = ?");
          $stmt->bind_param("s", $email);
          $stmt->execute();
          $result = $stmt->get_result();
          $lc1 = $result->fetch_array();
          $id = $lc1['userid'];

          $stmt = $conn->prepare("INSERT INTO `redem_license` (`licenseid`, `license`, `expires`, `userid`) VALUES (?, ?, ?, ?)");
          $stmt->bind_param("isss", $licenseid, $key, $tp, $id);
          $stmt->execute();

          $stmt = $conn->prepare("DELETE FROM `keys` WHERE license = ?");
          $stmt->bind_param("s", $key);
          $stmt->execute();

          $text = "Your account has been successfully activated with a license. Please download the anticheat, upload it to your Server and start the anticheat. After that you should see the server in the table below. There you can press Manage and set everything. If you encounter any problems or have any questions, please contact our Discord support.";
          $stmt = $conn->prepare("INSERT INTO `notifications` (`text`, `date`, `userid`) VALUES (?, ?, ?)");
          $stmt->bind_param("sss", $text, $date, $id);
          $stmt->execute();

          $discord_webhook_url = $website_config['discord_webhook_url'];
          $webhook_data = array(
            'username' => '' . $website_config['site_name'] .'  - FiveM Anticheat',
            'avatar_url' => '' .  $website_config['site_logo'] .'',
            'embeds' => array(
              array(
                'title' => 'User Register',
                'description' => 'User ' . $username . ' with email ' . $email . ' has successfully activated their account with license ' . $key . ' on ' . date('Y-m-d H:i:s'),
                'color' => hexdec('00ff00'),
                'footer' => array(
                  'text' => 'Register Alert'
                ),
                'timestamp' => date('Y-m-d\TH:i:s\Z')
              )
            )
          );

          $curl = curl_init($discord_webhook_url);
          curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
          curl_setopt($curl, CURLOPT_POST, 1);
          curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($webhook_data));
          curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          curl_exec($curl);
          curl_close($curl);

          echo "<script>window.location.href='https://" . $website_config['site_domain'] . "/login'</script>";
          exit;
        }
      } else {
        ?>
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Information',
            text: 'License Key not found'
          });
        </script>
        <?php
      }
    } else {
      ?>
      <script>
        Swal.fire({
          icon: 'error',
          title: 'Invalid',
          text: 'Input Fields can\'t be empty'
        });
      </script>
      <?php
    }
  }
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
    <title><?php echo $website_config['site_name']; ?> | Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.24/sweetalert2.all.js"></script>
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
        
        .register-container {
          min-height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
          padding: 2rem;
        }
        
        .register-card {
          width: 100%;
          max-width: 440px;
          background: var(--bg-card);
          border-radius: 12px;
          border: 1px solid var(--border);
          box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .register-header {
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
        
        .register-title {
          font-size: 1.25rem;
          font-weight: 600;
          margin: 0 0 0.5rem;
        }
        
        .register-subtitle {
          color: var(--text-secondary);
          font-size: 0.95rem;
          margin: 0;
        }
        
        .register-content {
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
        
        .form-text {
          font-size: 0.75rem;
          color: var(--text-secondary);
          margin-top: 0.25rem;
        }
        
        .checkbox-wrapper {
          display: flex;
          align-items: flex-start;
          gap: 0.75rem;
          margin-bottom: 1.5rem;
        }
        
        .checkbox-input {
          margin: 0;
          transform: scale(1.1);
          accent-color: var(--primary);
        }
        
        .checkbox-label {
          font-size: 0.875rem;
          color: var(--text-secondary);
          line-height: 1.4;
          margin: 0;
        }
        
        .checkbox-label a {
          color: var(--primary);
          text-decoration: none;
          font-weight: 500;
        }
        
        .checkbox-label a:hover {
          color: var(--primary-dark);
        }
        
        .register-button {
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
        
        .register-button:hover:not(:disabled) {
          background: var(--primary-dark);
          transform: translateY(-1px);
        }
        
        .register-button:disabled {
          opacity: 0.6;
          cursor: not-allowed;
        }
        
        .register-button.loading {
          position: relative;
          color: transparent;
        }
        
        .register-button.loading::after {
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
        
        .register-footer {
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

        .dmca {
          display: flex;
          justify-content: center;
          align-items: center;
          margin-top: 20px;
          margin-bottom: 20px;
        }

        /* Form validation states */
        .form-group.error .form-control {
          border-color: #ef4444;
        }
        
        .form-group.success .form-control {
          border-color: var(--success);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="brand-container">
                    <div class="brand-logo">
                        <i class="uil uil-shield-check"></i>
                    </div>
                    <div class="brand-text">
                        <h1 class="brand-name"><?php echo $website_config['site_name']; ?></h1>
                        <p class="brand-subtitle">Server Protection</p>
                    </div>
                </div>
                
                <h2 class="register-title">Create Account</h2>
                <p class="register-subtitle">Enter your personal details to create account</p>
            </div>
            
            <div class="register-content">
                <form id="registerForm" method="post" class="theme-form" novalidate>
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
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control" 
                                   placeholder="test@gmail.com"
                                   value=""
                                   required>
                            <i class="uil uil-envelope"></i>
                        </div>
                        <small class="form-text">Please use a real address.</small>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control" 
                                   placeholder="********"
                                   value=""
                                   required>
                            <i class="uil uil-lock"></i>
                            <i class="uil uil-eye password-toggle" id="togglePassword"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="license">License</label>
                        <div class="input-wrapper">
                            <input type="text" 
                                   id="license" 
                                   name="license" 
                                   class="form-control" 
                                   placeholder="example: <?php echo $website_config['site_name']; ?>_9475HXJDKQ57KD"
                                   value=""
                                   required>
                            <i class="uil uil-key-skeleton"></i>
                        </div>
                    </div>

                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="terms-checkbox" class="checkbox-input" required>
                        <label for="terms-checkbox" class="checkbox-label">
                            I accept the <a href="https://<?php echo $website_config['site_domain']; ?>/tos">Terms</a>, 
                            <a href="https://<?php echo $website_config['site_domain']; ?>/privacy">Policy</a> and 
                            <a href="https://<?php echo $website_config['site_domain']; ?>/refund">Refund</a>
                        </label>
                    </div>

                    <button type="submit" name="btnRegister" class="register-button" id="registerButton">
                        <span>Create Account</span>
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
                    <p>Already have an account? <a href="https://<?php echo $website_config['site_domain']; ?>/login" class="footer-link">Sign in</a></p>
                </div>
                
                <div class="footer-links">
                    <a href="https://<?php echo $website_config['site_domain']; ?>/tos" class="footer-link">Terms</a>
                    <span class="separator">•</span>
                    <a href="https://<?php echo $website_config['site_domain']; ?>/privacy" class="footer-link">Privacy</a>
                    <span class="separator">•</span>
                    <a href="https://<?php echo $website_config['site_domain']; ?>/refund" class="footer-link">Refund</a>
                </div>

                <div class="dmca">
                    <a href="https://www.dmca.com/r/wrejk1x" title="DMCA.com Protection Status" class="dmca-badge">
                        <img src="https://images.dmca.com/Badges/dmca-badge-w100-5x1-11.png?ID=9c9de7b3-a4ce-4ec0-9d39-8072e9ad971a" alt="DMCA.com Protection Status" />
                    </a>
                    <script src="https://images.dmca.com/Badges/DMCABadgeHelper.min.js"></script>
                </div>
                
                <div class="register-footer">
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

        // Form submission handling
        const registerForm = document.getElementById('registerForm');
        const registerButton = document.getElementById('registerButton');

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