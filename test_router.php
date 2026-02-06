<?php
require 'mikrotik_api.php';

$ip   = $_POST['vpn_ip'];
$user = $_POST['api_username'];
$pass = $_POST['api_password'];

$api = new RouterosAPI();

if ($api->connect($ip, $user, $pass)) {
    echo json_encode(['status' => 'online']);
    $api->disconnect();
} else {
    echo json_encode(['status' => 'offline']);
}
