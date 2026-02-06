<?php
require 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['router_name'];
    $wg_ip = $_POST['wg_ip'];
    $api_user = $_POST['api_user'];
    $api_pass = $_POST['api_pass'];
    $wg_key = $_POST['wg_key'];

    $stmt = $conn->prepare("INSERT INTO routers 
        (station_id, router_name, wg_ip, api_username, api_password, wg_public_key)
        VALUES (?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("isssss",
        $_SESSION['station_id'],
        $name,
        $wg_ip,
        $api_user,
        $api_pass,
        $wg_key
    );

    $stmt->execute();
    header("Location: routers.php");
}

echo "Router added successfully!". " <a href='routers.php'>View Routers</a>";
