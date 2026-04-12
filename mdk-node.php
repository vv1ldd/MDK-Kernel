<?php

declare(strict_types=1);

/**
 * DLVM Reference Node Launcher
 * Axiom: This script is the sovereign controller of the DLVM instance.
 */

// Simple PSR-4 Autoloader for the standalone Kernel
spl_autoload_register(function ($class) {
    $prefix = 'Meanly\\Mdk\\Kernel\\';
    $baseDir = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require $file;
});

use Meanly\Mdk\Kernel\Core\DLVMKernel;
use Meanly\Mdk\Kernel\Core\EngineConfig;
use Meanly\Mdk\Kernel\Core\BaseState;

echo "--- DLVM Virtual Machine Genesis Boot ---\n";

// 1. Define Physics (Config)
$config = new EngineConfig(
    constitutionId: 'MCEP-v1.0-GENESIS',
    mathMode: 'gmp'
);

// 2. Prepare Genesis State
$genesisState = new BaseState([
    'network' => 'meanly-sovereign-v1',
    'initial_supply' => '1000000000000000000000000', // 1M Mdk in atto-units
    'governance' => [
        'mode' => 'deterministic-consensus',
        'version' => '1.0.0'
    ]
]);

// 3. Perform Final Boot Transaction
$result = DLVMKernel::boot(
    constitutionId: 'MCEP-v1.0-GENESIS',
    config: $config,
    genesisState: $genesisState
);

// 4. Report Truth
echo "Status: " . ($result->success ? "SUCCESS" : "FAILED") . "\n";
echo "Constitution ID: " . $result->constitutionId . "\n";
echo "Execution Fingerprint: " . $result->executionFingerprint . "\n";
echo "Genesis State Root: " . $result->stateRoot . "\n";
echo "Checkpoint Hash: " . $result->checkpointHash . "\n";
echo "----------------------------------------\n";
