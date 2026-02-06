<?php
// wg_functions.php

// Full path to wg binary (important for Apache)
define('WG_BIN', '/usr/bin/wg');

/**
 * Generate WireGuard key pair safely
 */
function generateWireGuardKeys() {

    $output = shell_exec('sudo ' . WG_BIN . ' genkey 2>/dev/null');
    $private = $output !== null ? trim($output) : '';

    if (empty($private)) {
        throw new Exception("Failed to generate WireGuard private key. Check permissions for sudo " . WG_BIN);
    }

    $cmd = "echo " . escapeshellarg($private) . " | sudo " . WG_BIN . " pubkey 2>/dev/null";
    $outputPub = shell_exec($cmd);
    $public = $outputPub !== null ? trim($outputPub) : '';

    if (empty($public)) {
        throw new Exception("Failed to generate WireGuard public key");
    }

    return [$private, $public];
}

/**
 * Get next available VPN IP safely
 * Prevents duplicate IPs under concurrency
 */
function getNextWGIP($conn) {

    $base = "10.10.0.";
    $start = 2;
    $end = 254;

    // Fetch all used IPs
    $used = [];
    $res = $conn->query("SELECT wg_ip FROM wg_peers");
    while ($row = $res->fetch_assoc()) {
        $used[] = $row['wg_ip'];
    }

    for ($i = $start; $i <= $end; $i++) {
        $candidate = $base . $i;
        if (!in_array($candidate, $used)) {
            return $candidate;
        }
    }

    throw new Exception("No available WireGuard IPs left in pool");
}
