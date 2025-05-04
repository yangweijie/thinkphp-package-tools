<?php

namespace yangweijie\thinkphpPackageTools\command;

use think\console\Command;
use yangweijie\thinkphpPackageTools\commands\concerns\AskToRunMigrations;
use yangweijie\thinkphpPackageTools\commands\concerns\AskToStarRepoOnGitHub;
use yangweijie\thinkphpPackageTools\commands\concerns\PublishesResources;
use yangweijie\thinkphpPackageTools\commands\concerns\SupportsServiceProviderInApp;
use yangweijie\thinkphpPackageTools\commands\concerns\SupportsStartWithEndWith;
use yangweijie\thinkphpPackageTools\concerns\adapter\laravel\LaravelCommand;
use yangweijie\thinkphpPackageTools\Package;

class InstallCommand extends Command
{
    use AskToRunMigrations;
    use AskToStarRepoOnGitHub;
    use PublishesResources;
    use LaravelCommand;
    use SupportsServiceProviderInApp;
    use SupportsStartWithEndWith;

    protected Package $package;

    public function __construct(Package $package)
    {
        $this->signature = $package->shortName() . ':install';

        $this->description = 'Install ' . $package->name;

        $this->package = $package;

        $this->hidden = true;

        parent::__construct();
    }

    public function handle()
    {
        $this
            ->processStartWith()
            ->processPublishes()
            ->processAskToRunMigrations()
            ->processCopyServiceProviderInApp()
            ->processStarRepo()
            ->processEndWith();

        $this->info("{$this->package->shortName()} has been installed!");
    }
}