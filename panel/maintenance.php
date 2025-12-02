<?php
include('database.php');
include('config.php');

$stmt = $conn->prepare("SELECT COUNT(*) FROM `system` WHERE `maintenance` = 1");
$stmt->execute();
$result = $stmt->get_result();
if ($result->fetch_row()[0] == 0 && isset($_SESSION['id'])) {
  header("Location: https://" . $website_config['site_domain']);
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $website_config['site_description']; ?>">
    <meta name="keywords" content="<?php echo $website_config['site_keywords']; ?>">
    <meta name="author" content="<?php echo $website_config['site_author']; ?>">
    <link rel="icon" href="<?php echo $website_config['site_favicon']; ?>" type="image/x-icon">
    <title><?php echo $website_config['site_name']; ?> | Maintenance</title>
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
          --warning-orange: #f59e0b;
          --warning-bg: rgba(245, 158, 11, 0.1);
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
          overflow: hidden;
        }

        .maintenance-wrapper {
          max-width: 1000px;
          width: 95%;
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 16px;
          padding: 3rem 4rem;
          text-align: center;
          box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
          position: relative;
          overflow: hidden;
        }

        .maintenance-wrapper::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          height: 4px;
          background: linear-gradient(90deg, var(--primary), var(--warning-orange), var(--primary));
          background-size: 200% 100%;
          animation: gradient 3s ease infinite;
        }

        @keyframes gradient {
          0%, 100% { background-position: 0% 50%; }
          50% { background-position: 100% 50%; }
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
          box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }

        .site-name {
          font-size: 1.5rem;
          font-weight: 600;
          color: var(--text-primary);
          margin-bottom: 2rem;
        }

        .maintenance-icons {
          display: flex;
          justify-content: center;
          gap: 2rem;
          margin-bottom: 2rem;
        }

        .maintenance-icon {
          width: 50px;
          height: 50px;
          background: var(--warning-bg);
          border: 2px solid var(--warning-orange);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          animation: spin 3s linear infinite;
        }

        .maintenance-icon:nth-child(2) {
          animation-delay: -1s;
        }

        .maintenance-icon:nth-child(3) {
          animation-delay: -2s;
        }

        .maintenance-icon i {
          font-size: 1.5rem;
          color: var(--warning-orange);
        }

        @keyframes spin {
          from { transform: rotate(0deg); }
          to { transform: rotate(360deg); }
        }

        .maintenance-title {
          font-size: 2.5rem;
          font-weight: 800;
          color: var(--warning-orange);
          margin-bottom: 1rem;
          letter-spacing: 2px;
          text-shadow: 0 0 20px rgba(245, 158, 11, 0.3);
        }

        .maintenance-subtitle {
          font-size: 1.1rem;
          color: var(--text-secondary);
          margin-bottom: 1.5rem;
          line-height: 1.6;
          max-width: 600px;
          margin-left: auto;
          margin-right: auto;
        }

        .maintenance-details {
          background: var(--bg-sidebar);
          border: 1px solid var(--border);
          border-radius: 12px;
          padding: 2rem;
          margin-bottom: 2rem;
        }

        .maintenance-details h4 {
          color: var(--text-primary);
          font-size: 1.2rem;
          font-weight: 600;
          margin-bottom: 1rem;
        }

        .maintenance-details p {
          color: var(--text-secondary);
          margin-bottom: 1rem;
        }

        .status-indicators {
          display: flex;
          justify-content: center;
          gap: 1rem;
          margin-bottom: 2rem;
        }

        .status-indicator {
          display: flex;
          align-items: center;
          gap: 0.5rem;
          background: var(--bg-sidebar);
          border: 1px solid var(--border);
          border-radius: 8px;
          padding: 0.75rem 1rem;
        }

        .status-dot {
          width: 8px;
          height: 8px;
          border-radius: 50%;
          background: var(--warning-orange);
          animation: pulse 2s infinite;
        }

        @keyframes pulse {
          0%, 100% { opacity: 1; transform: scale(1); }
          50% { opacity: 0.7; transform: scale(1.2); }
        }

        .status-text {
          font-size: 0.875rem;
          color: var(--text-secondary);
          font-weight: 500;
        }

        .contact-info {
          display: flex;
          justify-content: center;
          gap: 2rem;
          margin-top: 2rem;
        }

        .contact-link {
          display: flex;
          align-items: center;
          gap: 0.5rem;
          color: var(--primary);
          text-decoration: none;
          font-weight: 500;
          transition: all 0.2s ease;
          padding: 0.5rem 1rem;
          border-radius: 8px;
          border: 1px solid transparent;
        }

        .contact-link:hover {
          background: rgba(59, 130, 246, 0.1);
          border-color: var(--primary);
          transform: translateY(-1px);
        }

        .footer-text {
          margin-top: 2rem;
          font-size: 0.875rem;
          color: #64748b;
        }

        .floating-particles {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          pointer-events: none;
          overflow: hidden;
        }

        .particle {
          position: absolute;
          width: 4px;
          height: 4px;
          background: var(--primary);
          border-radius: 50%;
          opacity: 0.3;
          animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(odd) {
          background: var(--warning-orange);
          animation-duration: 8s;
        }

        @keyframes float {
          0%, 100% { transform: translateY(0px) rotate(0deg); }
          50% { transform: translateY(-20px) rotate(180deg); }
        }

        @media (max-width: 768px) {
          .maintenance-wrapper {
            padding: 2rem 1.5rem;
            max-width: 90%;
          }
          
          .maintenance-title {
            font-size: 1.8rem;
          }
          
          .maintenance-subtitle {
            font-size: 1rem;
          }
          
          .maintenance-icons {
            gap: 1rem;
          }
          
          .maintenance-icon {
            width: 40px;
            height: 40px;
          }
          
          .contact-info {
            flex-direction: column;
            gap: 1rem;
          }

          .status-indicators {
            flex-direction: column;
            gap: 0.5rem;
          }

          .maintenance-details {
            grid-template-columns: 1fr;
            gap: 1rem;
            text-align: center;
          }

          .maintenance-details h4 {
            grid-column: 1;
          }
        }
    </style>
</head>
<body>
    <div class="maintenance-wrapper">
        <div class="floating-particles">
            <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
            <div class="particle" style="left: 20%; animation-delay: 1s;"></div>
            <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
            <div class="particle" style="left: 40%; animation-delay: 3s;"></div>
            <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
            <div class="particle" style="left: 60%; animation-delay: 5s;"></div>
            <div class="particle" style="left: 70%; animation-delay: 0.5s;"></div>
            <div class="particle" style="left: 80%; animation-delay: 1.5s;"></div>
            <div class="particle" style="left: 90%; animation-delay: 2.5s;"></div>
        </div>

        <div class="site-logo">FS</div>
        <div class="site-name"><?php echo $website_config['site_name']; ?></div>
        
        <div class="maintenance-icons">
            <div class="maintenance-icon">
                <i class="uil uil-setting"></i>
            </div>
            <div class="maintenance-icon">
                <i class="uil uil-cog"></i>
            </div>
            <div class="maintenance-icon">
                <i class="uil uil-wrench"></i>
            </div>
        </div>
        
        <h1 class="maintenance-title">MAINTENANCE</h1>
        
        <p class="maintenance-subtitle">
            Our site is currently under maintenance.<br>
            We will be back shortly. Thank you for your patience.
        </p>
        
        <div class="status-indicators">
            <div class="status-indicator">
                <div class="status-dot"></div>
                <span class="status-text">System Updates</span>
            </div>
            <div class="status-indicator">
                <div class="status-dot"></div>
                <span class="status-text">Database Optimization</span>
            </div>
            <div class="status-indicator">
                <div class="status-dot"></div>
                <span class="status-text">Security Enhancements</span>
            </div>
        </div>
        
        <div class="maintenance-details">
            <h4>What's Happening?</h4>
            <div class="detail-section">
                <p>We're performing scheduled maintenance to improve your experience.</p>
                <p><strong>Estimated Duration:</strong> Unknown...</p>
            </div>
            <div class="detail-section">
                <p>This includes system updates, performance optimizations, and security enhancements.</p>
                <p><strong>Status:</strong> In Progress</p>
            </div>
        </div>
        
        <div class="contact-info">
            <a href="mailto:contact@FiveSecurity.net" class="contact-link">
                <i class="uil uil-envelope"></i>
                Contact Support
            </a>
            <a href="https://<?php echo $website_config['site_domain']; ?>/discord" class="contact-link" target="_blank">
                <i class="uil uil-discord"></i>
                Discord Updates
            </a>
        </div>
        
        <div class="footer-text">
            We appreciate your patience during this maintenance window.
        </div>
    </div>

    <script>
        // Add some interactive floating particles
        function createParticle() {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 6 + 's';
            particle.style.animationDuration = (Math.random() * 4 + 4) + 's';
            
            document.querySelector('.floating-particles').appendChild(particle);
            
            setTimeout(() => {
                particle.remove();
            }, 8000);
        }

        // Create particles periodically
        setInterval(createParticle, 2000);

        // Add some mouse interaction
        document.addEventListener('mousemove', (e) => {
            const particles = document.querySelectorAll('.particle');
            particles.forEach(particle => {
                const rect = particle.getBoundingClientRect();
                const distance = Math.sqrt(
                    Math.pow(e.clientX - rect.left, 2) + 
                    Math.pow(e.clientY - rect.top, 2)
                );
                
                if (distance < 100) {
                    particle.style.transform = 'scale(1.5)';
                    particle.style.opacity = '0.8';
                } else {
                    particle.style.transform = 'scale(1)';
                    particle.style.opacity = '0.3';
                }
            });
        });
    </script>
</body>
</html>