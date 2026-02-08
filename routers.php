<?php
session_start();
require 'config.php';

$routers = $conn->query("SELECT * FROM routers WHERE station_id=".$_SESSION['station_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Routers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white">My Routers</div>
        <div class="card-body">
        <!-- <a href="add_router.php" class="btn btn-primary mb-3">Add Router</a> -->
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>VPN IP</th>
                        <th>Status</th>
                        <th>Last Seen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($r = $routers->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['router_name']) ?></td>
                        <td><?= $r['vpn_ip'] ?></td>
                        <td>
                            <span id="status<?= $r['id'] ?>" class="badge bg-secondary">
                                checking...
                            </span>
                        </td>
                        <td id="seen<?= $r['id'] ?>">
                            <?= $r['last_seen'] ?? '-' ?>
                        </td>
                    </tr>

<script>
document.addEventListener("DOMContentLoaded", function(){
    checkRouter<?= $r['id'] ?>();
});

function checkRouter<?= $r['id'] ?>(){
    let formData = new FormData();
    formData.append('id', '<?= $r['id'] ?>');
    formData.append('vpn_ip', '<?= $r['vpn_ip'] ?>');
    formData.append('api_username', '<?= $r['api_username'] ?>');
    formData.append('api_password', '<?= $r['api_password'] ?>');

    fetch('router_health.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        let badge = document.getElementById('status<?= $r['id'] ?>');
        let seen  = document.getElementById('seen<?= $r['id'] ?>');

        if(data.status === 'online'){
            badge.className = 'badge bg-success';
            badge.innerText = 'online';
            seen.innerText = 'just now';
        } else {
            badge.className = 'badge bg-danger';
            badge.innerText = 'offline';
        }
    });
}
</script>

                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

</body>
</html>