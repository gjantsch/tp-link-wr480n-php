<?php
/**
 * Get TP-LINK WR480-N statistics.
 *
 * This is a sample usage of the TPLinkWR480N, that
 * collects data usage from the router.
 *
 * This may work or not depending of the the lease time
 * set on your router, be aware that the router assigns
 * the same IP address to several MACs on a certain
 * period of time, and the router accounts the data usage
 * by IP, not MAC address.
 *
 * This is a CLI (command line interface) script.
 *
 */

require_once 'TPLinkWR480N.php';

$router = new TPLinkWR480N('192.168.100.1');
$router->startSession();
$stats_data = $router->getDataUsage();

// this will reset (zero) all the data usage counters
// $router->resetDataUsage();
$router->endSession();

$total = $stats_data['total'];
$stats_data = $stats_data['data'];

echo "#### ROUTER STATS " . PHP_EOL;
echo str_repeat('-', 100) . PHP_EOL;
echo str_pad('name', 30, ' ', STR_PAD_RIGHT);
echo str_pad('ip', 20, ' ', STR_PAD_LEFT);
echo str_pad('mac', 20, ' ', STR_PAD_LEFT);
echo str_pad('packets', 15, ' ', STR_PAD_LEFT);
echo str_pad('mb', 15, ' ', STR_PAD_LEFT);
echo PHP_EOL;

foreach ($stats_data as $data) {
    echo str_pad($data['name'], 30, ' ', STR_PAD_LEFT);
    echo str_pad($data['ip'], 20, ' ', STR_PAD_LEFT);
    echo str_pad($data['mac'], 20, ' ', STR_PAD_LEFT);
    echo str_pad($data['packets'], 15, ' ', STR_PAD_LEFT);
    echo str_pad($data['mb'], 15, ' ', STR_PAD_LEFT);
    echo PHP_EOL;
}

echo str_repeat('-', 100) . PHP_EOL;
echo "Total devices: " . count($stats_data) . PHP_EOL;
echo "Total: " . number_format($total/1024/1024, 1, ',', '.') . 'MB' . PHP_EOL;
