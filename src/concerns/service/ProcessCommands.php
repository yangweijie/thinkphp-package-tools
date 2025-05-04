<?php

namespace yangweijie\thinkphpPackageTools\concerns\service;

trait ProcessCommands
{
    protected function bootPackageCommand(): self
    {
        if (empty($this->package->commands)) {
            return $this;
        }

        // 注册 ThinkPHP 命令
        foreach ($this->package->commands as $command) {
            $this->app->console->addCommand(new $command());
        }

        return $this;
    }
}