# Manage KoboToolBox from your Laravel Project

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stats4sd/laravel-kobo-link.svg?style=flat-square)](https://packagist.org/packages/stats4sd/laravel-kobo-link)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/stats4sd/laravel-kobo-link/run-tests?label=tests)](https://github.com/stats4sd/laravel-kobo-link/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/stats4sd/laravel-kobo-link/Check%20&%20fix%20styling?label=code%20style)](https://github.com/stats4sd/laravel-kobo-link/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/stats4sd/laravel-kobo-link.svg?style=flat-square)](https://packagist.org/packages/stats4sd/laravel-kobo-link)

[](delete) 1) manually replace `Dave Mills, stats4sd, auhor@domain.com, stats4sd, stats4sd, Vendor Name, laravel-kobo-link, laravel-kobo-link, laravel-kobo-link, KoboLink, Manage KoboToolBox from your Laravel Project` with their correct values
[](delete) in `CHANGELOG.md, LICENSE.md, README.md, ExampleTest.php, ModelFactory.php, KoboLink.php, KoboLinkCommand.php, KoboLinkFacade.php, KoboLinkServiceProvider.php, TestCase.php, composer.json, create_laravel-kobo-link_table.php.stub`
[](delete) and delete `configure-laravel-kobo-link.sh`

[](delete) 2) You can also run `./configure-laravel-kobo-link.sh` to do this automatically.

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/package-laravel-kobo-link-laravel.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/package-laravel-kobo-link-laravel)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require stats4sd/laravel-kobo-link
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Stats4sd\KoboLink\KoboLinkServiceProvider" --tag="laravel-kobo-link-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Stats4sd\KoboLink\KoboLinkServiceProvider" --tag="laravel-kobo-link-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$laravel-kobo-link = new Stats4sd\KoboLink();
echo $laravel-kobo-link->echoPhrase('Hello, Spatie!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Dave Mills](https://github.com/stats4sd)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
