<?php
require 'config.php';
requireLogin();
require_once 'mikrotik_api.php';

$api = new RouterosAPI();

// Quick stats
$routers_count  = $conn->query("SELECT COUNT(*) as cnt FROM routers WHERE station_id=".$_SESSION['station_id'])->fetch_assoc()['cnt'];
$profiles_count = $conn->query("SELECT COUNT(*) as cnt FROM hotspot_profiles WHERE station_id=".$_SESSION['station_id'])->fetch_assoc()['cnt'];
$vouchers_count = $conn->query("SELECT COUNT(*) as cnt FROM vouchers WHERE station_id=".$_SESSION['station_id'])->fetch_assoc()['cnt'];
$active_users   = 0;

// Get total active users across all routers
$routers = $conn->query("SELECT * FROM routers WHERE station_id=".$_SESSION['station_id']);

while($r = $routers->fetch_assoc()) {
    $api = new RouterosAPI();
    if($api->connect($r['vpn_ip'], $r['api_username'], $r['api_password'])) {
        $users = $api->comm("/ip/hotspot/active/print");
        $active_users += count($users);
        $api->disconnect();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mikrotik Remote</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f5f5f5; }
        .card { cursor:pointer; transition: transform 0.1s; }
        .card:hover { transform: scale(1.03); }
        .sidebar { min-height:100vh; background:#343a40; color:white; }
        .sidebar a { color:white; text-decoration:none; display:block; padding:10px 20px; }
        .sidebar a:hover { background:#495057; }

        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: 250px;
                z-index: 1050;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>
<div class="row">

    <!-- Sidebar -->
    <div class="col-md-2 sidebar p-0" id="sidebar">
        <div class="d-md-none text-end p-2">
            <button class="btn btn-sm btn-outline-light" onclick="toggleSidebar()"><i class="bi bi-x-lg"></i></button>
        </div>
        <h4 class="text-center py-3 bg-dark">Secure | Remote Access</h4>
        <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <!-- <a href="routers.php"><i class="bi bi-hdd-network"></i> Routers</a> -->
        <a href="provision_router.php"><i class="bi bi-hdd-network"></i>+ Add Router</a>
        <a href="profiles.php"><i class="bi bi-gear"></i> Profiles</a>
        <a href="generate_vouchers.php"><i class="bi bi-ticket-perforated"></i> Voucher Generator</a>
        <a href="active_users.php"><i class="bi bi-people"></i> Active Users</a>
        <a href="#"><i class="bi bi-bar-chart-line"></i> Reports</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-10 p-4">
        <button class="btn btn-dark d-md-none mb-3" onclick="toggleSidebar()">
            <i class="bi bi-list"></i> Menu
        </button>
        <h2>Dashboard</h2>
        <div class="row mt-4">

            <!-- Routers Card -->
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary" onclick="location.href='routers.php'">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-hdd-network"></i> Routers</h5>
                        <p class="card-text fs-3"><?= $routers_count ?></p>
                    </div>
                </div>
            </div>

            <!-- Profiles Card -->
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success" onclick="location.href='profiles.php'">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-gear"></i> Profiles</h5>
                        <p class="card-text fs-3"><?= $profiles_count ?></p>
                    </div>
                </div>
            </div>

            <!-- Vouchers Card -->
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-warning" onclick="location.href='generate_vouchers.php'">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-ticket-perforated"></i> Vouchers</h5>
                        <p class="card-text fs-3"><?= $vouchers_count ?></p>
                    </div>
                </div>
            </div>

            <!-- Active Users Card -->
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-danger" onclick="location.href='active_users.php'">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-people"></i> Active Users</h5>
                        <p class="card-text fs-3"><?= $active_users ?></p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Optionally add charts/analytics -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header">Vouchers Usage</div>
                    <div class="card-body">
                        <canvas id="voucherChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header">Active Users per Router</div>
                    <div class="card-body">
                        <canvas id="activeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
    document.querySelector('.sidebar-overlay').classList.toggle('show');
}

const voucherCtx = document.getElementById('voucherChart').getContext('2d');
const voucherChart = new Chart(voucherCtx, {
    type: 'doughnut',
    data: {
        labels: ['Used', 'Unused'],
        datasets: [{
            label: 'Vouchers',
            data: [
                <?= $conn->query("SELECT COUNT(*) FROM vouchers WHERE station_id=".$_SESSION['station_id']." AND status='used'")->fetch_row()[0] ?>,
                <?= $conn->query("SELECT COUNT(*) FROM vouchers WHERE station_id=".$_SESSION['station_id']." AND status='unused'")->fetch_row()[0] ?>
            ],
            backgroundColor: ['#dc3545','#198754']
        }]
    }
});

const activeCtx = document.getElementById('activeChart').getContext('2d');
const activeChart = new Chart(activeCtx, {
    type: 'bar',
    data: {
        labels: [
            <?php
            $routers = $conn->query("SELECT * FROM routers WHERE station_id=".$_SESSION['station_id']);
            $labels = [];
            while($r = $routers->fetch_assoc()) $labels[] = "'" . e($r['router_name']) . "'";
            echo implode(',', $labels);
            ?>
        ],
        datasets: [{
            label: 'Active Users',
            data: [
                <?php
                $routers = $conn->query("SELECT * FROM routers WHERE station_id=".$_SESSION['station_id']);
                $data=[];
                // require 'mikrotik_api.php'; // Already required at the top
                while($r=$routers->fetch_assoc()){
                    $api = new RouterosAPI();
                    if($api->connect($r['vpn_ip'],$r['api_username'],$r['api_password'])){
                        $users = $api->comm("/ip/hotspot/active/print");
                        $data[] = count($users);
                        $api->disconnect();
                    } else $data[]=0;
                }
                echo implode(',', $data);
                ?>
            ],
            backgroundColor: '#dc3545'
        }]
    },
    options: { responsive:true, plugins: { legend:{ display:false } } }
});
</script>

</body>
</html>