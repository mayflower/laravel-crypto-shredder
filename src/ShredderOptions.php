<?php

declare(strict_types=1);

namespace Mayflower\LaravelCryptoShredder;

use JetBrains\PhpStorm\Pure;

final class ShredderOptions
{
    public const CIPHER_AES_128_CBC = 'aes-128-cbc';

    public const CIPHER_AES_256_CBC = 'aes-256-cbc';

    public const CIPHER_AES_128_GCM = 'aes-128-gcm';

    public const CIPHER_AES_256_GCM = 'aes-256-gcm';

    private string $cypher = self::CIPHER_AES_128_CBC;

    /** @var array<int, string> $cryptAttributes */
    private array $cryptAttributes = [];

    public static function create(): ShredderOptions
    {
        return new self();
    }

    public function getCypher(): string
    {
        return $this->cypher;
    }

    public function setCypher(string $cypher): self
    {
        $this->cypher = $cypher;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getCryptAttributes(): array
    {
        return $this->cryptAttributes;
    }

    /**
     * @param array<int, string> $cryptAttributes
     */
    public function setCryptAttributes(array $cryptAttributes): self
    {
        $this->cryptAttributes = $cryptAttributes;

        return $this;
    }
}
