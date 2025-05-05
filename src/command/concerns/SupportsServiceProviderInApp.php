<?php

namespace yangweijie\thinkphpPackageTools\commands\concerns;

use Illuminate\Support\Str;

trait SupportsServiceProviderInApp
{
    protected bool $copyServiceProviderInApp = false;

    public function copyAndRegisterServiceProviderInApp(): self
    {
        $this->copyServiceProviderInApp = true;

        return $this;
    }

    protected function processCopyServiceProviderInApp(): self
    {
        if ($this->copyServiceProviderInApp) {
            $this->comment('Publishing service provider...');

            $this->copyServiceProviderInApp();
        }

        return $this;
    }

    protected function copyServiceProviderInApp(): self
    {
        $providerName = $this->package->publishableProviderName;

        if (! $providerName) {
            return $this;
        }

        $this->callSilent('vendor:pub', ['--tag' => $this->package->shortName() . '-service']);

        $namespace = Str::replaceLast('\\', '', $this->app->getNamespace());

        $providerPath = app_path('service.php');

        $providersConfig = file_get_contents($providerPath);

        $class = '\\Service\\' . Str::replace('/', '\\', $providerName) . '::class';

        if (Str::contains($providersConfig, $namespace . $class)) {
            return $this;
        }

        file_put_contents($providerPath, str_replace(
            "];",
            "   $namespace.$class::class\n];",
                $providersConfig
        ));

        return $this;
    }
}