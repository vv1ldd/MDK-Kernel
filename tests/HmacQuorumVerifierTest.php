<?php

declare(strict_types=1);

namespace Meanly\Mdk\Kernel\Tests;

use Meanly\Mdk\Kernel\Settlement\HmacQuorumVerifier;
use PHPUnit\Framework\TestCase;

final class HmacQuorumVerifierTest extends TestCase
{
    public function testSingleSecretSingleSignature(): void
    {
        $v = new HmacQuorumVerifier();
        $canonical = '{"a":1,"b":"x"}';
        $secret = 'test-secret';
        $sig = hash_hmac('sha256', $canonical, $secret);
        $this->assertTrue($v->verify($canonical, [$sig], [$secret], 1));
    }

    public function testWrongSignatureRejected(): void
    {
        $v = new HmacQuorumVerifier();
        $canonical = '{"a":1}';
        $secret = 'test-secret';
        $this->assertFalse($v->verify($canonical, [str_repeat('ab', 32)], [$secret], 1));
    }

    public function testTwoOfTwoQuorum(): void
    {
        $v = new HmacQuorumVerifier();
        $canonical = '{"order":42}';
        $s1 = 'alpha';
        $s2 = 'beta';
        $sig1 = hash_hmac('sha256', $canonical, $s1);
        $sig2 = hash_hmac('sha256', $canonical, $s2);
        $this->assertTrue($v->verify($canonical, [$sig1, $sig2], [$s1, $s2], 2));
    }

    public function testQuorumNotMet(): void
    {
        $v = new HmacQuorumVerifier();
        $canonical = '{"x":true}';
        $s1 = 'a';
        $s2 = 'b';
        $sig1 = hash_hmac('sha256', $canonical, $s1);
        $this->assertFalse($v->verify($canonical, [$sig1], [$s1, $s2], 2));
    }
}
