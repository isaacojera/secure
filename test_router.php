<?php
session_start();
require 'config.php';
require 'routeros_api.php';

$api = new RouterosAPI();

// Fetch all routers for current station
$routers = $conn->query("SELECT * FROM routers WHERE station_id=" . $_SESSION['station_id']);

while ($router = $routers->fetch_assoc()) {
    $routerId = $router['id'];
    $wgIp     = $router['wg_ip'];      // WireGuard IP
    $user     = $router['api_username'];
    $pass     = $router['api_password'];

    $status = 'offline';

    if ($api->connect($wgIp, $user, $pass)) {
        // Test by fetching system identity
        $response = $api->comm('/system/identity/print');
        if (!empty($response)) {
            $status = 'online';
        }
        $api->disconnect();
    }

    // Update router status in DB
    $stmt = $conn->prepare("UPDATE routers SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $routerId);
    $stmt->execute();

    echo "Router {$router['router_name']} ({$wgIp}) status: {$status}\n";
}

echo "All routers checked.\n";
?>
