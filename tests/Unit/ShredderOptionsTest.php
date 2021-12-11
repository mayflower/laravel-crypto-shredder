<?php

declare(strict_types=1);

namespace Unit;

use Mayflower\LaravelCryptoShredder\Tests\TestCase;
use Mayflower\LaravelCryptoShredder\ShredderOptions;

class ShredderOptionsTest extends TestCase
{
    public function testItCreatesOptions(): void
    {
        $result = ShredderOptions::create();
        $expected = ShredderOptions::class;

        self::assertInstanceOf($expected, $result);
    }

    public function testItReturnsDefaultCypher(): void
    {
        $options = ShredderOptions::create();

        $expected = 'aes-128-cbc';
        $result = $options->getCypher();

        self::assertSame($expected, $result);
    }

    public function testItReturnsChosenCypher(): void
    {
        $options = ShredderOptions::create();
        $options->setCypher(ShredderOptions::CIPHER_AES_256_GCM);

        $expected = 'aes-256-gcm';
        $result = $options->getCypher();

        self::assertSame($expected, $result);
    }

    public function testItReturnsCryptAttributes(): void
    {
        $cryptAttributes = [
            'name',
            'email'
        ];
        $options = ShredderOptions::create();
        $options->setCryptAttributes($cryptAttributes);

        $expected = [
            'name',
            'email'
        ];

        $result = $options->getCryptAttributes();

        self::assertIsArray($result);
        self::assertSame($expected, $result);
    }
}
