<?php

declare(strict_types=1);

namespace Meanly\Mdk\Kernel\Tests;

use Meanly\Mdk\Kernel\Identity\CanonicalJsonEncoder;
use PHPUnit\Framework\TestCase;

final class CanonicalJsonEncoderTest extends TestCase
{
    public function testSortsObjectKeysLexicographically(): void
    {
        $enc = new CanonicalJsonEncoder();
        $this->assertSame(
            '{"a":1,"b":"x"}',
            $enc->encode(['b' => 'x', 'a' => 1]),
        );
    }

    public function testNestedAssociativeAndList(): void
    {
        $enc = new CanonicalJsonEncoder();
        $data = [
            'z' => ['b' => 2, 'a' => 1],
            'y' => [3, 2, 1],
        ];
        $this->assertSame(
            '{"y":[3,2,1],"z":{"a":1,"b":2}}',
            $enc->encode($data),
        );
    }

    public function testFloatUsesJsonNumberToken(): void
    {
        $enc = new CanonicalJsonEncoder();
        $this->assertSame(
            '{"x":99.5}',
            $enc->encode(['x' => 99.5]),
        );
    }

    public function testListEncoding(): void
    {
        $enc = new CanonicalJsonEncoder();
        $this->assertSame('[]', $enc->encode([]));
        $this->assertSame('[1,2]', $enc->encode([1, 2]));
    }

    public function testUnsupportedTypeThrows(): void
    {
        $enc = new CanonicalJsonEncoder();
        $this->expectException(\InvalidArgumentException::class);
        $enc->encode(new \stdClass());
    }
}
