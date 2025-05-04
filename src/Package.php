<?php
namespace yangweijie\thinkphpPackageTools;


use Illuminate\Support\Str;
use yangweijie\thinkphpPackageTools\concerns\package\HasAsset;
use yangweijie\thinkphpPackageTools\concerns\package\HasBladeComponent;
use yangweijie\thinkphpPackageTools\concerns\package\HasCommand;
use yangweijie\thinkphpPackageTools\concerns\package\HasConfig;
use yangweijie\thinkphpPackageTools\concerns\package\HasInertia;
use yangweijie\thinkphpPackageTools\concerns\package\HasInstallCommand;
use yangweijie\thinkphpPackageTools\concerns\package\HasMigration;
use yangweijie\thinkphpPackageTools\concerns\package\HasRoute;
use yangweijie\thinkphpPackageTools\concerns\package\HasService;
use yangweijie\thinkphpPackageTools\concerns\package\HasTranslation;
use yangweijie\thinkphpPackageTools\concerns\package\HasView;
use yangweijie\thinkphpPackageTools\concerns\package\HasViewComposer;
use yangweijie\thinkphpPackageTools\concerns\package\HasViewSharedData;

class Package
{

    use HasAsset;
    use HasBladeComponent;
    use HasCommand;
    use HasConfig;
    use HasInertia;
    use HasInstallCommand;
    use HasMigration;
    use HasRoute;
    use HasService;
    use HasTranslation;
    use HasViewComposer;
    use HasView;
    use HasViewSharedData;

    public string $name;
    public string $basePath;


    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function shortName(): string
    {
        return Str::after($this->name, (Str::startsWith('thinkphp', $this->name)?'thinkphp-':'think-'));
    }

    public function basePath(?string $directory = null): string
    {
        if ($directory === null) {
            return $this->basePath;
        }

        return $this->basePath . DIRECTORY_SEPARATOR . ltrim($directory, DIRECTORY_SEPARATOR);
    }

    public function setBasePath(string $path): self
    {
        $this->basePath = $path;

        return $this;
    }
}