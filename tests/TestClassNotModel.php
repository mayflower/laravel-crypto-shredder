<?php

declare(strict_types=1);

namespace Mayflower\LaravelCryptoShredder\Tests;

use Mayflower\LaravelCryptoShredder\ShredderOptions;
use Mayflower\LaravelCryptoShredder\UseCryptoShredding;

class TestClassNotModel
{
    use UseCryptoShredding;

    public static function getShredderOptions(): ShredderOptions
    {
        return ShredderOptions::create()
            ->setCryptAttributes([
                'someAttribute'
            ])
            ->setCypher(ShredderOptions::CIPHER_AES_128_CBC);
    }
}
