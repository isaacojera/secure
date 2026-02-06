<?php
require 'config.php';
require 'mikrotik_api.php';
//requireLogin();

$data = $_POST;

/* Get router details */
$r = $conn->query("SELECT * FROM routers WHERE id=".$data['router_id'])->fetch_assoc();

$api = new RouterosAPI();

if (!$api->connect($r['vpn_ip'], $r['api_username'], $r['api_password'])) {
    die("<h3>Error: Connection Failed</h3>
         <p>Could not connect to router at <b>{$r['vpn_ip']}</b>.</p>
         <p>Please check if the router is online and the WireGuard VPN is active.</p>
         <a href='create_profile.php'>Back</a>");
}

/* Push profile to MikroTik */
$api->comm("/ip/hotspot/user/profile/add", [
    "name"            => $data['profile_name'],
    "rate-limit"      => $data['rate_limit'],
    "session-timeout" => $data['session_timeout'],
    "idle-timeout"    => $data['idle_timeout'],
    "shared-users"    => $data['shared_users']
]);

$api->disconnect();

/* Save locally */
$stmt = $conn->prepare("INSERT INTO hotspot_profiles
(station_id, router_id, profile_name, rate_limit, session_timeout, idle_timeout, shared_users)
VALUES (?,?,?,?,?,?,?)");

$stmt->bind_param(
    "iissssi",
    $_SESSION['station_id'],
    $data['router_id'],
    $data['profile_name'],
    $data['rate_limit'],
    $data['session_timeout'],
    $data['idle_timeout'],
    $data['shared_users']
);

$stmt->execute();

echo "<div style='padding:20px;font-family:arial'>
<h3>✅ Profile created and pushed successfully!</h3>
<p>Profile <b>" . htmlspecialchars($data['profile_name']) . "</b> has been added to <b>" . htmlspecialchars($r['router_name']) . "</b>.</p>
<a href='profiles.php'>View Profiles</a> | <a href='create_profile.php'>Create Another</a>
</div>";
