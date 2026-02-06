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

            <a href="add_router.php" class="btn btn-primary mb-3">Add Router</a>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>VPN IP</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Test</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($r = $routers->fetch_assoc()): ?>
                    <tr>
                        <td><?= $r['router_name'] ?></td>
                        <td><?= $r['vpn_ip'] ?></td>
                        <td><?= $r['location'] ?></td>
                        <td>
                            <span id="status<?= $r['id'] ?>" class="badge bg-secondary">
                                <?= $r['status'] ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning"
                                onclick="testRouter('<?= $r['id'] ?>','<?= $r['vpn_ip'] ?>','<?= $r['api_username'] ?>','<?= $r['api_password'] ?>')">
                                Test
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<script>
function testRouter(id, ip, user, pass){
    let formData = new FormData();
    formData.append('vpn_ip', ip);
    formData.append('api_username', user);
    formData.append('api_password', pass);

    fetch('test_router.php', {
        method: 'POST',
        body: formData
    }).then(res => res.json())
      .then(data => {
        let badge = document.getElementById('status'+id);
        if(data.status === 'online'){
            badge.className = 'badge bg-success';
            badge.innerText = 'online';
        } else {
            badge.className = 'badge bg-danger';
            badge.innerText = 'offline';
        }
      });
}
</script>

</body>
</html>
