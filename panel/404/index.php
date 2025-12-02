<?php
include('../config.php'); // Dont remove it!
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
    <title><?php echo $website_config['site_name']; ?> | 404 Not Found</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <style>
        :root {
          --primary: #3b82f6;
          --primary-dark: #2563eb;
          --bg-dark: #0f172a;
          --bg-card: #1e293b;
          --text-primary: #f8fafc;
          --text-secondary: #94a3b8;
          --text-muted: #64748b;
          --border: #334155;
          --danger: #ef4444;
          --warning: #f59e0b;
        }

        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }

        body {
          font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
          background: var(--bg-dark);
          color: var(--text-primary);
          min-height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
          overflow: hidden;
          position: relative;
        }

        /* Animated background */
        .bg-animation {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          overflow: hidden;
          z-index: 1;
        }

        .grid-overlay {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-image: 
            linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
          background-size: 15px 15px;
        }

        /* Main container */
        .error-container {
          position: relative;
          z-index: 10;
          text-align: center;
          max-width: 500px;
          padding: 1.5rem;
          animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
          from {
            opacity: 0;
            transform: translateY(30px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        /* Brand section */
        .error-brand {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 0.75rem;
          margin-bottom: 2rem;
        }

        .brand-logo {
          width: 40px;
          height: 40px;
          background: var(--primary);
          border-radius: 10px;
          display: flex;
          align-items: center;
          justify-content: center;
        }

        .brand-logo i {
          font-size: 20px;
          color: white;
        }

        .brand-name {
          font-size: 1.25rem;
          font-weight: 600;
          color: var(--text-primary);
        }

        /* Error illustration */
        .error-illustration {
          margin-bottom: 1.5rem;
          position: relative;
        }

        .error-icon {
          width: 100px;
          height: 100px;
          margin: 0 auto 1.5rem;
          background: linear-gradient(135deg, var(--danger), var(--warning));
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          position: relative;
        }

        .error-icon i {
          font-size: 2.5rem;
          color: white;
        }

        /* Error content */
        .error-code {
          font-size: 4rem;
          font-weight: 800;
          color: var(--danger);
          margin-bottom: 0.75rem;
        }

        .error-title {
          font-size: 1.5rem;
          font-weight: 600;
          color: var(--text-primary);
          margin-bottom: 0.75rem;
        }

        .error-message {
          font-size: 1rem;
          color: var(--text-secondary);
          line-height: 1.6;
          margin-bottom: 2rem;
          max-width: 400px;
          margin-left: auto;
          margin-right: auto;
        }

        /* Action buttons */
        .error-actions {
          display: flex;
          flex-direction: row;
          gap: 1rem;
          align-items: center;
          justify-content: center;
          margin-bottom: 2rem;
        }

        .btn {
          display: inline-flex;
          align-items: center;
          gap: 0.5rem;
          padding: 0.75rem 1.5rem;
          border: none;
          border-radius: 8px;
          font-size: 0.875rem;
          font-weight: 600;
          text-decoration: none;
          cursor: pointer;
          transition: all 0.3s ease;
        }

        .btn-primary {
          background: var(--primary);
          color: white;
        }

        .btn-primary:hover {
          background: var(--primary-dark);
          transform: translateY(-2px);
        }

        .btn-secondary {
          background: transparent;
          color: var(--text-secondary);
          border: 1px solid var(--border);
        }

        .btn-secondary:hover {
          background: var(--bg-card);
          color: var(--text-primary);
          border-color: var(--primary);
        }

        .btn i {
          font-size: 1rem;
        }

        /* Additional info */
        .error-info {
          margin-top: 2rem;
          padding-top: 2rem;
          border-top: 1px solid var(--border);
        }

        .info-grid {
          display: flex;
          gap: 1rem;
          justify-content: center;
          margin-top: 1.5rem;
        }

        .info-item {
          background: transparent;
          border: 1px solid var(--border);
          border-radius: 8px;
          padding: 1rem;
          transition: all 0.3s ease;
          text-align: center;
          min-width: 150px;
        }

        .info-item:hover {
          border-color: var(--primary);
          background: var(--bg-card);
        }

        .info-item i {
          font-size: 1.5rem;
          color: var(--primary);
          margin-bottom: 0.5rem;
        }

        .info-item h3 {
          font-size: 0.875rem;
          font-weight: 600;
          color: var(--text-primary);
          margin-bottom: 0.25rem;
        }

        .info-item p {
          display: none;
        }

        .info-item a {
          color: var(--primary);
          text-decoration: none;
          font-weight: 500;
        }

        .info-item a:hover {
          text-decoration: underline;
        }

        /* Responsive design */
        @media (max-width: 768px) {
          .error-container {
            padding: 1rem;
          }

          .error-code {
            font-size: 4rem;
          }

          .error-title {
            font-size: 1.5rem;
          }

          .error-message {
            font-size: 1rem;
          }

          .error-actions {
            flex-direction: column;
            width: 100%;
          }

          .btn {
            width: 100%;
            justify-content: center;
          }

          .info-grid {
            flex-direction: column;
            align-items: center;
          }

          .info-item {
            width: 100%;
            max-width: 200px;
          }

          .error-icon {
            width: 120px;
            height: 120px;
          }

          .error-icon i {
            font-size: 3rem;
          }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
          width: 8px;
        }

        ::-webkit-scrollbar-track {
          background: var(--bg-dark);
        }

        ::-webkit-scrollbar-thumb {
          background: var(--border);
          border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
          background: var(--text-muted);
        }
    </style>
</head>
<body>
    <!-- Animated background -->
    <div class="bg-animation">
        <div class="grid-overlay"></div>
    </div>

    <!-- Main error container -->
    <div class="error-container">
        <!-- Brand section -->
        <div class="error-brand">
            <div class="brand-logo">
                <i class="uil uil-shield-check"></i>
            </div>
            <div class="brand-name"><?php echo $website_config['site_name']; ?></div>
        </div>

        <!-- Error illustration -->
        <div class="error-illustration">
            <div class="error-icon">
                <i class="uil uil-exclamation-triangle"></i>
            </div>
        </div>

        <!-- Error content -->
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-message">
            The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
        </p>

        <!-- Action buttons -->
        <div class="error-actions">
            <a href="https://<?php echo $website_config['site_domain']; ?>" class="btn btn-primary">
                <i class="uil uil-estate"></i>
                <span>Back to Panel</span>
            </a>
            <button onclick="window.history.back()" class="btn btn-secondary">
                <i class="uil uil-arrow-left"></i>
                <span>Go Back</span>
            </button>
        </div>

        <!-- Additional info -->
        <div class="error-info">
            <div class="info-grid">
                <div class="info-item">
                    <i class="uil uil-headphones"></i>
                    <h3>Need Help?</h3>
                    <p>Contact our support team for assistance with any issues you're experiencing.</p>
                    <a href="/discord">Join Discord</a>
                </div>
                <div class="info-item">
                    <i class="uil uil-file-alt"></i>
                    <h3>Documentation</h3>
                    <p>Check our comprehensive documentation for guides and troubleshooting.</p>
                    <a href="<?php echo $website_config['docs_url']; ?>">View Docs</a>
                </div>
                <div class="info-item">
                    <i class="uil uil-bug"></i>
                    <h3>Report Issue</h3>
                    <p>Found a broken link? Help us improve by reporting the issue.</p>
                    <a href="/discord">Report Bug</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Add ripple effect to buttons
            document.querySelectorAll('.btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    const rect = this.getBoundingClientRect();
                    const ripple = document.createElement('span');
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.3);
                        transform: scale(0);
                        animation: rippleEffect 0.6s linear;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                    `;
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Add CSS for ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes rippleEffect {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        });

        // Prevent right-click and text selection (like in original)
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
        });

        // Add some console easter egg
        console.log(`
        ████████╗██╗  ██╗██╗███╗   ██╗██╗  ██╗██╗███╗   ██╗ ██████╗ 
        ╚══██╔══╝██║  ██║██║████╗  ██║██║ ██╔╝██║████╗  ██║██╔════╝ 
           ██║   ███████║██║██╔██╗ ██║█████╔╝ ██║██╔██╗ ██║██║  ███╗
           ██║   ██╔══██║██║██║╚██╗██║██╔═██╗ ██║██║╚██╗██║██║   ██║
           ██║   ██║  ██║██║██║ ╚████║██║  ██╗██║██║ ╚████║╚██████╔╝
           ╚═╝   ╚═╝  ╚═╝╚═╝╚═╝  ╚═══╝╚═╝  ╚═╝╚═╝╚═╝  ╚═══╝ ╚═════╝ 
        
        🤔 Lost? This page doesn't exist!
        💡 Try going back to the main panel.
        🐛 If you think this is a bug, let us know on Discord!
        `);
    </script>
</body>
</html>