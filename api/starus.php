<?php
header('Content-Type: application/json');

echo json_encode([
    "status" => "success",
    "server_mode" => "online",
    "message" => "Server is running",
    "version" => "1.0.0",
    "maintenance" => false
]);
?>
