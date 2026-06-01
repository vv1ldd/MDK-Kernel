<?php

declare(strict_types=1);

/**
 * MDK Settlement CLI — applies a signed SSP packet to MySQL ledger tables.
 *
 * Env:
 *   MDK_DB_HOST, MDK_DB_NAME, MDK_DB_USER, MDK_DB_PASSWORD
 *   MDK_ALLOW_UNSIGNED=1 — dev only: skip Ed25519 signature verification
 *   MDK_MPO_SETTLE_DOMAIN — default "MPO_SETTLE_V1|"
 *   MDK_SETTLEMENT_SIGNATURE_QUORUM — default 1
 *
 * Usage: php mdk-settle.php '{"payload":{...},"signature":"<base64>"}'
 *        php mdk-settle.php @packet.json
 */

spl_autoload_register(function (string $class): void {
    if (! str_starts_with($class, 'Meanly\\Mdk\\Kernel\\')) {
        return;
    }
    $path = __DIR__ . '/src/' . str_replace('\\', '/', substr($class, 18)) . '.php';
    if (is_file($path)) {
        require_once $path;
    }
});

use Meanly\Mdk\Kernel\Settlement\SettlementException;
use Meanly\Mdk\Kernel\Settlement\SettlementRunner;

echo "--- MDK Settlement v0.4.0 ---\n";

$input = $argv[1] ?? null;
if (! $input) {
    fwrite(STDERR, "ERROR: Missing SSP packet (JSON string or @file).\n");
    exit(1);
}

if (str_starts_with($input, '@')) {
    $path = substr($input, 1);
    if (! is_file($path)) {
        fwrite(STDERR, "ERROR: SSP file not found: {$path}\n");
        exit(1);
    }
    $input = file_get_contents($path);
    if ($input === false) {
        fwrite(STDERR, "ERROR: Cannot read {$path}\n");
        exit(1);
    }
}

$packet = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
if (! is_array($packet)) {
    fwrite(STDERR, "ERROR: Packet must be a JSON object.\n");
    exit(1);
}

try {
    $runner = new SettlementRunner();
    $result = $runner->run($packet);
    echo 'Settlement hash: ' . substr($result['settlement_id'], 0, 16) . "…\n";
    echo 'Tx / idempotency key: ' . $result['tx_hash'] . "\n";
    echo 'Status: ' . $result['status'] . "\n";
    exit($result['status'] === 'SETTLED' || $result['status'] === 'ALREADY_SETTLED' ? 0 : 1);
} catch (SettlementException $e) {
    fwrite(STDERR, 'FAILED: ' . $e->getMessage() . "\n");
    exit(1);
} catch (Throwable $e) {
    fwrite(STDERR, 'FAILED: ' . $e->getMessage() . "\n");
    exit(1);
}
