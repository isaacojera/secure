<?php
//provision_router.php
require 'config.php';
require 'wg_functions.php';
requireLogin();

$script = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $router_name = $_POST['router_name'];

    // STEP 1 — Generate WireGuard keys
    list($private, $public) = generateWireGuardKeys();

    // STEP 2 — Get next available VPN IP
    $wg_ip = getNextWGIP($conn);

    // STEP 3 — Save peer in database
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

    // ==============================
    // STEP 4 — Add peer to WG server
    // ==============================
    $peerConfig = "\n[Peer]
PublicKey = $public
AllowedIPs = $wg_ip/32
";

    // Add peer LIVE to WireGuard interface
    shell_exec("sudo wg set wg0 peer $public allowed-ips $wg_ip/32");
    // ==============================
    // STEP 5 — Build MikroTik script
    // ==============================
    $serverPublicKey = "HV0MT79EZepRCRjeLi/ZPTpjfs5nzGSVCFwkVcZKIzE=";   // run: wg show wg0
    $serverEndpoint  = "146.190.174.165:51820";

    $script = "
/interface wireguard add name=wg0 private-key=\"$private\"

/interface wireguard peers add \
    interface=wg0 \
    public-key=\"$serverPublicKey\" \
    endpoint-address=$serverEndpoint \
    allowed-address=0.0.0.0/0 \
    persistent-keepalive=25

/ip address add address=$wg_ip/24 interface=wg0

/ip service set api address=10.10.0.0/24 disabled=no
";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Provision Router</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <!-- Back Button -->
    <a href="dashboard.php" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>

    <h3 class="mb-4">Provision New MikroTik Router</h3>

    <form method="POST" class="card p-4 shadow mb-4">
        <div class="mb-3">
            <label>Router Name</label>
            <input type="text" name="router_name" class="form-control" required>
        </div>
        <button class="btn btn-primary">Provision Router</button>
    </form>

    <?php if (!empty($script)) { ?>
        <div class="card p-4 shadow">
            <h5 class="mb-3 text-success">✅ Router Provisioned Successfully</h5>
            <p>Copy this script and paste it into the MikroTik terminal:</p>
            <textarea class="form-control" rows="14" readonly><?= htmlspecialchars($script) ?></textarea>
        </div>
    <?php } ?>

</div>

</body>
</html>
