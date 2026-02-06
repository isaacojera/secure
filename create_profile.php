<?php
require 'config.php';
//requireLogin();

$routers = $conn->query("SELECT * FROM routers WHERE station_id=".$_SESSION['station_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Hotspot Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

<a href="profiles.php" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to Profiles</a>

<div class="card shadow">
<div class="card-header bg-dark text-white">Create Hotspot Profile</div>
<div class="card-body">

<form method="POST" action="push_profile.php">

    <div class="mb-3">
        <label>Select Router</label>
        <select name="router_id" class="form-control" required>
            <?php while($r = $routers->fetch_assoc()): ?>
                <option value="<?= $r['id'] ?>"><?= e($r['router_name']) ?> (<?= $r['vpn_ip'] ?>)</option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Profile Name</label>
        <input type="text" name="profile_name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Rate Limit (e.g 2M/2M)</label>
        <input type="text" name="rate_limit" class="form-control">
    </div>

    <div class="mb-3">
        <label>Session Timeout (e.g 1h, 30m)</label>
        <input type="text" name="session_timeout" class="form-control">
    </div>

    <div class="mb-3">
        <label>Idle Timeout (e.g 5m)</label>
        <input type="text" name="idle_timeout" class="form-control">
    </div>

    <div class="mb-3">
        <label>Shared Users</label>
        <input type="number" name="shared_users" value="1" class="form-control">
    </div>

    <button class="btn btn-success">Create & Push to Router</button>

</form>

</div>
</div>
</div>

</body>
</html>
