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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- THIS IS THE FIX FOR "SMALL BODY" -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Mikrotik Remote</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f8f9fa; font-family: sans-serif; }
        .card { cursor:pointer; transition: transform 0.1s; border: none; border-radius: 12px; }
        .card:hover { transform: translateY(-5px); }
        .sidebar { min-height:100vh; background:#212529; color:white; }
        .sidebar a { color:rgba(255,255,255,0.8); text-decoration:none; display:block; padding:12px 20px; border-radius: 8px; margin: 4px 10px; transition: 0.3s; }
        .sidebar a:hover { background:rgba(255,255,255,0.1); color: #fff; }
        
        /* Mobile Sidebar Logic */
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed; top: 0; left: 0; height: 100vh; width: 260px;
                z-index: 1050; transform: translateX(-100%); transition: 0.3s ease;
            }
            .sidebar.show { transform: translateX(0); }
            .sidebar-overlay {
                display: none; position: fixed; top: 0; left: 0; width: 100%;
                height: 100%; background: rgba(0,0,0,0.5); z-index: 1040;
            }
            .sidebar-overlay.show { display: block; }
            /* Make text readable on small screens */
            .card-text.fs-3 { font-size: 1.5rem !important; }
            .card-title { font-size: 0.9rem; }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 sidebar p-0" id="sidebar">
            <div class="d-md-none text-end p-3">
                <i class="bi bi-x-lg fs-4" onclick="toggleSidebar()"></i>
            </div>
            <h5 class="text-center py-4 px-2 fw-bold text-uppercase" style="letter-spacing:1px;">Mikrotik Admin</h5>
            <a href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="provision_router.php"><i class="bi bi-plus-circle me-2"></i> Add Router</a>
            <a href="profiles.php"><i class="bi bi-gear me-2"></i> Profiles</a>
            <a href="generate_vouchers.php"><i class="bi bi-ticket-perforated me-2"></i> Vouchers</a>
            <a href="active_users.php"><i class="bi bi-people me-2"></i> Active Users</a>
            <hr class="mx-3 opacity-25">
            <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center d-md-none mb-3">
                <h4 class="m-0">Dashboard</h4>
                <button class="btn btn-dark" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            
            <h2 class="d-none d-md-block mb-4">Dashboard Overview</h2>
            
            <div class="row g-3"> <!-- g-3 adds nice spacing between cards -->
                <!-- Routers - col-6 means 2 per row on mobile -->
                <div class="col-6 col-md-3">
                    <div class="card text-white bg-primary h-100 shadow-sm" onclick="location.href='routers.php'">
                        <div class="card-body py-4">
                            <h6 class="card-title opacity-75"><i class="bi bi-hdd-network"></i> Routers</h6>
                            <p class="card-text fs-3 fw-bold mb-0"><?= $routers_count ?></p>
                        </div>
                    </div>
                </div>

                <!-- Profiles -->
                <div class="col-6 col-md-3">
                    <div class="card text-white bg-success h-100 shadow-sm" onclick="location.href='profiles.php'">
                        <div class="card-body py-4">
                            <h6 class="card-title opacity-75"><i class="bi bi-gear"></i> Profiles</h6>
                            <p class="card-text fs-3 fw-bold mb-0"><?= $profiles_count ?></p>
                        </div>
                    </div>
                </div>

                <!-- Vouchers -->
                <div class="col-6 col-md-3">
                    <div class="card text-white bg-warning h-100 shadow-sm" onclick="location.href='generate_vouchers.php'">
                        <div class="card-body py-4 text-dark">
                            <h6 class="card-title opacity-75"><i class="bi bi-ticket-perforated"></i> Vouchers</h6>
                            <p class="card-text fs-3 fw-bold mb-0"><?= $vouchers_count ?></p>
                        </div>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="col-6 col-md-3">
                    <div class="card text-white bg-danger h-100 shadow-sm" onclick="location.href='active_users.php'">
                        <div class="card-body py-4">
                            <h6 class="card-title opacity-75"><i class="bi bi-people"></i> Active</h6>
                            <p class="card-text fs-3 fw-bold mb-0"><?= $active_users ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row mt-4 g-3">
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white fw-bold">Vouchers Usage</div>
                        <div class="card-body">
                            <canvas id="voucherChart" style="max-height: 250px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white fw-bold">Active Users per Router</div>
                        <div class="card-body">
                            <canvas id="activeChart" style="max-height: 250px;"></canvas>
                        </div>
                    </div>
                </div>
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