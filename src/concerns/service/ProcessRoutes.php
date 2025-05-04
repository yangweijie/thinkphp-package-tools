<?php

namespace yangweijie\thinkphpPackageTools\concerns\service;

trait ProcessRoutes
{
    protected function bootPackageRoute(): self
    {
        if (empty($this->package->routeFileNames)) {
            return $this;
        }

        foreach ($this->package->routeFileNames as $routeFileName) {
            $this->loadRoutesFrom("{$this->package->basePath('/../routes/')}{$routeFileName}.php");
        }

        return $this;
    }
}