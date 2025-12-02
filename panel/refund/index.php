<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FiveSecurity | Refund Policy</title>
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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
          font-family: 'Inter', sans-serif;
          background: var(--bg-dark);
          color: var(--text-primary);
          line-height: 1.6;
        }

        .dashboard-container { display: flex; min-height: 100vh; }

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
          color: white;
          font-weight: 600;
          font-size: 1.2rem;
        }

        .sidebar-brand {
          font-size: 1.25rem;
          font-weight: 600;
          color: var(--text-primary);
        }

        .sidebar-nav { padding: 1rem 0; }
        .nav-section { margin-bottom: 2rem; }

        .nav-section-title {
          padding: 0 1.5rem 0.5rem;
          font-size: 0.75rem;
          font-weight: 600;
          color: #64748b;
          text-transform: uppercase;
          letter-spacing: 0.5px;
        }

        .nav-item { margin: 0.25rem 1rem; }

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

        .nav-link i { font-size: 18px; width: 20px; }

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
          background: var(--primary);
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-weight: 600;
        }

        .user-info { display: flex; flex-direction: column; }

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

        .dropdown-item:last-child { border-bottom: none; }

        .dropdown-arrow {
          margin-left: 0.5rem;
          transition: transform 0.3s ease;
        }

        .content-area {
          flex: 1;
          max-width: 900px;
          margin: 0 auto;
          padding: 2rem;
          width: 100%;
        }

        .refund-header {
          text-align: center;
          margin-bottom: 2rem;
          padding-bottom: 2rem;
          border-bottom: 1px solid var(--border);
        }

        .refund-header h1 {
          font-size: 2.5rem;
          font-weight: 700;
          color: var(--text-primary);
          margin-bottom: 0.5rem;
        }

        .refund-header .subtitle {
          font-size: 1rem;
          color: var(--text-secondary);
          margin-bottom: 0.5rem;
        }

        .refund-section {
          margin-bottom: 3rem;
        }

        .section-title {
          font-size: 1.5rem;
          font-weight: 600;
          color: var(--text-primary);
          margin-bottom: 1rem;
          padding-bottom: 0.5rem;
          border-bottom: 2px solid var(--primary);
        }

        .section-subtitle {
          font-size: 1.25rem;
          font-weight: 600;
          color: var(--text-primary);
          margin: 1.5rem 0 1rem;
        }

        .refund-text {
          color: var(--text-secondary);
          line-height: 1.6;
          margin-bottom: 1rem;
          font-size: 1rem;
        }

        .refund-text strong {
          color: var(--text-primary);
        }

        .refund-list {
          color: var(--text-secondary);
          margin: 1rem 0;
          padding-left: 1.5rem;
        }

        .refund-list li {
          margin-bottom: 0.5rem;
          font-size: 1rem;
        }

        .refund-link {
          color: var(--primary);
          text-decoration: none;
        }

        .refund-link:hover {
          text-decoration: underline;
        }

        .highlight-box {
          background: rgba(59, 130, 246, 0.1);
          border: 1px solid var(--primary);
          border-radius: 8px;
          padding: 1.5rem;
          margin: 1.5rem 0;
        }

        .contact-box {
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 8px;
          padding: 1.5rem;
          margin-top: 2rem;
        }

        .conditions-box {
          background: rgba(239, 68, 68, 0.1);
          border: 1px solid #ef4444;
          border-radius: 8px;
          padding: 1.5rem;
          margin: 1.5rem 0;
        }

        .conditions-box .refund-text {
          color: #fecaca;
        }

        .mobile-menu-toggle {
          display: none;
          background: none;
          border: none;
          color: var(--text-primary);
          font-size: 1.5rem;
          cursor: pointer;
        }

        .footer {
          background: var(--bg-card);
          border-top: 1px solid var(--border);
          padding: 1rem 2rem;
          text-align: center;
          color: var(--text-secondary);
          font-size: 0.875rem;
        }

        @media (max-width: 768px) {
          .mobile-menu-toggle { display: block; }
          .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
          }
          .sidebar.open { transform: translateX(0); }
          .main-content { margin-left: 0; }
          .content-area { padding: 1rem; }
          .refund-header h1 { font-size: 2rem; }
          .header { padding: 1rem; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">FS</div>
                <div class="sidebar-brand">FiveSecurity</div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">General</div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="uil uil-estate"></i>
                            <span>Home</span>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Products</div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="uil uil-download-alt"></i>
                            <span>Download FiveSecurity</span>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Information</div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="uil uil-file-alt"></i>
                            <span>T.O.S</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="uil uil-question-circle"></i>
                            <span>FAQ</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="uil uil-credit-card"></i>
                            <span>Refund</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="uil uil-shield-check"></i>
                            <span>Privacy Policy</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="uil uil-book-open"></i>
                            <span>Docs</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="uil uil-discord"></i>
                            <span>Discord</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <div class="main-content">
            <div class="header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                        <i class="uil uil-bars"></i>
                    </button>
                    <div class="header-title">Return and Refund Policy</div>
                </div>
                <div class="user-menu" onclick="toggleUserDropdown()">
                    <div class="user-avatar">U</div>
                    <div class="user-info">
                        <div class="user-name">Username</div>
                        <div class="user-role">user</div>
                    </div>
                    <i class="uil uil-angle-down dropdown-arrow"></i>
                    
                    <div class="user-dropdown" id="userDropdown">
                        <a href="#" class="dropdown-item">
                            <i class="uil uil-user"></i>
                            <span>Account</span>
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="uil uil-setting"></i>
                            <span>Settings</span>
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="uil uil-signout"></i>
                            <span>Log out</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-area">
                <div class="refund-header">
                    <h1>Return and Refund Policy</h1>
                    <div class="subtitle">Understand your rights regarding cancellations, returns, and refunds</div>
                </div>

                <div class="refund-section">
                    <h2 class="section-title">Interpretation and Definitions</h2>
                    
                    <h3 class="section-subtitle">Interpretation</h3>
                    <p class="refund-text">The words of which the initial letter is capitalized have meanings defined under the following conditions. The following definitions shall have the same meaning regardless of whether they appear in singular or in plural.</p>
                    
                    <h3 class="section-subtitle">Definitions</h3>
                    <p class="refund-text">For the purposes of this Return and Refund Policy:</p>
                    
                    <ul class="refund-list">
                        <li><strong>Company</strong> (referred to as either "the Company", "We", "Us" or "Our" in this Agreement) refers to FiveSecurity ANTICHEAT.</li>
                        <li><strong>Goods</strong> refer to the items offered for sale on the Service.</li>
                        <li><strong>Orders</strong> mean a request by You to purchase Goods from Us.</li>
                        <li><strong>Service</strong> refers to the Website.</li>
                        <li><strong>Website</strong> refers to FiveSecurity ANTICHEAT, accessible from <a href="https://panel.fivesecurity.de/" class="refund-link" target="_blank">https://panel.fivesecurity.de/</a></li>
                        <li><strong>You</strong> means the individual accessing or using the Service, or the company, or other legal entity on behalf of which such individual is accessing or using the Service, as applicable.</li>
                    </ul>
                </div>

                <div class="refund-section">
                    <h2 class="section-title">Your Order Cancellation Rights</h2>
                    
                    <div class="highlight-box">
                        <p class="refund-text"><strong>14-Day Cancellation Period:</strong> You are entitled to cancel Your Order within 14 days without giving any reason for doing so.</p>
                    </div>
                    
                    <p class="refund-text">The deadline for cancelling an Order is 14 days from the date on which You received the Goods or on which a third party you have appointed, who is not the carrier, takes possession of the product delivered.</p>
                    
                    <p class="refund-text">In order to exercise Your right of cancellation, You must inform Us of your decision by means of a clear statement. You can inform us of your decision by:</p>
                    
                    <ul class="refund-list">
                        <li>By our Email: <a href="mailto:contact@FiveSecurity.net" class="refund-link">contact@FiveSecurity.net</a></li>
                    </ul>
                    
                    <p class="refund-text">We will reimburse You no later than 14 days from the day on which We receive the returned Goods. We will use the store credits that you can use to repurchase goods at <a href="https://shop.FiveSecurity.net" class="refund-link" target="_blank">https://shop.FiveSecurity.net</a>.</p>
                </div>

                <div class="refund-section">
                    <h2 class="section-title">Conditions for Returns</h2>
                    
                    <p class="refund-text">In order for the Goods to be eligible for a return, please make sure that:</p>
                    
                    <ul class="refund-list">
                        <li>The Goods were purchased in the last 14 days</li>
                    </ul>
                    
                    <div class="conditions-box">
                        <p class="refund-text"><strong>The following Goods cannot be returned:</strong></p>
                        <ul class="refund-list">
                            <li>Your product is personalized for your purchase.</li>
                            <li>The time of 14 days for the product to return has passed.</li>
                        </ul>
                    </div>
                    
                    <p class="refund-text">We reserve the right to refuse returns of any merchandise that does not meet the above return conditions in our sole discretion.</p>
                    
                    <p class="refund-text">Only regular priced Goods may be refunded. Unfortunately, Goods on sale cannot be refunded. This exclusion may not apply to You if it is not permitted by applicable law.</p>
                    
                    <div class="highlight-box">
                        <p class="refund-text"><strong>Important Notice:</strong> We cannot be held responsible for Goods damaged or lost in return shipment. Therefore, We recommend an insured and trackable mail service. We are unable to issue a refund without actual receipt of the Goods or proof of received return delivery.</p>
                    </div>
                </div>

                <div class="refund-section">
                    <h2 class="section-title">Contact Us</h2>
                    
                    <div class="contact-box">
                        <p class="refund-text">If you have any questions about our Returns and Refunds Policy, please contact us:</p>
                        
                        <ul class="refund-list">
                            <li>By our Email: <a href="mailto:contact@FiveSecurity.net" class="refund-link">contact@FiveSecurity.net</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <p>2022-<script>document.write(new Date().getFullYear())</script> FiveSecurity. All rights reserved.</p>
            </footer>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }

        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            const arrow = document.querySelector('.dropdown-arrow');
            dropdown.classList.toggle('show');
            arrow.style.transform = dropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
        }

        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.getElementById('userDropdown');
            if (!userMenu.contains(event.target)) {
                dropdown.classList.remove('show');
                document.querySelector('.dropdown-arrow').style.transform = 'rotate(0deg)';
            }
        });
    </script>
</body>
</html>