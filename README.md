# config

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

PublishingKit/Config is a simple config container. It can parse the following formats:

* PHP files (useful for dynamic stuff that can change based on the environment)
* `ini` files
* YAML files

## Install

Via Composer

``` bash
$ composer require publishing-kit/config
```

## Usage

You can simply pass in an array for the configuration:

``` php
$values = [
    'foo' => 'bar'
];
$config = new PublishingKit\Config\Config($values);
echo $config->get('foo'); // returns 'bar'
```

However, in practice you're unlikely to do this. Instead, you will normally use the named constructors to create the config from a file:

```php
$config = PublishingKit\Config\Config::fromFile('config.php');
$multiConfig = PublishingKit\Config\Config::fromFiles([
    'config.php',
    'config.ini',
    'config.yml'
]);
```

Once you have a config object, you can check for existence with the `has()` method, and get the value with the `get()` method, or as a property:

```php
$config->has('foo'); // returns true
$config->get('foo'); // returns 'bar'
$config->foo; // returns 'bar'
```

Since the config object implements `ArrayAccess` and `IteratorAggregate`, you can also loop over them or access properties using array notation.

Config objects are immutable and so cannot be changed once created.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email 450801+matthewbdaly@users.noreply.github.com instead of using the issue tracker.

## Credits

- [Matthew Daly][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/publishing-kit/config.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/publishing-kit/config/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/publishing-kit/config.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/publishing-kit/config.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/publishing-kit/config.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/publishing-kit/config
[link-travis]: https://travis-ci.org/publishing-kit/config
[link-scrutinizer]: https://scrutinizer-ci.com/g/publishing-kit/config/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/publishing-kit/config
[link-downloads]: https://packagist.org/packages/publishing-kit/config
[link-author]: https://github.com/matthewbdaly
[link-contributors]: ../../contributors
