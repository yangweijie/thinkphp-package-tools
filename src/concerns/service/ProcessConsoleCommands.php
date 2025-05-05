<?php

namespace yangweijie\thinkphpPackageTools\concerns\service;

trait ProcessConsoleCommands
{
    protected function bootPackageConsoleCommands(): self
    {
        if (empty($this->package->consoleCommands) || ! $this->app->runningInConsole()) {
            return $this;
        }

        $this->commands($this->package->consoleCommands);

        return $this;
    }
}