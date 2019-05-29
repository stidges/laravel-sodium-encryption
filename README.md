# Laravel Sodium Encryption

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

A [Sodium](https://www.php.net/manual/en/book.sodium.php) based Encrypter class for Laravel, with the same API as the built-in Encrypter class.

## Install

You can install the package via Composer:

``` bash
$ composer require stidges/laravel-sodium-encryption
```

The service provider is registered through auto-discovery, so you can start using it out of the box!

## Usage

You can resolve the Encrypter class from Laravel's [service container](https://laravel.com/docs/5.8/container):

```php
use Stidges\LaravelSodiumEncryption\Encrypter;

$encrypter = app(Encrypter::class);
// or
$encrypter = app('encrypter.sodium');
```

The API is the same as Laravel's built-in Encrypter class, so please [review the official Laravel documentation](https://laravel.com/docs/5.8/encryption) on how to use it.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email info@stidges.com instead of using the issue tracker.

## Credits

- [Stidges][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/stidges/laravel-sodium-encryption.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/stidges/laravel-sodium-encryption/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/stidges/laravel-sodium-encryption.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/stidges/laravel-sodium-encryption.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/stidges/laravel-sodium-encryption.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/stidges/laravel-sodium-encryption
[link-travis]: https://travis-ci.org/stidges/laravel-sodium-encryption
[link-scrutinizer]: https://scrutinizer-ci.com/g/stidges/laravel-sodium-encryption/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/stidges/laravel-sodium-encryption
[link-downloads]: https://packagist.org/packages/stidges/laravel-sodium-encryption
[link-author]: https://github.com/stidges
[link-contributors]: ../../contributors
