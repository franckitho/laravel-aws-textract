<?php

namespace Franckitho\Textract;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TextractServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-aws-textract')
            ->hasConfigFile('aws-textract');

        $this->app->bind('analyseDocument', function () {
            return new AnalyseDocument;
        });
    }
}
