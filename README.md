# Deterministic Ledger Virtual Machine (DLVM)

**Sovereign. Deterministic. Verifiable.**

DLVM is the **ultimate source of truth** for financial execution, governed by the **Meanly Causal Equilibrium Protocol (MCEP)**. It serves as the sovereign execution authority, while external layers (like the Commitment layer) provide cryptographic anchoring and auditability.

Built as a zero-dependency, pure-PHP 8.2+ "truth oracle" for building verifiable economic infrastructure.

---

## 🏛 Core Pillars

- **Absolute Determinism**: $\delta(S, E, C) \to (S', \tau)$ is a pure function. Identical inputs yield byte-identical outputs across any platform (ARM/x86).
- **Atto-Precision Physics**: Fixed-point math locked at $10^{-18}$ precision using integer-only arithmetic to prevent floating-point drift.
- **Boundary Rounding Law**: Intermediate calculations remain unrounded; Banker's Rounding (Half-Even) is applied only at atomic commitment boundaries.
- **Cryptographic Integrity**: State-root sealing via SHA-256 and Canonical JSON (RFC 8785 / JCS) serialization.
- **Zero-Dependency Sovereignty**: Full control over the execution path. No external supply-chain risks.

## 🧬 The MCEP Protocol

DLVM operates under a formal constitution. Every execution session is locked by an **Execution Fingerprint** that detects any change in hardware, OS, or configuration that could potentially breach the determinism of the financial state.

## 🚀 Getting Started

The kernel is designed to be instantiated, not just run.

```php
use Meanly\Mdk\Kernel\Core\DLVMKernel;
use Meanly\Mdk\Kernel\Core\EngineConfig;

// 1. Define the laws of your universe
$config = new EngineConfig(constitutionId: 'MCEP-v1.0-GENESIS');

// 2. Instantiate the reality
$result = DLVMKernel::boot($constitutionId, $config, $genesisState);

echo "Reality Initialized: " . $result->stateRoot;
```

### 🌉 Integration via Bridge

For host applications (like Bagisto), all filesystem interactions with MDK MUST be resolved through the `Meanly\Mdk\Kernel\Bridge\MDKPath` layer. This ensures that the application remains topology-independent.

```php
use Meanly\Mdk\Kernel\Bridge\MDKPath;

// Resolving path to the commitment scripts
$scriptPath = MDKPath::resolve('commitment.scripts') . '/gasless_relayer.js';
```

---

## ⚖️ License

Proprietary / Institutional Use.
Part of the Meanly Sovereign Financial Infrastructure.
