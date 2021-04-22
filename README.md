# Manage KoboToolBox from your Laravel Project

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stats4sd/laravel-kobo-link.svg?style=flat-square)](https://packagist.org/packages/stats4sd/laravel-kobo-link)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/stats4sd/laravel-kobo-link/run-tests?label=tests)](https://github.com/stats4sd/laravel-kobo-link/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/stats4sd/laravel-kobo-link/Check%20&%20fix%20styling?label=code%20style)](https://github.com/stats4sd/laravel-kobo-link/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/stats4sd/laravel-kobo-link.svg?style=flat-square)](https://packagist.org/packages/stats4sd/laravel-kobo-link)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require stats4sd/laravel-kobo-link
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Stats4sd\KoboLink\KoboLinkServiceProvider" --tag="kobo-link-migrations"
php artisan migrate
```

### Setup Required Configuration Variables

In order to link up to a KoBoToolbox server, you must provide the following environment variables:

```
KOBO_ENDPOINT=
KOBO_OLD_ENDPOINT=
KOBO_USERNAME=
KOBO_PASSWORD=
```

The two endpoint variables should be the full url to the server you are using. For example:
```
## If you use the 'for everyone else' server provided by the team at https://kobotoolbox.org:
KOBO_ENDPOINT=https://kf.kobotoolbox.org,
KOBO_OLD_ENDPOINT=https://kc.kobotoolbox.org

## If you use their humanitarian server, use:
KOBO_ENDPOINT=https://kobo.humanitarianresponse.info
KOBO_OLD_ENDPOINT=https://kc.humanitarianresponse.info
```

The platform requires a 'primary' user account on the KoboToolbox server to manage deployments of ODK forms. This account will *own* every form published by the platform. We HIGHLY recommend creating an account specifically for the Laravel application. If the application uses an account also used by other users, there is a chance that your database will become out of sync with the forms present on KoBoToolbox, and the form management functions may stop working correctly.

### Publishing The config

If you add the required ENV variables to your application, there should be no need to publish the config file. However, you may wish to do so anyway. To publish the file, use:

```bash
php artisan vendor:publish --provider="Stats4sd\KoboLink\KoboLinkServiceProvider" --tag="laravel-kobo-link-config"
```


## Add the CrudControllers to the Sidebar
This package assumes you are using Laravel Backpack as your admin panel. As such, it comes with a set of CrudControllers for managing your XLS forms and submissions. 

You can add links to these crud panels into your sidebar:

```
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('xlsform') }}'><i class="lab la-wpforms nav-icon"></i> XLSForms</a></li>
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
