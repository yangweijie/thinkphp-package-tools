<?php

namespace yangweijie\thinkphpPackageTools\command;

use think\console\Command;
use yangweijie\thinkphpPackageTools\adapter\laravel\LaravelCommand;
use yangweijie\thinkphpPackageTools\concerns\command\AskToRunMigrations;
use yangweijie\thinkphpPackageTools\concerns\command\AskToStarRepoOnGitHub;
use yangweijie\thinkphpPackageTools\concerns\command\PublishesResources;
use yangweijie\thinkphpPackageTools\concerns\command\SupportsServiceProviderInApp;
use yangweijie\thinkphpPackageTools\concerns\command\SupportsStartWithEndWith;
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
    /**
     * @var true
     */
    private bool $hidden;

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