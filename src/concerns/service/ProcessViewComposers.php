<?php

namespace yangweijie\thinkphpPackageTools\concerns\service;

use Illuminate\Support\Facades\View;

trait ProcessViewComposers
{
    protected function bootPackageViewComposers(): self
    {
        if (empty($this->package->viewComposers)) {
            return $this;
        }

        foreach ($this->package->viewComposers as $viewName => $viewComposer) {
            View::composer($viewName, $viewComposer);
        }

        return $this;
    }
}