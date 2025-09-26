<?php

// Set the content type to JSON, so the browser knows how to interpret the response.
header('Content-Type: application/json');

// Define the directory where your phishing kit templates are stored.
$templateDir = 'templates';
$links = [];

// --- Error Handling: Check if the templates directory actually exists ---
if (!is_dir($templateDir)) {
    // If the directory doesn't exist, return an empty JSON array.
    // This prevents errors on the frontend.
    echo json_encode([]);
    exit();
}

// --- Determine the Base URL of your server ---
// 1. Determine the protocol (http or https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";

// 2. Get the host name
$host = $_SERVER['HTTP_HOST'];

// 3. Get the path to the directory containing this script
$path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

// 4. Combine them to create the full base URL
$baseUrl = "{$protocol}://{$host}{$path}";

// --- Scan the directory for templates and build the links array ---
// scandir() lists all files and directories, including '.' and '..'
$items = scandir($templateDir);

foreach ($items as $item) {
    // Ignore the current ('.') and parent ('..') directory entries.
    // Also, ensure the item is a directory, not just a random file.
    if ($item === '.' || $item === '..') {
        continue;
    }
    
    $fullPath = $templateDir . '/' . $item;
    if (is_dir($fullPath)) {
        // The item is a valid template directory.

        // Create a user-friendly name from the directory name.
        // E.g., "facebook-mobile" becomes "Facebook mobile".
        $formattedName = ucfirst(str_replace('-', ' ', $item));

        // Construct the full, shareable URL for the template.
        $fullUrl = "{$baseUrl}/{$fullPath}/"; // The trailing slash is important.

        // Add the structured data to our links array.
        $links[] = [
            'name' => $formattedName,
            'url'  => $fullUrl,
        ];
    }
}

// --- Output the final result ---
// Encode the array of links into a JSON string and print it.
// JSON_PRETTY_PRINT makes it easier to read if you open the file directly in a browser.
echo json_encode($links, JSON_PRETTY_PRINT);

?>