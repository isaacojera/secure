<?php
require 'config.php';
require 'wg_functions.php';
requireLogin();

function randomPassword($length = 12) {
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%'), 0, $length);
}

$script = "";
$display_pass = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $router_name = $_POST['router_name'];

    // STEP 1 — Generate WireGuard keys
    list($private, $public) = generateWireGuardKeys();

    // STEP 2 — Get next available VPN IP
    $wg_ip = getNextWGIP($conn);

    // STEP 3 — Generate UNIQUE API credentials
    $api_user = "apiuser";
    $api_pass = randomPassword();
    $display_pass = $api_pass;

    // STEP 4 — Save peer in wg_peers
    $stmt = $conn->prepare("INSERT INTO wg_peers 
        (station_id, router_name, wg_ip, private_key, public_key)
        VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param("issss",
        $_SESSION['station_id'],
        $router_name,
        $wg_ip,
        $private,
        $public
    );
    $stmt->execute();

    // STEP 5 — Register router in routers table
    $stmt2 = $conn->prepare("INSERT INTO routers 
        (station_id, router_name, vpn_ip, api_username, api_password)
        VALUES (?, ?, ?, ?, ?)");

    $stmt2->bind_param("issss",
        $_SESSION['station_id'],
        $router_name,
        $wg_ip,
        $api_user,
        $api_pass
    );
    $stmt2->execute();

    // STEP 6 — Add peer LIVE to WireGuard
    shell_exec("sudo wg set wg0 peer $public allowed-ips $wg_ip/32");

    // STEP 7 — MikroTik script
    $serverPublicKey = "HV0MT79EZepRCRjeLi/ZPTpjfs5nzGSVCFwkVcZKIzE=";
    $serverEndpoint  = "146.190.174.165";

    $script = "
/user add name=$api_user password=$api_pass group=full
/ip service enable api

/interface wireguard
add name=wg-mikhmon listen-port=13231 private-key=\"$private\"

/interface wireguard peers
add interface=wg-mikhmon \
    public-key=\"$serverPublicKey\" \
    endpoint-address=$serverEndpoint \
    endpoint-port=51820 \
    allowed-address=10.10.0.1/32 \
    persistent-keepalive=25

/ip address
add address=$wg_ip/32 interface=wg-mikhmon

/ip service
set api address=10.10.0.0/24 disabled=no
";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Provision Router</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <a href="dashboard.php" class="btn btn-outline-secondary mb-3">Back to Dashboard</a>

    <h3>Provision New MikroTik Router</h3>

    <form method="POST" class="card p-4 shadow mb-4">
        <div class="mb-3">
            <label>Router Name</label>
            <input type="text" name="router_name" class="form-control" required>
        </div>
        <button class="btn btn-primary">Provision Router</button>
    </form>

    <?php if (!empty($script)) { ?>
        <div class="card p-4 shadow">
            <h5 class="text-success">Router Provisioned Successfully</h5>

            <div class="alert alert-warning">
                <strong>API Username:</strong> <?= $api_user ?><br>
                <strong>API Password:</strong> <?= $display_pass ?>
            </div>

            <p>Copy this script into MikroTik terminal:</p>
            <textarea class="form-control" rows="16" readonly><?= htmlspecialchars($script) ?></textarea>
        </div>
    <?php } ?>

</div>

</body>
</html>