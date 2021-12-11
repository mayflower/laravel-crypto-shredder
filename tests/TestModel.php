<?php

declare(strict_types=1);

namespace Mayflower\LaravelCryptoShredder\Tests;

use Illuminate\Database\Eloquent\Model;
use Mayflower\LaravelCryptoShredder\ShredderOptions;
use Mayflower\LaravelCryptoShredder\UseCryptoShredding;

class TestModel extends Model
{
    use UseCryptoShredding;

    protected $fillable = [
        'someAttribute'
    ];

    public $timestamps = false;

    public static function getShredderOptions(): ShredderOptions
    {
        return ShredderOptions::create()
            ->setCryptAttributes([
                'someAttribute'
            ])
            ->setCypher(ShredderOptions::CIPHER_AES_128_CBC);
    }
}
