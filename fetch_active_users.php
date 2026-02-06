<?php
require 'mikrotik_api.php';

$ip   = $_POST['vpn_ip'];
$user = $_POST['api_username'];
$pass = $_POST['api_password'];

$api = new RouterosAPI();

if (!$api->connect($ip, $user, $pass)) {
    die("<div class='alert alert-danger'>Router connection failed</div>");
}

// Query active users
$users = $api->comm("/ip/hotspot/active/print");

$api->disconnect();

if (empty($users)) {
    echo "<div class='alert alert-info'>No active users</div>";
    exit;
}

echo "<table class='table table-bordered'>";
echo "<tr>
        <th>User</th>
        <th>IP</th>
        <th>MAC</th>
        <th>Uptime</th>
        <th>Bytes In</th>
        <th>Bytes Out</th>
        <th>Server</th>
      </tr>";

foreach ($users as $u) {
    echo "<tr>
        <td>{$u['user']}</td>
        <td>{$u['address']}</td>
        <td>{$u['mac-address']}</td>
        <td>{$u['uptime']}</td>
        <td>{$u['bytes-in']}</td>
        <td>{$u['bytes-out']}</td>
        <td>{$u['server']}</td>
    </tr>";
}

echo "</table>";
