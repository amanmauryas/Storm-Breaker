<?php

// Set the content type to JSON.
header('Content-Type: application/json');

// Define the directory where your templates are stored.
$templateDir = 'templates';
$links = [];

// Error Handling: Check if the templates directory exists.
if (!is_dir($templateDir)) {
    echo json_encode([]);
    exit();
}

// --- Determine the Base URL of your server (UPDATED LOGIC) ---

// 1. Determine the protocol (http or https).
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";

// 2. Get the host name.
// **THIS IS THE MODIFIED PART**
// Check if the request is coming through a proxy like ngrok.
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    // If it is, use the host provided by the proxy.
    $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
} else {
    // Otherwise, use the standard host name.
    $host = $_SERVER['HTTP_HOST'];
}

// 3. Get the path to the directory containing this script.
$path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

// 4. Combine them to create the full base URL.
$baseUrl = "{$protocol}://{$host}{$path}";

// --- Scan the directory for templates and build the links array ---
$items = scandir($templateDir);

foreach ($items as $item) {
    if ($item === '.' || $item === '..') {
        continue;
    }
    
    $fullPath = $templateDir . '/' . $item;
    if (is_dir($fullPath)) {
        // Create a user-friendly name from the directory name.
        $formattedName = ucfirst(str_replace('-', ' ', $item));

        // Construct the full, shareable URL for the template.
        $fullUrl = "{$baseUrl}/{$fullPath}/";

        // Add the structured data to our links array.
        $links[] = [
            'name' => $formattedName,
            'url'  => $fullUrl,
        ];
    }
}

// --- Output the final result ---
echo json_encode($links, JSON_PRETTY_PRINT);

?>