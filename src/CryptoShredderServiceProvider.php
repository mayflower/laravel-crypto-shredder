<?php

declare(strict_types=1);

namespace Tmogdans\LaravelCryptoShredder;

use Illuminate\Support\ServiceProvider;

class CryptoShredderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
