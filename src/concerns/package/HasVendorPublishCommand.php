<?php

namespace yangweijie\thinkphpPackageTools\concerns\package;

use yangweijie\thinkphpPackageTools\command\VendorPublishCommand;

trait HasVendorPublishCommand
{
    public function HasVendorPublishCommand($callable): static
    {
        $installCommand = new VendorPublishCommand($this);

        $callable($installCommand);

        $this->consoleCommands[] = $installCommand;

        return $this;
    }
}