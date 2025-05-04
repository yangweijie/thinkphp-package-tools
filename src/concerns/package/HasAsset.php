<?php

namespace yangweijie\thinkphpPackageTools\concerns\package;

trait HasAsset
{
    public bool $hasAssets = false;

    public function hasAssets(): static
    {
        $this->hasAssets = true;

        return $this;
    }
}