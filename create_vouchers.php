<?php
require 'config.php';
require 'mikrotik_api.php';
requireLogin();

$data = $_POST;

$r = $conn->query("SELECT * FROM routers WHERE id=".$data['router_id'])->fetch_assoc();

$api = new RouterosAPI();
if (!$api->connect($r['vpn_ip'], $r['api_username'], $r['api_password'])) {
    die("Router connection failed");
}

$vouchers = [];

for ($i = 0; $i < $data['count']; $i++) {

    $username = generateVoucher(8);
    $password = generateVoucher(8);

    // Push to MikroTik
    $api->comm("/ip/hotspot/user/add", [
        "name"     => $username,
        "password" => $password,
        "profile"  => $data['profile_name'],
        "comment"  => $username
    ]);

    // Save locally
    $stmt = $conn->prepare("INSERT INTO vouchers
    (station_id, router_id, profile_name, username, password)
    VALUES (?,?,?,?,?)");

    $stmt->bind_param(
        "iisss",
        $_SESSION['station_id'],
        $data['router_id'],
        $data['profile_name'],
        $username,
        $password
    );

    $stmt->execute();

    $vouchers[] = [$username, $password];
}

$api->disconnect();

$_SESSION['last_vouchers'] = $vouchers;

header("Location: vouchers_result.php");
