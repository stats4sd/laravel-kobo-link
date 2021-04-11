<?php

namespace Stats4sd\KoboLink;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stats4sd\KoboLink\Commands\KoboLinkCommand;

class KoboLinkServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-kobo-link')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-kobo-link_table')
            ->hasCommand(KoboLinkCommand::class);
    }
}
