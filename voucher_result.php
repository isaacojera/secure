<?php
require 'config.php';
requireLogin();

$vouchers = $_SESSION['last_vouchers'] ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vouchers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print { .no-print { display:none; } }
        .voucher-box{
            border:1px dashed #000;
            padding:10px;
            margin:5px;
            width:220px;
            float:left;
            text-align:center;
        }
    </style>
</head>
<body>

<div class="container mt-3">

<div class="no-print mb-3">
    <a href="export_csv.php" class="btn btn-success">Download CSV</a>
    <button onclick="window.print()" class="btn btn-primary">Print</button>
</div>

<?php foreach($vouchers as $v): ?>
    <div class="voucher-box">
        <h5>WiFi Voucher</h5>
        <strong>User:</strong> <?= $v[0] ?><br>
        <strong>Pass:</strong> <?= $v[1] ?><br>
    </div>
<?php endforeach; ?>

</div>

</body>
</html>
