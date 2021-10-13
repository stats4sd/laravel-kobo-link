<?php

namespace Stats4sd\KoboLink;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stats4sd\KoboLink\Commands\KoboLinkCommand;
use Stats4sd\KoboLink\Models\Team;
use Stats4sd\KoboLink\Models\Xlsform;
use Stats4sd\KoboLink\Observers\TeamObserver;
use Stats4sd\KoboLink\Observers\XlsformObserver;

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
            ->hasMigration('create_kobo_link_tables')
            ->hasRoute('backpack/kobolink')
            ->hasCommand(KoboLinkCommand::class);

        // Add extra config merge(s)
        //$this->mergeConfigFrom($this->package->basePath("/../config/services.php"), $configFileName);
    }

    public function bootingPackage()
    {
        XlsForm::observe(XlsformObserver::class);
        Team::observe(TeamObserver::class);
    }
}
