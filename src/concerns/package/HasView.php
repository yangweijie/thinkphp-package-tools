<?php

namespace yangweijie\thinkphpPackageTools\concerns\package;

trait HasView
{
    public bool $hasViews = false;

    public ?string $viewNamespace = null;

    public function hasViews(?string $namespace = null): static
    {
        $this->hasViews = true;

        $this->viewNamespace = $namespace;

        return $this;
    }

    public function viewNamespace(): string
    {
        return $this->viewNamespace ?? $this->shortName();
    }
}