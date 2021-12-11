# Laravel Crypto Shredder

This package provides a trait that will automatically encrypt attributes when saving an Eloquent model and decrypt when retrieving it.
For each entry it generates a separate key. The dataset is shredded if you delete this key.

## Installation

### via Composer

You can install the package via composer:

```
composer require mayflower/laravel-crypto-shredder
```

### Service provider

The package will automatically register its service provider.

### Migrations

Migrate the table for the keys.

```
php artisan migrate
```

## Usage

Your Eloquent models should use the `Mayflower\LaravelCryptoShredder\UseCryptoShredding` trait and the `Mayflower\LaravelCryptoShredder\ShredderOptions` class.

The trait contains an abstract method `getShredderOptions()` that you must implement yourself.

With the `ShredderOptions` class you can specify which attributes of your Eloquent model should be encrypted.

These fields must be of type `varchar` in your database (`string` in your migrations), no matter of the real type of your attribute.

### Example

```php
namespace App;

class YourEloquentModel extends Model
{
    use UseCryptoShredding;
  
    public static function getShredderOptions(): ShredderOptions
    {
        return ShredderOptions::create()
            ->setCryptAttributes([
                'attribute1', // list all attributes you want to encrypt
                'attribute2',
            ])
            ->setCypher(ShredderOptions::CIPHER_AES_128_CBC);
    }
}
```

That's it! Your values now are automatically encrypted on saving your model and decrypted when retrieving it.
A key is generated when your model is created the first time and saved in table `keys`.
Your models data now is safe.

If you delete the key to a data record, this data can no longer be decrypted.

## Testing

```
composer phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
