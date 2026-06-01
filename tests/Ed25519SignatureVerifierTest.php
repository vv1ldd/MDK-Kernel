<?php

declare(strict_types=1);

namespace Meanly\Mdk\Kernel\Tests;

use Meanly\Mdk\Kernel\Identity\CanonicalJsonEncoder;
use Meanly\Mdk\Kernel\Settlement\Ed25519SignatureVerifier;
use PHPUnit\Framework\TestCase;

final class Ed25519SignatureVerifierTest extends TestCase
{
    public function testValidSignatureVerifies(): void
    {
        $encoder = new CanonicalJsonEncoder();
        $payload = [
            'b' => 'x',
            'a' => 1,
        ];

        $canonical = $encoder->encode($payload);
        $domain = 'MPO_SETTLE_V1|';
        $message = $domain . $canonical;

        $keypair = sodium_crypto_sign_keypair();
        $publicKey = sodium_crypto_sign_publickey($keypair);
        $secretKey = sodium_crypto_sign_secretkey($keypair);

        $signature = sodium_crypto_sign_detached($message, $secretKey);
        $signatureB64 = base64_encode($signature);
        $publicKeyHex = bin2hex($publicKey);

        $verifier = new Ed25519SignatureVerifier($domain);

        $this->assertTrue(
            $verifier->verify($canonical, $signatureB64, $publicKeyHex),
        );
    }

    public function testWrongPublicKeyFails(): void
    {
        $encoder = new CanonicalJsonEncoder();
        $payload = [
            'x' => 'y',
        ];
        $canonical = $encoder->encode($payload);
        $domain = 'MPO_SETTLE_V1|';
        $message = $domain . $canonical;

        $keypairA = sodium_crypto_sign_keypair();
        $publicKeyA = sodium_crypto_sign_publickey($keypairA);
        $secretKeyA = sodium_crypto_sign_secretkey($keypairA);

        $signature = sodium_crypto_sign_detached($message, $secretKeyA);
        $signatureB64 = base64_encode($signature);
        $publicKeyHexA = bin2hex($publicKeyA);

        $verifier = new Ed25519SignatureVerifier($domain);

        $keypairB = sodium_crypto_sign_keypair();
        $publicKeyBHex = bin2hex(sodium_crypto_sign_publickey($keypairB));

        $this->assertFalse(
            $verifier->verify($canonical, $signatureB64, $publicKeyBHex),
        );

        // Sanity: A verifies its own signature.
        $this->assertTrue($verifier->verify($canonical, $signatureB64, $publicKeyHexA));
    }
}

