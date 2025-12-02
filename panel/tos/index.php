<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FiveSecurity | Terms of Service</title>
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

        .tos-header {
          text-align: center;
          margin-bottom: 2rem;
          padding-bottom: 2rem;
          border-bottom: 1px solid var(--border);
        }

        .tos-header h1 {
          font-size: 2.5rem;
          font-weight: 700;
          color: var(--text-primary);
          margin-bottom: 0.5rem;
        }

        .tos-header .subtitle {
          font-size: 1rem;
          color: var(--text-secondary);
          margin-bottom: 0.5rem;
        }

        .tos-header .last-updated {
          font-size: 0.875rem;
          color: #64748b;
        }

        .table-of-contents {
          background: var(--bg-card);
          border: 1px solid var(--border);
          border-radius: 8px;
          padding: 1.5rem;
          margin-bottom: 2rem;
        }

        .table-of-contents h2 {
          font-size: 1.25rem;
          font-weight: 600;
          color: var(--text-primary);
          margin-bottom: 1rem;
        }

        .toc-list {
          list-style: decimal;
          color: var(--text-secondary);
          margin-left: 1.5rem;
        }

        .toc-list li {
          margin-bottom: 0.5rem;
        }

        .toc-list a {
          color: var(--primary);
          text-decoration: none;
          font-size: 0.875rem;
          transition: all 0.2s ease;
        }

        .toc-list a:hover {
          text-decoration: underline;
        }

        .tos-section {
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

        .tos-text {
          color: var(--text-secondary);
          line-height: 1.6;
          margin-bottom: 1rem;
          font-size: 1rem;
        }

        .tos-text strong {
          color: var(--text-primary);
        }

        .tos-text em {
          color: var(--primary);
        }

        .tos-list {
          color: var(--text-secondary);
          margin: 1rem 0;
          padding-left: 1.5rem;
        }

        .tos-list li {
          margin-bottom: 0.75rem;
          font-size: 1rem;
          line-height: 1.5;
        }

        .tos-link {
          color: var(--primary);
          text-decoration: none;
        }

        .tos-link:hover {
          text-decoration: underline;
        }

        .warning-box {
          background: rgba(239, 68, 68, 0.1);
          border: 1px solid #ef4444;
          border-radius: 8px;
          padding: 1.5rem;
          margin: 1.5rem 0;
        }

        .warning-box .tos-text {
          color: #fecaca;
          margin-bottom: 0;
        }

        .important-box {
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
          .tos-header h1 { font-size: 2rem; }
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
                        <a href="#" class="nav-link active">
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
                        <a href="#" class="nav-link">
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
                    <div class="header-title">Terms of Service</div>
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
                <div class="tos-header">
                    <h1>TERMS OF SERVICE</h1>
                    <div class="subtitle">Legal agreement governing your use of FiveSecurity services</div>
                    <div class="last-updated">Last updated: January 28, 2023</div>
                </div>

                <div class="table-of-contents">
                    <h2>TABLE OF CONTENTS</h2>
                    <ol class="toc-list">
                        <li><a href="#agreement">AGREEMENT TO TERMS</a></li>
                        <li><a href="#ip">INTELLECTUAL PROPERTY RIGHTS</a></li>
                        <li><a href="#userreps">USER REPRESENTATIONS</a></li>
                        <li><a href="#userreg">USER REGISTRATION</a></li>
                        <li><a href="#prohibited">PROHIBITED ACTIVITIES</a></li>
                        <li><a href="#ugc">USER GENERATED CONTRIBUTIONS</a></li>
                        <li><a href="#license">CONTRIBUTION LICENSE</a></li>
                        <li><a href="#submissions">SUBMISSIONS</a></li>
                        <li><a href="#sitemanage">SITE MANAGEMENT</a></li>
                        <li><a href="#privacypolicy2">PRIVACY POLICY</a></li>
                        <li><a href="#terms">TERM AND TERMINATION</a></li>
                        <li><a href="#modifications">MODIFICATIONS AND INTERRUPTIONS</a></li>
                        <li><a href="#law">GOVERNING LAW</a></li>
                        <li><a href="#disputes">DISPUTE RESOLUTION</a></li>
                        <li><a href="#corrections">CORRECTIONS</a></li>
                        <li><a href="#disclaimer">DISCLAIMER</a></li>
                        <li><a href="#liability">LIMITATIONS OF LIABILITY</a></li>
                        <li><a href="#indemnification">INDEMNIFICATION</a></li>
                        <li><a href="#userdata">USER DATA</a></li>
                        <li><a href="#electronic">ELECTRONIC COMMUNICATIONS, TRANSACTIONS, AND SIGNATURES</a></li>
                        <li><a href="#california">CALIFORNIA USERS AND RESIDENTS</a></li>
                        <li><a href="#misc">MISCELLANEOUS</a></li>
                        <li><a href="#contact">CONTACT US</a></li>
                    </ol>
                </div>

                <div class="tos-section" id="agreement">
                    <h2 class="section-title">1. AGREEMENT TO TERMS</h2>
                    
                    <p class="tos-text">These Terms of Use constitute a legally binding agreement made between you, whether personally or on behalf of an entity ("you") and <strong>FiveSecurity</strong> ("<strong>Company</strong>," "<strong>we</strong>," "<strong>us</strong>," or "<strong>our</strong>"), concerning your access to and use of the <a href="https://FiveSecurity.net" class="tos-link" target="_blank">https://FiveSecurity.net</a> website as well as any other media form, media channel, mobile website or mobile application related, linked, or otherwise connected thereto (collectively, the "Site").</p>
                    
                    <p class="tos-text">You agree that by accessing the Site, you have read, understood, and agreed to be bound by all of these Terms of Use. <strong>IF YOU DO NOT AGREE WITH ALL OF THESE TERMS OF USE, THEN YOU ARE EXPRESSLY PROHIBITED FROM USING THE SITE AND YOU MUST DISCONTINUE USE IMMEDIATELY.</strong></p>
                    
                    <div class="warning-box">
                        <p class="tos-text"><strong>Important Notice:</strong> We reserve the right to revoke your FiveSecurity license or even close our project at any time without any reason. We reserve the right to revoke any license without justification. It is not allowed to resell or redistribute products or product keys / FiveSecurity. It is not allowed to share the product files with 3rd parties. We are not obliged to help with the configuration or installation of FiveSecurity. If the money is withdrawn or the money is withdrawn by Tebex, we reserve the right to block all your FiveSecurity Acc including the Panel, and Discord account (License too + blacklist).</p>
                    </div>
                    
                    <p class="tos-text">Supplemental terms and conditions or documents that may be posted on the Site from time to time are hereby expressly incorporated herein by reference. We reserve the right, in our sole discretion, to make changes or modifications to these Terms of Use from time to time.</p>
                </div>

                <div class="tos-section" id="ip">
                    <h2 class="section-title">2. INTELLECTUAL PROPERTY RIGHTS</h2>
                    
                    <p class="tos-text">Unless otherwise indicated, the Site is our proprietary property and all source code, databases, functionality, software, website designs, audio, video, text, photographs, and graphics on the Site (collectively, the "Content") and the trademarks, service marks, and logos contained therein (the "Marks") are owned or controlled by us or licensed to us, and are protected by copyright and trademark laws and various other intellectual property rights and unfair competition laws of the United States, international copyright laws, and international conventions.</p>
                    
                    <p class="tos-text">Provided that you are eligible to use the Site, you are granted a limited license to access and use the Site and to download or print a copy of any portion of the Content to which you have properly gained access solely for your personal, non-commercial use. We reserve all rights not expressly granted to you in and to the Site, the Content and the Marks.</p>
                </div>

                <div class="tos-section" id="userreps">
                    <h2 class="section-title">3. USER REPRESENTATIONS</h2>
                    
                    <p class="tos-text">By using the Site, you represent and warrant that:</p>
                    
                    <ul class="tos-list">
                        <li>All registration information you submit will be true, accurate, current, and complete</li>
                        <li>You will maintain the accuracy of such information and promptly update such registration information as necessary</li>
                        <li>You have the legal capacity and you agree to comply with these Terms of Use</li>
                        <li>You are not a minor in the jurisdiction in which you reside</li>
                        <li>You will not access the Site through automated or non-human means, whether through a bot, script, or otherwise</li>
                        <li>You will not use the Site for any illegal or unauthorized purpose</li>
                        <li>Your use of the Site will not violate any applicable law or regulation</li>
                    </ul>
                    
                    <p class="tos-text">If you provide any information that is untrue, inaccurate, not current, or incomplete, we have the right to suspend or terminate your account and refuse any and all current or future use of the Site (or any portion thereof).</p>
                </div>

                <div class="tos-section" id="userreg">
                    <h2 class="section-title">4. USER REGISTRATION</h2>
                    
                    <p class="tos-text">You may be required to register with the Site. You agree to keep your password confidential and will be responsible for all use of your account and password. We reserve the right to remove, reclaim, or change a username you select if we determine, in our sole discretion, that such username is inappropriate, obscene, or otherwise objectionable.</p>
                </div>

                <div class="tos-section" id="prohibited">
                    <h2 class="section-title">5. PROHIBITED ACTIVITIES</h2>
                    
                    <p class="tos-text">You may not access or use the Site for any purpose other than that for which we make the Site available. The Site may not be used in connection with any commercial endeavors except those that are specifically endorsed or approved by us.</p>
                    
                    <p class="tos-text">As a user of the Site, you agree not to:</p>
                    
                    <ul class="tos-list">
                        <li>Systematically retrieve data or other content from the Site to create or compile, directly or indirectly, a collection, compilation, database, or directory without written permission from us</li>
                        <li>Trick, defraud, or mislead us and other users, especially in any attempt to learn sensitive account information such as user passwords</li>
                        <li>Circumvent, disable, or otherwise interfere with security-related features of the Site</li>
                        <li>Disparage, tarnish, or otherwise harm, in our opinion, us and/or the Site</li>
                        <li>Use any information obtained from the Site in order to harass, abuse, or harm another person</li>
                        <li>Make improper use of our support services or submit false reports of abuse or misconduct</li>
                        <li>Use the Site in a manner inconsistent with any applicable laws or regulations</li>
                        <li>Engage in unauthorized framing of or linking to the Site</li>
                        <li>Upload or transmit viruses, Trojan horses, or other material that interferes with any party's uninterrupted use and enjoyment of the Site</li>
                        <li>Engage in any automated use of the system, such as using scripts to send comments or messages</li>
                        <li>Delete the copyright or other proprietary rights notice from any Content</li>
                        <li>Attempt to impersonate another user or person or use the username of another user</li>
                        <li>Interfere with, disrupt, or create an undue burden on the Site or the networks or services connected to the Site</li>
                        <li>Copy or adapt the Site's software, including but not limited to Flash, PHP, HTML, JavaScript, or other code</li>
                        <li>Use the Site as part of any effort to compete with us or otherwise use the Site and/or the Content for any revenue-generating endeavor or commercial enterprise</li>
                    </ul>
                </div>

                <div class="tos-section" id="sitemanage">
                    <h2 class="section-title">9. SITE MANAGEMENT</h2>
                    
                    <p class="tos-text">We reserve the right, but not the obligation, to: (1) monitor the Site for violations of these Terms of Use; (2) take appropriate legal action against anyone who, in our sole discretion, violates the law or these Terms of Use; (3) refuse, restrict access to, limit the availability of, or disable any of your Contributions; (4) remove from the Site or otherwise disable all files and content that are excessive in size or are in any way burdensome to our systems; and (5) otherwise manage the Site in a manner designed to protect our rights and property and to facilitate the proper functioning of the Site.</p>
                </div>

                <div class="tos-section" id="terms">
                    <h2 class="section-title">11. TERM AND TERMINATION</h2>
                    
                    <div class="warning-box">
                        <p class="tos-text"><strong>TERMINATION RIGHTS:</strong> WITHOUT LIMITING ANY OTHER PROVISION OF THESE TERMS OF USE, WE RESERVE THE RIGHT TO, IN OUR SOLE DISCRETION AND WITHOUT NOTICE OR LIABILITY, DENY ACCESS TO AND USE OF THE SITE (INCLUDING BLOCKING CERTAIN IP ADDRESSES), TO ANY PERSON FOR ANY REASON OR FOR NO REASON, INCLUDING WITHOUT LIMITATION FOR BREACH OF ANY REPRESENTATION, WARRANTY, OR COVENANT CONTAINED IN THESE TERMS OF USE OR OF ANY APPLICABLE LAW OR REGULATION.</p>
                    </div>
                    
                    <p class="tos-text">If we terminate or suspend your account for any reason, you are prohibited from registering and creating a new account under your name, a fake or borrowed name, or the name of any third party, even if you may be acting on behalf of the third party.</p>
                </div>

                <div class="tos-section" id="law">
                    <h2 class="section-title">13. GOVERNING LAW</h2>
                    
                    <p class="tos-text">These conditions are governed by and interpreted following the laws of <strong>Germany</strong>, and the use of the United Nations Convention of Contracts for the International Sale of Goods is expressly excluded. If your habitual residence is in the EU, and you are a consumer, you additionally possess the protection provided to you by obligatory provisions of the law of your country of residence.</p>
                </div>

                <div class="tos-section" id="disclaimer">
                    <h2 class="section-title">16. DISCLAIMER</h2>
                    
                    <div class="warning-box">
                        <p class="tos-text"><strong>THE SITE IS PROVIDED ON AN AS-IS AND AS-AVAILABLE BASIS.</strong> YOU AGREE THAT YOUR USE OF THE SITE AND OUR SERVICES WILL BE AT YOUR SOLE RISK. TO THE FULLEST EXTENT PERMITTED BY LAW, WE DISCLAIM ALL WARRANTIES, EXPRESS OR IMPLIED, IN CONNECTION WITH THE SITE AND YOUR USE THEREOF.</p>
                    </div>
                </div>

                <div class="tos-section" id="contact">
                    <h2 class="section-title">23. CONTACT US</h2>
                    
                    <div class="contact-box">
                        <p class="tos-text">In order to resolve a complaint regarding the Site or to receive further information regarding use of the Site, please contact us at:</p>
                        
                        <p class="tos-text"><strong>FiveSecurity</strong><br>
                        Germany<br>
                        <a href="mailto:contact@FiveSecurity.net" class="tos-link">contact@FiveSecurity.net</a></p>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <p>2022-<script>document.write(new Date().getFullYear())</script> FiveSecurity LLC. All rights reserved.</p>
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

        // Smooth scrolling for table of contents links
        document.querySelectorAll('.toc-list a').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>