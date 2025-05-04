<?php

namespace yangweijie\thinkphpPackageTools\concerns\package;

trait HasRoute
{
    public array $routeFileNames = [];

    public function hasRoute(string $routeFileName): static
    {
        $this->routeFileNames[] = $routeFileName;

        return $this;
    }

    public function hasRoutes(...$routeFileNames): static
    {
        $this->routeFileNames = array_merge(
            $this->routeFileNames,
            collect($routeFileNames)->flatten()->toArray()
        );

        return $this;
    }
}