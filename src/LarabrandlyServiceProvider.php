<?php

namespace KemokRepos\Larabrandly;

use KemokRepos\Larabrandly\Http\RebrandlyClient;
use KemokRepos\Larabrandly\Services\RebrandlyService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LarabrandlyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('larabrandly')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(RebrandlyClient::class, function ($app) {
            $apiKey = config('larabrandly.api_key');

            if (empty($apiKey)) {
                throw new \InvalidArgumentException('Rebrandly API key is required. Please set REBRANDLY_API_KEY in your .env file.');
            }

            return new RebrandlyClient($apiKey);
        });

        $this->app->singleton(RebrandlyService::class, function ($app) {
            return new RebrandlyService($app->make(RebrandlyClient::class));
        });

        $this->app->singleton(\KemokRepos\Larabrandly\Services\TagService::class, function ($app) {
            return new \KemokRepos\Larabrandly\Services\TagService($app->make(RebrandlyClient::class));
        });

        $this->app->alias(RebrandlyService::class, 'rebrandly');
        $this->app->alias(\KemokRepos\Larabrandly\Services\TagService::class, 'rebrandly-tags');
    }
}
