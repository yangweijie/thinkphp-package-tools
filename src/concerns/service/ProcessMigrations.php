<?php

namespace yangweijie\thinkphpPackageTools\concerns\service;

trait ProcessMigrations
{
    protected function bootPackageMigration(): self
    {
        // 实现 ThinkPHP 的迁移文件注册逻辑
        if ($this->package->runsMigrations) {
            // 注册迁移目录或文件
            $this->loadMigrationsFrom($this->package->basePath('../database/migrations'));
        }

        return $this;
    }

    protected function loadMigrationsFrom(string $path): void
    {
        // 实现 ThinkPHP 的迁移文件加载逻辑
        // ...
    }
}