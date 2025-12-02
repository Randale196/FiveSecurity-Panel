<?php
session_start();
include('../func.php');
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
    <title><?php echo $website_config['site_name']; ?> | FAQ</title>
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
        }

        .sidebar-logo img { max-width: 100%; max-height: 100%; border-radius: 4px; }

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
          max-width: 800px;
          margin: 0 auto;
          padding: 2rem;
          width: 100%;
        }

        .faq-header { text-align: center; margin-bottom: 2rem; }

        .faq-header h1 {
          font-size: 2rem;
          font-weight: 700;
          color: var(--text-primary);
          margin-bottom: 0.5rem;
        }

        .faq-header p {
          font-size: 1rem;
          color: var(--text-secondary);
        }

        .search-container {
          margin-bottom: 1.5rem;
          position: relative;
        }

        .search-input {
          width: 100%;
          padding: 0.75rem 1rem 0.75rem 2.5rem;
          border: 1px solid var(--border);
          border-radius: 8px;
          background: var(--bg-card);
          color: var(--text-primary);
          font-size: 0.875rem;
        }

        .search-input:focus {
          outline: none;
          border-color: var(--primary);
        }

        .search-icon {
          position: absolute;
          left: 0.75rem;
          top: 50%;
          transform: translateY(-50%);
          color: var(--text-secondary);
          font-size: 1rem;
        }

        .faq-items { display: grid; gap: 0.75rem; }

        .faq-item {
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 8px;
          overflow: hidden;
        }

        .faq-question {
          width: 100%;
          padding: 1rem;
          background: none;
          border: none;
          color: var(--text-primary);
          font-size: 0.875rem;
          font-weight: 500;
          text-align: left;
          cursor: pointer;
          display: flex;
          align-items: center;
          gap: 0.75rem;
          transition: all 0.2s ease;
        }

        .faq-question:hover { background: rgba(59, 130, 246, 0.05); }

        .question-icon {
          font-size: 1rem;
          color: var(--primary);
          min-width: 16px;
        }

        .question-text { flex: 1; }

        .expand-icon {
          font-size: 1rem;
          transition: transform 0.3s ease;
          color: var(--text-secondary);
        }

        .faq-question.expanded .expand-icon { transform: rotate(180deg); }

        .faq-answer {
          padding: 0 1rem;
          max-height: 0;
          overflow: hidden;
          transition: all 0.3s ease;
          background: rgba(59, 130, 246, 0.02);
        }

        .faq-answer.show {
          max-height: 400px;
          padding: 1rem;
        }

        .answer-content {
          color: var(--text-secondary);
          line-height: 1.5;
          font-size: 0.875rem;
        }

        .answer-content a {
          color: var(--primary);
          text-decoration: none;
        }

        .answer-content a:hover { text-decoration: underline; }

        .answer-content ul {
          padding-left: 1.2rem;
          margin: 0;
        }

        .answer-content li { margin-bottom: 0.5rem; }

        .answer-content code {
          background: var(--bg-dark);
          padding: 0.2rem 0.4rem;
          border-radius: 3px;
          font-family: monospace;
          font-size: 0.8rem;
          cursor: pointer;
        }

        .answer-content code:hover { background: rgba(59, 130, 246, 0.1); }

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
          .faq-header h1 { font-size: 1.5rem; }
          .header { padding: 1rem; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="<?php echo $website_config['site_logo']; ?>" alt="Logo">
                </div>
                <div class="sidebar-brand"><?php echo $website_config['site_name']; ?></div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">General</div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>" class="nav-link">
                            <i class="uil uil-estate"></i>
                            <span>Home</span>
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
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Information</div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/tos/" class="nav-link">
                            <i class="uil uil-file-alt"></i>
                            <span>T.O.S</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/faq/" class="nav-link active">
                            <i class="uil uil-question-circle"></i>
                            <span>FAQ</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/refund/" class="nav-link">
                            <i class="uil uil-credit-card"></i>
                            <span>Refund</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="<?php echo $website_config['docs_url']; ?>" class="nav-link">
                            <i class="uil uil-book-open"></i>
                            <span>Docs</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/discord" class="nav-link">
                            <i class="uil uil-discord"></i>
                            <span>Discord</span>
                        </a>
                    </div>
                </div>

                <?php if ($_SESSION["group"] == "admin"): ?>
                <div class="nav-section">
                    <div class="nav-section-title">Admin</div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/server/overview/" class="nav-link">
                            <i class="uil uil-server"></i>
                            <span>Server Overview</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/server/servertable.php" class="nav-link">
                            <i class="uil uil-table"></i>
                            <span>Server Table</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/logs/authlogs/" class="nav-link">
                            <i class="uil uil-file-search-alt"></i>
                            <span>Auth Logs</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/logs/serverlogs/" class="nav-link">
                            <i class="uil uil-terminal"></i>
                            <span>Server Logs</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/auth/overview" class="nav-link">
                            <i class="uil uil-key-skeleton"></i>
                            <span>Key Overview</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/auth/keygenerator" class="nav-link">
                            <i class="uil uil-plus-circle"></i>
                            <span>Key Creator</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/website/settings/" class="nav-link">
                            <i class="uil uil-setting"></i>
                            <span>Settings</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/admin/website/config/" class="nav-link">
                            <i class="uil uil-edit"></i>
                            <span>Config</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </nav>
        </div>

        <div class="main-content">
            <div class="header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                        <i class="uil uil-bars"></i>
                    </button>
                    <div class="header-title">FAQ</div>
                </div>
                <div class="user-menu" onclick="toggleUserDropdown()">
                    <img class="user-avatar" src="<?php echo $avatar; ?>" alt="Avatar">
                    <div class="user-info">
                        <div class="user-name"><?php echo $_SESSION["username"]; ?></div>
                        <div class="user-role"><?php echo $_SESSION["group"]; ?></div>
                    </div>
                    <i class="uil uil-angle-down dropdown-arrow"></i>
                    
                    <div class="user-dropdown" id="userDropdown">
                        <a href="https://<?php echo $website_config['site_domain']; ?>/account" class="dropdown-item">
                            <i class="uil uil-user"></i>
                            <span>Account</span>
                        </a>
                        <a href="https://<?php echo $website_config['site_domain']; ?>/account/" class="dropdown-item">
                            <i class="uil uil-setting"></i>
                            <span>Settings</span>
                        </a>
                        <a href="https://<?php echo $website_config['site_domain']; ?>/logout.php" class="dropdown-item">
                            <i class="uil uil-signout"></i>
                            <span>Log out</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-area">
                <div class="faq-header">
                    <h1>Frequently Asked Questions</h1>
                    <p>Find answers to commonly asked questions</p>
                </div>

                <div class="search-container">
                    <i class="uil uil-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search FAQ..." id="faqSearch">
                </div>

                <div class="faq-items">
                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleAnswer('faq1')">
                            <i class="uil uil-question-circle question-icon"></i>
                            <span class="question-text">How can I setup the Admin bypass?</span>
                            <i class="uil uil-angle-down expand-icon"></i>
                        </button>
                        <div class="faq-answer" id="faq1">
                            <div class="answer-content">
                                <strong>Admin Bypass via Steam IDs:</strong> <code>add_ace identifier.steam:SteamID FiveSecurity.Bypass allow</code><br><br>
                                <strong>Admin Bypass via Groups:</strong> <code>add_ace group.admin FiveSecurity.Bypass allow</code>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleAnswer('faq2')">
                            <i class="uil uil-question-circle question-icon"></i>
                            <span class="question-text">Under what circumstances should a "global ban" be issued?</span>
                            <i class="uil uil-angle-down expand-icon"></i>
                        </button>
                        <div class="faq-answer" id="faq2">
                            <div class="answer-content">
                                We only ban on <u>Injections</u> which has been 100% verified
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleAnswer('faq3')">
                            <i class="uil uil-question-circle question-icon"></i>
                            <span class="question-text">Which commands have <?php echo $website_config['site_name']; ?>?</span>
                            <i class="uil uil-angle-down expand-icon"></i>
                        </button>
                        <div class="faq-answer" id="faq3">
                            <div class="answer-content">
                                <ul>
                                    <li><code>FiveUnban 17</code>: This command unbans the player with ID 17</li>
                                    <li><code>FiveScreen 53</code>: This command sends a screenshot of the player with ID 53 to the screenshot channel</li>
                                    <li><code>FiveClearpeds</code>: This command removes all pedestrians (peds) from the map</li>
                                    <li><code>FiveClearveh</code>: This command removes all vehicles from the map</li>
                                    <li><code>FiveClearprops</code>: This command removes all objects (props) from the map</li>
                                    <li><code>FiveClearall</code>: This command removes all pedestrians, vehicles, and objects from the map</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleAnswer('faq4')">
                            <i class="uil uil-question-circle question-icon"></i>
                            <span class="question-text">How can I fix the "screenshot-basic is missing" error?</span>
                            <i class="uil uil-angle-down expand-icon"></i>
                        </button>
                        <div class="faq-answer" id="faq4">
                            <div class="answer-content">
                                Install screenshot-basic (<a href="https://www.dropbox.com/s/7t0cnlmeorv8f4t/screenshot-basic.zip?dl=0">Download here</a>) and restart your Server.
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleAnswer('faq5')">
                            <i class="uil uil-question-circle question-icon"></i>
                            <span class="question-text">How can I reset my License?</span>
                            <i class="uil uil-angle-down expand-icon"></i>
                        </button>
                        <div class="faq-answer" id="faq5">
                            <div class="answer-content">
                                You can't reset your License Key.
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleAnswer('faq6')">
                            <i class="uil uil-question-circle question-icon"></i>
                            <span class="question-text">How can I reset the License IP?</span>
                            <i class="uil uil-angle-down expand-icon"></i>
                        </button>
                        <div class="faq-answer" id="faq6">
                            <div class="answer-content">
                                Contact our Support via Discord or click the reset ip button on the panel (Once a month).
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleAnswer('faq7')">
                            <i class="uil uil-question-circle question-icon"></i>
                            <span class="question-text">How can I change my Email or password?</span>
                            <i class="uil uil-angle-down expand-icon"></i>
                        </button>
                        <div class="faq-answer" id="faq7">
                            <div class="answer-content">
                                You can change your email and password via the <a href="https://<?php echo $website_config['site_domain']; ?>/account/">settings</a> page
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleAnswer('faq8')">
                            <i class="uil uil-question-circle question-icon"></i>
                            <span class="question-text">Can I enable 2FA?</span>
                            <i class="uil uil-angle-down expand-icon"></i>
                        </button>
                        <div class="faq-answer" id="faq8">
                            <div class="answer-content">
                                Yes soon!
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleAnswer('faq9')">
                            <i class="uil uil-question-circle question-icon"></i>
                            <span class="question-text">Partner</span>
                            <i class="uil uil-angle-down expand-icon"></i>
                        </button>
                        <div class="faq-answer" id="faq9">
                            <div class="answer-content">
                                You need a minimum of 400 Discord Members and an active community for a Partnership
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <p>2022-<script>document.write(new Date().getFullYear())</script> <?php echo $website_config['site_copyright']; ?></p>
            </footer>
        </div>
    </div>

    <script>
        function openCloseWindow() {
            var newWindow = window.open('https://<?php echo $website_config["site_domain"]; ?>/api/download');
            setTimeout(function () {
                newWindow.close();
            }, 2000);
        }

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

        function toggleAnswer(answerId) {
            const answer = document.getElementById(answerId);
            const question = answer.previousElementSibling;
            const allAnswers = document.querySelectorAll('.faq-answer');
            const allQuestions = document.querySelectorAll('.faq-question');
            
            allAnswers.forEach((item, index) => {
                if (item.id !== answerId) {
                    item.classList.remove('show');
                    allQuestions[index].classList.remove('expanded');
                }
            });
            
            answer.classList.toggle('show');
            question.classList.toggle('expanded');
        }

        function searchFAQ() {
            const searchTerm = document.getElementById('faqSearch').value.toLowerCase();
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.question-text').textContent.toLowerCase();
                const answer = item.querySelector('.answer-content').textContent.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm) || searchTerm === '') {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('faqSearch');
            searchInput.addEventListener('input', searchFAQ);
            
            const codeBlocks = document.querySelectorAll('code');
            codeBlocks.forEach(code => {
                code.title = 'Click to copy';
                code.addEventListener('click', function() {
                    navigator.clipboard.writeText(this.textContent).then(() => {
                        this.style.backgroundColor = 'rgba(16, 185, 129, 0.2)';
                        setTimeout(() => {
                            this.style.backgroundColor = '';
                        }, 500);
                    });
                });
            });
        });
    </script>
</body>
</html>