<?php

declare(strict_types=1);

namespace Mayflower\LaravelCryptoShredder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\Encrypter;
use Mayflower\LaravelCryptoShredder\Models\Key;

trait UseCryptoShredding
{
    protected ShredderOptions $shredderOptions;

    abstract public static function getShredderOptions(): ShredderOptions;

    protected static function bootUseCryptoShredding(): void
    {
        $key = Encrypter::generateKey(self::getShredderOptions()->getCypher());

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
        $encrypter = new Encrypter($key, self::getShredderOptions()->getCypher());

        foreach (self::getShredderOptions()->getCryptAttributes() as $attribute) {
            $this->setAttribute($attribute, $encrypter->encrypt($this->$attribute));
        }
    }

    protected function encryptOnUpdate(string $key): void
    {
        $encrypter = new Encrypter($key, self::getShredderOptions()->getCypher());

        foreach (self::getShredderOptions()->getCryptAttributes() as $attribute) {
            $this->setAttribute($attribute, $encrypter->encrypt($this->$attribute));
        }
    }

    protected function decryptOnRetrieve(string $key): void
    {
        $decrypter = new Encrypter($key, self::getShredderOptions()->getCypher());

        foreach (self::getShredderOptions()->getCryptAttributes() as $attribute) {
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
