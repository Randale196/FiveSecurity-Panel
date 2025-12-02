<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FiveSecurity | Privacy Policy</title>
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

        .privacy-header {
          text-align: center;
          margin-bottom: 2rem;
          padding-bottom: 2rem;
          border-bottom: 1px solid var(--border);
        }

        .privacy-header h1 {
          font-size: 2.5rem;
          font-weight: 700;
          color: var(--text-primary);
          margin-bottom: 0.5rem;
        }

        .privacy-header .subtitle {
          font-size: 1rem;
          color: var(--text-secondary);
          margin-bottom: 0.5rem;
        }

        .privacy-header .last-updated {
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
          list-style: none;
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

        .privacy-section {
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

        .privacy-text {
          color: var(--text-secondary);
          line-height: 1.6;
          margin-bottom: 1rem;
        }

        .privacy-text strong {
          color: var(--text-primary);
        }

        .privacy-text em {
          color: var(--primary);
        }

        .privacy-list {
          color: var(--text-secondary);
          margin: 1rem 0;
          padding-left: 1.5rem;
        }

        .privacy-list li {
          margin-bottom: 0.5rem;
        }

        .privacy-link {
          color: var(--primary);
          text-decoration: none;
        }

        .privacy-link:hover {
          text-decoration: underline;
        }

        .highlight-box {
          background: rgba(59, 130, 246, 0.1);
          border: 1px solid var(--primary);
          border-radius: 8px;
          padding: 1rem;
          margin: 1rem 0;
        }

        .contact-info {
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
          .privacy-header h1 { font-size: 2rem; }
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
                        <a href="#" class="nav-link">
                            <i class="uil uil-credit-card"></i>
                            <span>Refund</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link active">
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
                    <div class="header-title">Privacy Policy</div>
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
                <div class="privacy-header">
                    <h1>PRIVACY NOTICE</h1>
                    <div class="subtitle">This privacy notice explains how FiveSecurity collects, uses, and protects your information</div>
                    <div class="last-updated">Last updated: January 28, 2023</div>
                </div>

                <div class="table-of-contents">
                    <h2>TABLE OF CONTENTS</h2>
                    <ol class="toc-list">
                        <li><a href="#section1">1. WHAT INFORMATION DO WE COLLECT?</a></li>
                        <li><a href="#section2">2. HOW DO WE PROCESS YOUR INFORMATION?</a></li>
                        <li><a href="#section3">3. WHAT LEGAL BASES DO WE RELY ON TO PROCESS YOUR PERSONAL INFORMATION?</a></li>
                        <li><a href="#section4">4. WHEN AND WITH WHOM DO WE SHARE YOUR PERSONAL INFORMATION?</a></li>
                        <li><a href="#section5">5. DO WE USE COOKIES AND OTHER TRACKING TECHNOLOGIES?</a></li>
                        <li><a href="#section6">6. HOW LONG DO WE KEEP YOUR INFORMATION?</a></li>
                        <li><a href="#section7">7. HOW DO WE KEEP YOUR INFORMATION SAFE?</a></li>
                        <li><a href="#section8">8. WHAT ARE YOUR PRIVACY RIGHTS?</a></li>
                        <li><a href="#section9">9. CONTROLS FOR DO-NOT-TRACK FEATURES</a></li>
                        <li><a href="#section10">10. DO CALIFORNIA RESIDENTS HAVE SPECIFIC PRIVACY RIGHTS?</a></li>
                        <li><a href="#section11">11. DO WE MAKE UPDATES TO THIS NOTICE?</a></li>
                        <li><a href="#section12">12. HOW CAN YOU CONTACT US ABOUT THIS NOTICE?</a></li>
                        <li><a href="#section13">13. HOW CAN YOU REVIEW, UPDATE, OR DELETE THE DATA WE COLLECT FROM YOU?</a></li>
                    </ol>
                </div>

                <div class="highlight-box">
                    <p class="privacy-text"><strong>Summary:</strong> This privacy notice for <strong>FiveSecurity</strong> describes how and why we might collect, store, use, and/or share your information when you use our services, such as when you visit our website at <a href="https://panel.fivesecurity.de" class="privacy-link">https://panel.fivesecurity.de</a> or engage with us in other related ways.</p>
                    <p class="privacy-text"><strong>Questions or concerns?</strong> Reading this privacy notice will help you understand your privacy rights and choices. If you do not agree with our policies and practices, please do not use our Services. If you still have any questions or concerns, please contact us at <a href="mailto:contact@fivesecurity.de" class="privacy-link">contact@fivesecurity.de</a>.</p>
                </div>

                <div class="privacy-section" id="section1">
                    <h2 class="section-title">1. WHAT INFORMATION DO WE COLLECT?</h2>
                    
                    <h3 class="section-subtitle">Personal information you disclose to us</h3>
                    <p class="privacy-text"><strong><em>In Short:</em></strong> <em>We collect personal information that you provide to us.</em></p>
                    
                    <p class="privacy-text">We collect personal information that you voluntarily provide to us when you register on the Services, express an interest in obtaining information about us or our products and Services, when you participate in activities on the Services, or otherwise when you contact us.</p>
                    
                    <p class="privacy-text"><strong>Personal Information Provided by You.</strong> The personal information that we collect depends on the context of your interactions with us and the Services, the choices you make, and the products and features you use. The personal information we collect may include the following:</p>
                    
                    <ul class="privacy-list">
                        <li>usernames</li>
                        <li>passwords</li>
                        <li>email addresses</li>
                    </ul>
                    
                    <p class="privacy-text"><strong>Sensitive Information.</strong> We do not process sensitive information.</p>
                    
                    <p class="privacy-text">All personal information that you provide to us must be true, complete, and accurate, and you must notify us of any changes to such personal information.</p>
                </div>

                <div class="privacy-section" id="section2">
                    <h2 class="section-title">2. HOW DO WE PROCESS YOUR INFORMATION?</h2>
                    
                    <p class="privacy-text"><strong><em>In Short:</em></strong> <em>We process your information to provide, improve, and administer our Services, communicate with you, for security and fraud prevention, and to comply with law. We may also process your information for other purposes with your consent.</em></p>
                    
                    <p class="privacy-text"><strong>We process your personal information for a variety of reasons, depending on how you interact with our Services, including:</strong></p>
                    
                    <ul class="privacy-list">
                        <li><strong>To facilitate account creation and authentication and otherwise manage user accounts.</strong> We may process your information so you can create and log in to your account, as well as keep your account in working order.</li>
                        <li><strong>To save or protect an individual's vital interest.</strong> We may process your information when necessary to save or protect an individual's vital interest, such as to prevent harm.</li>
                    </ul>
                </div>

                <div class="privacy-section" id="section3">
                    <h2 class="section-title">3. WHAT LEGAL BASES DO WE RELY ON TO PROCESS YOUR INFORMATION?</h2>
                    
                    <p class="privacy-text"><strong><em>In Short:</em></strong> <em>We only process your personal information when we believe it is necessary and we have a valid legal reason (i.e., legal basis) to do so under applicable law, like with your consent, to comply with laws, to provide you with services to enter into or fulfill our contractual obligations, to protect your rights, or to fulfill our legitimate business interests.</em></p>
                    
                    <p class="privacy-text"><strong><em>If you are located in the EU or UK, this section applies to you.</em></strong></p>
                    
                    <p class="privacy-text">The General Data Protection Regulation (GDPR) and UK GDPR require us to explain the valid legal bases we rely on in order to process your personal information. As such, we may rely on the following legal bases to process your personal information:</p>
                    
                    <ul class="privacy-list">
                        <li><strong>Consent.</strong> We may process your information if you have given us permission (i.e., consent) to use your personal information for a specific purpose. You can withdraw your consent at any time.</li>
                        <li><strong>Legal Obligations.</strong> We may process your information where we believe it is necessary for compliance with our legal obligations, such as to cooperate with a law enforcement body or regulatory agency, exercise or defend our legal rights, or disclose your information as evidence in litigation in which we are involved.</li>
                        <li><strong>Vital Interests.</strong> We may process your information where we believe it is necessary to protect your vital interests or the vital interests of a third party, such as situations involving potential threats to the safety of any person.</li>
                    </ul>
                </div>

                <div class="privacy-section" id="section4">
                    <h2 class="section-title">4. WHEN AND WITH WHOM DO WE SHARE YOUR PERSONAL INFORMATION?</h2>
                    
                    <p class="privacy-text"><strong><em>In Short:</em></strong> <em>We may share information in specific situations described in this section and/or with the following third parties.</em></p>
                    
                    <p class="privacy-text">We may need to share your personal information in the following situations:</p>
                    
                    <ul class="privacy-list">
                        <li><strong>Business Transfers.</strong> We may share or transfer your information in connection with, or during negotiations of, any merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company.</li>
                    </ul>
                </div>

                <div class="privacy-section" id="section5">
                    <h2 class="section-title">5. DO WE USE COOKIES AND OTHER TRACKING TECHNOLOGIES?</h2>
                    
                    <p class="privacy-text"><strong><em>In Short:</em></strong> <em>We may use cookies and other tracking technologies to collect and store your information.</em></p>
                    
                    <p class="privacy-text">We may use cookies and similar tracking technologies (like web beacons and pixels) to access or store information. Specific information about how we use such technologies and how you can refuse certain cookies is set out in our Cookie Notice.</p>
                </div>

                <div class="privacy-section" id="section6">
                    <h2 class="section-title">6. HOW LONG DO WE KEEP YOUR INFORMATION?</h2>
                    
                    <p class="privacy-text"><strong><em>In Short:</em></strong> <em>We keep your information for as long as necessary to fulfill the purposes outlined in this privacy notice unless otherwise required by law.</em></p>
                    
                    <p class="privacy-text">We will only keep your personal information for as long as it is necessary for the purposes set out in this privacy notice, unless a longer retention period is required or permitted by law (such as tax, accounting, or other legal requirements). No purpose in this notice will require us keeping your personal information for longer than the period of time in which users have an account with us.</p>
                    
                    <p class="privacy-text">When we have no ongoing legitimate business need to process your personal information, we will either delete or anonymize such information, or, if this is not possible (for example, because your personal information has been stored in backup archives), then we will securely store your personal information and isolate it from any further processing until deletion is possible.</p>
                </div>

                <div class="privacy-section" id="section7">
                    <h2 class="section-title">7. HOW DO WE KEEP YOUR INFORMATION SAFE?</h2>
                    
                    <p class="privacy-text"><strong><em>In Short:</em></strong> <em>We aim to protect your personal information through a system of organizational and technical security measures.</em></p>
                    
                    <p class="privacy-text">We have implemented appropriate and reasonable technical and organizational security measures designed to protect the security of any personal information we process. However, despite our safeguards and efforts to secure your information, no electronic transmission over the Internet or information storage technology can be guaranteed to be 100% secure, so we cannot promise or guarantee that hackers, cybercriminals, or other unauthorized third parties will not be able to defeat our security and improperly collect, access, steal, or modify your information. Although we will do our best to protect your personal information, transmission of personal information to and from our Services is at your own risk. You should only access the Services within a secure environment.</p>
                </div>

                <div class="privacy-section" id="section8">
                    <h2 class="section-title">8. WHAT ARE YOUR PRIVACY RIGHTS?</h2>
                    
                    <p class="privacy-text"><strong><em>In Short:</em></strong> <em>In some regions, such as the European Economic Area (EEA) and United Kingdom (UK), you have rights that allow you greater access to and control over your personal information. You may review, change, or terminate your account at any time.</em></p>
                    
                    <p class="privacy-text">In some regions (like the EEA and UK), you have certain rights under applicable data protection laws. These may include the right (i) to request access and obtain a copy of your personal information, (ii) to request rectification or erasure; (iii) to restrict the processing of your personal information; and (iv) if applicable, to data portability. In certain circumstances, you may also have the right to object to the processing of your personal information.</p>
                    
                    <p class="privacy-text">If you are located in the EEA or UK and you believe we are unlawfully processing your personal information, you also have the right to complain to your local data protection supervisory authority.</p>
                    
                    <h3 class="section-subtitle">Withdrawing your consent</h3>
                    <p class="privacy-text">If we are relying on your consent to process your personal information, which may be express and/or implied consent depending on the applicable law, you have the right to withdraw your consent at any time.</p>
                    
                    <h3 class="section-subtitle">Account Information</h3>
                    <p class="privacy-text">If you would at any time like to review or change the information in your account or terminate your account, you can contact us using the contact information provided.</p>
                </div>

                <div class="privacy-section" id="section9">
                    <h2 class="section-title">9. CONTROLS FOR DO-NOT-TRACK FEATURES</h2>
                    
                    <p class="privacy-text">Most web browsers and some mobile operating systems and mobile applications include a Do-Not-Track ("DNT") feature or setting you can activate to signal your privacy preference not to have data about your online browsing activities monitored and collected. At this stage no uniform technology standard for recognizing and implementing DNT signals has been finalized. As such, we do not currently respond to DNT browser signals or any other mechanism that automatically communicates your choice not to be tracked online.</p>
                </div>

                <div class="privacy-section" id="section10">
                    <h2 class="section-title">10. DO CALIFORNIA RESIDENTS HAVE SPECIFIC PRIVACY RIGHTS?</h2>
                    
                    <p class="privacy-text"><strong><em>In Short:</em></strong> <em>Yes, if you are a resident of California, you are granted specific rights regarding access to your personal information.</em></p>
                    
                    <p class="privacy-text">California Civil Code Section 1798.83, also known as the "Shine The Light" law, permits our users who are California residents to request and obtain from us, once a year and free of charge, information about categories of personal information (if any) we disclosed to third parties for direct marketing purposes and the names and addresses of all third parties with which we shared personal information in the immediately preceding calendar year.</p>
                </div>

                <div class="privacy-section" id="section11">
                    <h2 class="section-title">11. DO WE MAKE UPDATES TO THIS NOTICE?</h2>
                    
                    <p class="privacy-text"><strong><em>In Short:</em></strong> <em>Yes, we will update this notice as necessary to stay compliant with relevant laws.</em></p>
                    
                    <p class="privacy-text">We may update this privacy notice from time to time. The updated version will be indicated by an updated "Revised" date and the updated version will be effective as soon as it is accessible. If we make material changes to this privacy notice, we may notify you either by prominently posting a notice of such changes or by directly sending you a notification.</p>
                </div>

                <div class="privacy-section" id="section12">
                    <h2 class="section-title">12. HOW CAN YOU CONTACT US ABOUT THIS NOTICE?</h2>
                    
                    <div class="contact-info">
                        <p class="privacy-text">If you have questions or comments about this notice, you may email us at <a href="mailto:contact@fivesecurity.de" class="privacy-link">contact@fivesecurity.de</a> or by post to:</p>
                        
                        <p class="privacy-text"><strong>FiveSecurity.</strong><br>
                        Germany</p>
                    </div>
                </div>

                <div class="privacy-section" id="section13">
                    <h2 class="section-title">13. HOW CAN YOU REVIEW, UPDATE, OR DELETE THE DATA WE COLLECT FROM YOU?</h2>
                    
                    <p class="privacy-text">Based on the applicable laws of your country, you may have the right to request access to the personal information we collect from you, change that information, or delete it. To request to review, update, or delete your personal information, please submit a request form by clicking <a href="https://panel.fivesecurity.de/discord" class="privacy-link" target="_blank">here</a>.</p>
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