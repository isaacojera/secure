<?php
require 'config.php';
//requireLogin();

$profiles = $conn->query("
SELECT p.*, r.router_name 
FROM hotspot_profiles p
JOIN routers r ON p.router_id=r.id
WHERE p.station_id=".$_SESSION['station_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hotspot Profiles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="card shadow">
<div class="card-header bg-dark text-white">Hotspot Profiles</div>
<div class="card-body">

<a href="create_profile.php" class="btn btn-primary mb-3">Create Profile</a>

<table class="table table-bordered">
<thead>
<tr>
<th>Profile</th>
<th>Router</th>
<th>Rate Limit</th>
<th>Session Timeout</th>
<th>Idle Timeout</th>
<th>Shared</th>
</tr>
</thead>
<tbody>
<?php while($p = $profiles->fetch_assoc()): ?>
<tr>
<td><?= e($p['profile_name']) ?></td>
<td><?= e($p['router_name']) ?></td>
<td><?= e($p['rate_limit']) ?></td>
<td><?= e($p['session_timeout']) ?></td>
<td><?= e($p['idle_timeout']) ?></td>
<td><?= $p['shared_users'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</div>
</div>
</div>

</body>
</html>
