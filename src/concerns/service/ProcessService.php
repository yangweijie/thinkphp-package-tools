<?php

namespace yangweijie\thinkphpPackageTools\concerns\service;

trait ProcessService
{
    protected function bootPackageServiceProviders(): self
    {
        if (! $this->package->publishableProviderName || ! $this->app->runningInConsole()) {
            return $this;
        }

        $providerName = $this->package->publishableProviderName;
        $vendorProvider = $this->package->basePath("/../resources/stubs/{$providerName}.php.stub");
        $appProvider = base_path("app/service/{$providerName}.php");

        $this->publishes([$vendorProvider => $appProvider], "{$this->package->shortName()}-provider");

        return $this;
    }
}