<?php

declare(strict_types=1);

namespace Tmogdans\LaravelCryptoShredder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\Encrypter;
use Tmogdans\LaravelCryptoShredder\Models\Key;

trait UseCryptoShredding
{
    private string $cypher = 'aes-128-cbc';

    protected static function bootUseCryptoShredding(): void
    {
        $key = Encrypter::generateKey('aes-128-cbc');

        static::creating(function (Model $model) use ($key) {
            $model->encryptOnCreate($key);
        });

        static::created(function (Model $model) use ($key) {
            $model->saveKey($model, $key);
        });

        static::updating(function (Model $model) {
            $key = $model->retrieveKey($model);
            $model->encryptOnUpdate($key);
        });

        static::retrieved(function (Model $model) {
            $key = $model->retrieveKey($model);
            $model->decryptOnRetrieve($key);
        });
    }

    protected function encryptOnCreate(string $key): void
    {
        $encrypter = new Encrypter($key, $this->cypher);

        foreach ($this->crypt as $attribute) {
            $this->setAttribute($attribute, $encrypter->encrypt($this->$attribute));
        }
    }

    protected function encryptOnUpdate(string $key): void
    {
        $encrypter = new Encrypter($key, $this->cypher);

        foreach ($this->crypt as $attribute) {
            $this->setAttribute($attribute, $encrypter->encrypt($this->$attribute));
        }
    }

    protected function decryptOnRetrieve(string $key): void
    {
        $decrypter = new Encrypter($key, $this->cypher);

        foreach ($this->crypt as $attribute) {
            $this->setAttribute($attribute, $decrypter->decrypt($this->$attribute));
        }
    }

    protected function saveKey(Model $model, string $key): void
    {
        $keyModel = new Key([
            'model' => $model::class,
            'model_id' => $model->getAttribute('id'),
            'key' => $key
        ]);
        $keyModel->save();
    }

    protected function retrieveKey(Model $model): string
    {
        $key = Key::where('model', $model::class)
            ->where('model_id', $model->getAttribute('id'))
            ->first();

        if (!$key instanceof Key) {
            throw new \InvalidArgumentException();
        }

        return $key->key;
    }
}
