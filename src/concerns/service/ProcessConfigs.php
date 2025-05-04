<?php

namespace yangweijie\thinkphpPackageTools\concerns\service;

trait ProcessConfigs
{
    public function registerPackageConfig(): self
    {
        if (empty($this->package->configFileNames)) {
            return $this;
        }

        foreach ($this->package->configFileNames as $configFileName) {
            $vendorConfig = $this->package->basePath("/../config/{$configFileName}.php");

            // Only mergeConfigFile if a .php file and not if a stub file
            if (! is_file($vendorConfig)) {
                continue;
            }

            $this->mergeConfigFrom($vendorConfig, $configFileName);
        }

        return $this;
    }

    protected function bootPackageConfig(): self
    {
        if (empty($this->package->configFileNames)) {
            return $this;
        }

        foreach ($this->package->configFileNames as $configFileName) {
            $vendorConfig = $this->package->basePath("/../config/{$configFileName}.php");

            if (!is_file($vendorConfig)) {
                continue;
            }

            // ThinkPHP 配置加载方式
            $this->app->config->load($vendorConfig, $configFileName);
        }

        return $this;
    }
}