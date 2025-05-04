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
        $this->commands($this->package->commands);
        return $this;
    }
}