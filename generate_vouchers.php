<?php
require 'config.php';
//requireLogin();

$routers  = $conn->query("SELECT * FROM routers WHERE station_id=".$_SESSION['station_id']);
$profiles = $conn->query("SELECT * FROM hotspot_profiles WHERE station_id=".$_SESSION['station_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Vouchers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <a href="dashboard.php" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>

<div class="card shadow">
<div class="card-header bg-dark text-white">Voucher Generator</div>
<div class="card-body">

<form method="POST" action="create_vouchers.php">

    <div class="mb-3">
        <label>Select Router</label>
        <select name="router_id" class="form-control" required>
            <?php while($r = $routers->fetch_assoc()): ?>
                <option value="<?= $r['id'] ?>"><?= e($r['router_name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Select Profile</label>
        <select name="profile_name" class="form-control" required>
            <?php while($p = $profiles->fetch_assoc()): ?>
                <option value="<?= e($p['profile_name']) ?>"><?= e($p['profile_name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Number of Vouchers</label>
        <input type="number" name="count" class="form-control" required>
    </div>

    <button class="btn btn-success">Generate & Push</button>

</form>

</div>
</div>
</div>

</body>
</html>
