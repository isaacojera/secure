<?php
require 'config.php';
require 'mikrotik_api.php';

$id   = $_POST['id'];
$ip   = $_POST['vpn_ip'];
$user = $_POST['api_username'];
$pass = $_POST['api_password'];

$api = new RouterosAPI();

$status = "offline";

if ($api->connect($ip, $user, $pass)) {
    $status = "online";
    $api->disconnect();

    $stmt = $conn->prepare("UPDATE routers SET status='online', last_seen=NOW() WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("UPDATE routers SET status='offline' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

echo json_encode(["status"=>$status]);
