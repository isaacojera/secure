<?php
require 'config.php';
requireLogin();

$vouchers = $_SESSION['last_vouchers'] ?? [];

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="vouchers.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Username', 'Password']);

foreach ($vouchers as $v) {
    fputcsv($output, $v);
}

fclose($output);
exit;
