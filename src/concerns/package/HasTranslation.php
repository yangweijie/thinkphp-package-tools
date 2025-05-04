<?php

namespace yangweijie\thinkphpPackageTools\concerns\package;

trait HasTranslation
{
    public bool $hasTranslations = false;

    public function hasTranslations(): static
    {
        $this->hasTranslations = true;

        return $this;
    }
}