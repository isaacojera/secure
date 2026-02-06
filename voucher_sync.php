<?php
require 'config.php';
require 'mikrotik_api.php';

// Get all routers for all stations
$routers = $conn->query("SELECT * FROM routers");

while($r = $routers->fetch_assoc()) {

    $api = new RouterosAPI();

    if (!$api->connect($r['vpn_ip'], $r['api_username'], $r['api_password'])) {
        echo "Router {$r['router_name']} ({$r['vpn_ip']}) connection failed\n";
        continue;
    }

    // Fetch active hotspot users
    $api->comm("/ip/hotspot/active/print");
    $response = $api->read();
    $active_users = $api->parseResponse($response);

    $active_usernames = array_column($active_users, 'user');

    // Mark all vouchers as unused first
    $conn->query("UPDATE vouchers SET status='unused' WHERE router_id={$r['id']}");

    // Update vouchers used
    if(!empty($active_usernames)) {
        $in  = "'" . implode("','", $active_usernames) . "'";
        $conn->query("UPDATE vouchers SET status='used' WHERE router_id={$r['id']} AND username IN ($in)");
    }

    $api->disconnect();
    echo "Router {$r['router_name']} synced. Active users: " . count($active_usernames) . "\n";
}

//* * * * * /usr/bin/php /var/www/html/voucher_sync.php >> /var/log/voucher_sync.log 2>&1