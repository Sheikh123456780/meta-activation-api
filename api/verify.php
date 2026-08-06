<?php
header('Content-Type: application/json');

// === DATABASE - GitHub RAW file se ===
$db_url = "https://raw.githubusercontent.com/Sheikh123456780/meta-activation-api/refs/heads/main/api/database.json";
$db = json_decode(file_get_contents($db_url), true);

// === INPUT LE ===
$user_key = $_POST['user_key'] ?? $_GET['user_key'] ?? '';
$package_name = $_POST['package_name'] ?? $_GET['package_name'] ?? '';
$device_id = $_POST['device_id'] ?? $_GET['device_id'] ?? '';

if (empty($user_key)) {
    echo json_encode(["status" => "error", "reason" => "User key required"]);
    exit;
}

// === KEY CHECK KARO ===
$found = false;
$response = [
    "status" => "error",
    "reason" => "Invalid key",
    "server_mode" => "online"
];

foreach ($db['keys'] as $key) {
    if ($key['key'] === $user_key) {
        $found = true;
        
        // Check if banned
        if (isset($key['banned']) && $key['banned']) {
            $response = [
                "status" => "error",
                "reason" => "Key banned",
                "server_mode" => "online"
            ];
            break;
        }
        
        // Check expiry
        $expiry = $key['expiry'] ?? '';
        if (!empty($expiry)) {
            $expiry_time = strtotime($expiry);
            if ($expiry_time < time()) {
                $response = [
                    "status" => "error",
                    "reason" => "Key expired on $expiry",
                    "server_mode" => "online"
                ];
                break;
            }
        }
        
        // SUCCESS
        $response = [
            "status" => "success",
            "expiry" => $expiry,
            "message" => "Activated",
            "server_mode" => "online",
            "toggle_expiry" => 1,
            "feature1" => $key['daemon'] ?? 0,
            "feature2" => $key['hide_root'] ?? 0,
            "user_name" => $key['name'] ?? "User"
        ];
        break;
    }
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
