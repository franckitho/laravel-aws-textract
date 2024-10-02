<?php

namespace Franckitho\Textract;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Franckitho\Textract\Commands\TextractCommand;

class TextractServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-aws-textract')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_aws_textract_table')
            ->hasCommand(TextractCommand::class);
    }
}
