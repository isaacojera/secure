<?php
require 'config.php';

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['station_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ojesystems - Home</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
        <style>
                :root{--brand:#0d6efd;--muted:rgba(13,110,253,0.08)}
                body{font-family:Inter,system-ui,Segoe UI,Roboto,"Helvetica Neue",Arial,sans-serif;background:linear-gradient(135deg,#0b63d6 0%,#33a6ff 100%);color:#fff;min-height:100vh}
                .navbar-brand{font-weight:700;letter-spacing:.2px}
                .hero{padding:4rem 0}
                .hero-card{border-radius:1rem;background:linear-gradient(180deg,rgba(255,255,255,0.98),#fff);color:#0b1a2b}
                .btn-pill{border-radius:50px;padding:10px 26px;font-weight:600}
                .feature-icon{width:56px;height:56px;border-radius:12px;background:var(--muted);display:flex;align-items:center;justify-content:center;color:#06449b;font-weight:700}
                footer{padding:1.25rem 0;color:rgba(255,255,255,0.85)}
                @media (max-width:767px){.hero{padding:2rem 0}.hero .col-md-6:first-child{margin-bottom:1.25rem}}
        </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-transparent">
    <div class="container">
        <a class="navbar-brand text-white" href="index.php">Ojesystems</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link text-white" href="dashboard.php">Mikrotik Dashboard</a></li>
                 <!-- <li class="nav-item"><a class="nav-link text-white" href="https://ojesystems.tech/">Billing System Dashboard</a></li> -->
                <!--<li class="nav-item"><a class="nav-link text-white" href="profiles.php">Profiles</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="generate_vouchers.php">Vouchers</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="active_users.php">Active Users</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="export_csv.php">Export</a></li> -->
                <li class="nav-item ms-2"><a class="btn btn-outline-light btn-pill" href="login.php">Login</a></li>
                <li class="nav-item ms-2"><a class="btn btn-light text-primary btn-pill" href="signup.php">Sign Up</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="container hero">
    <div class="row align-items-center">
        <div class="col-md-6 text-white">
            <h1 class="display-5 fw-bold">Manage MikroTik routers with confidence</h1>
            <p class="lead mt-3">Ojesystems helps ISPs and hotspots generate vouchers, manage profiles, monitor active users, and keep router fleets in sync — all from one simple dashboard.</p>
            <div class="mt-4">
                <a href="login.php" class="btn btn-light btn-pill me-2">Get Started</a>
                <a href="#" class="btn btn-outline-light btn-pill">ReadMore....</a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow hero-card p-4">
                <h4 class="mb-3">Quick Actions</h4>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="feature-icon">R</div>
                            <div>
                                <div class="fw-bold">Mikrotik Config Wizard</div>
                                <div class="text-muted small">This enables you to setup a wifi hotspot from scratch and configure New MikroTik routers</div>
                            </div>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary">Open</a>
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="feature-icon">V</div>
                            <div>
                                <div class="fw-bold">Billing System</div>
                                <div class="text-muted small">Create a mobile money account for payments</div>
                            </div>
                        </div>
                        <a href="https://ojesystems.tech/" class="btn btn-sm btn-primary">Billing System</a>
                    </div>

                    <!-- <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="feature-icon">U</div>
                            <div>
                                <div class="fw-bold">Active Users</div>
                                <div class="text-muted small">Monitor active sessions in real-time</div>
                            </div>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary">View</a>
                    </div>
                </div> -->
            </div>
        </div>
    </div>

    <section class="row mt-5 text-center">
        <div class="col-12">
            <h3 class="fw-semibold mb-3">Features</h3>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 p-3">
                <div class="card-body">
                    <h5 class="card-title">Router Sync</h5>
                    <p class="card-text small">Push profiles and settings to multiple routers quickly and reliably.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 p-3">
                <div class="card-body">
                    <h5 class="card-title">Voucher Management</h5>
                    <p class="card-text small">Create, export, and validate vouchers with usage limits and expiry.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 p-3">
                <div class="card-body">
                    <h5 class="card-title">Reporting</h5>
                    <p class="card-text small">Export active user lists and session logs for auditing.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="text-center">
    <div class="container">
        <small>© <?= date('Y') ?> Ojesystems. All rights reserved. Built for MikroTik management.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
