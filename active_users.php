<?php
require 'config.php';
//requireLogin();

$routers = $conn->query("SELECT * FROM routers WHERE station_id=".$_SESSION['station_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Active Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="card shadow">
<div class="card-header bg-dark text-white">Active Hotspot Users</div>
<div class="card-body">

<div class="mb-3">
    <label>Select Router</label>
    <select id="router" class="form-control">
        <?php while($r = $routers->fetch_assoc()): ?>
            <option 
                value="<?= $r['id'] ?>"
                data-ip="<?= $r['vpn_ip'] ?>"
                data-user="<?= $r['api_username'] ?>"
                data-pass="<?= $r['api_password'] ?>">
                <?= e($r['router_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

<button onclick="loadUsers()" class="btn btn-primary mb-3">Load Active Users</button>

<div id="usersTable"></div>

</div>
</div>
</div>

<script>
function loadUsers(){
    let sel = document.getElementById('router');
    let opt = sel.options[sel.selectedIndex];

    let formData = new FormData();
    formData.append('vpn_ip', opt.dataset.ip);
    formData.append('api_username', opt.dataset.user);
    formData.append('api_password', opt.dataset.pass);

    fetch('fetch_active_users.php', {
        method: 'POST',
        body: formData
    }).then(res => res.text())
      .then(html => {
        document.getElementById('usersTable').innerHTML = html;
      });
}
</script>

</body>
</html>
