<?php

declare(strict_types=1);

namespace Mayflower\LaravelCryptoShredder\Tests;

use Mayflower\LaravelCryptoShredder\CryptoShredderServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            CryptoShredderServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
