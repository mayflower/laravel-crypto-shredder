<?php

declare(strict_types=1);

namespace Tmogdans\LaravelCryptoShredder;

class ShredderOptions
{
    public const CIPHER_AES_128_CBC = 'aes-128-cbc';

    public const CIPHER_AES_256_CBC = 'aes-256-cbc';

    public const CIPHER_AES_128_GCM = 'aes-128-gcm';

    public const CIPHER_AES_256_GCM = 'aes-256-gcm';

    private string $cypher = self::CIPHER_AES_128_CBC;

    private array $cryptAttributes = [];

    public static function create(): static
    {
        return new static();
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

    public function getCryptAttributes(): array
    {
        return $this->cryptAttributes;
    }

    public function setCryptAttributes(array $cryptAttributes): self
    {
        $this->cryptAttributes = $cryptAttributes;

        return $this;
    }
}
