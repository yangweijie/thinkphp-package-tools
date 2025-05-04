<?php

namespace yangweijie\thinkphpPackageTools\concerns\package;

use yangweijie\thinkphpPackageTools\command\InstallCommand;

trait HasInstallCommand
{
    public function hasInstallCommand($callable): static
    {
        $installCommand = new InstallCommand($this);

        $callable($installCommand);

        $this->consoleCommands[] = $installCommand;

        return $this;
    }
}