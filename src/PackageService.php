<?php

namespace yangweijie\thinkphpPackageTools;

use ReflectionClass;
use think\Service;
use yangweijie\thinkphpPackageTools\concerns\adapter\laravel\LaravelService;
use yangweijie\thinkphpPackageTools\concerns\service\ProcessAssets;
use yangweijie\thinkphpPackageTools\concerns\service\ProcessBladeComponents;
use yangweijie\thinkphpPackageTools\concerns\service\ProcessCommands;
use yangweijie\thinkphpPackageTools\concerns\service\ProcessConfigs;
use yangweijie\thinkphpPackageTools\concerns\service\ProcessInertia;
use yangweijie\thinkphpPackageTools\concerns\service\ProcessMigrations;
use yangweijie\thinkphpPackageTools\concerns\service\ProcessRoutes;
use yangweijie\thinkphpPackageTools\concerns\service\ProcessService;
use yangweijie\thinkphpPackageTools\concerns\service\ProcessTranslations;
use yangweijie\thinkphpPackageTools\concerns\service\ProcessViewComposers;
use yangweijie\thinkphpPackageTools\concerns\service\ProcessViews;
use yangweijie\thinkphpPackageTools\concerns\service\ProcessViewSharedData;
use yangweijie\thinkphpPackageTools\exception\InvalidPackage;

abstract class PackageService extends Service
{
    use LaravelService;
    use ProcessAssets;
    use ProcessBladeComponents;
    use ProcessCommands;
    use ProcessConfigs;
    use ProcessInertia;
    use ProcessMigrations;
    use ProcessRoutes;
    use ProcessService;
    use ProcessTranslations;
    use ProcessViewComposers;
    use ProcessViews;
    use ProcessViewSharedData;

    protected Package $package;

    abstract public function configurePackage(Package $package): void;

    /** @throws InvalidPackage */
    public function register()
    {
        $this->registeringPackage();

        $this->package = $this->newPackage();
        $this->package->setBasePath($this->getPackageBaseDir());

        $this->configurePackage($this->package);

        $this->registerPackageConfig();

        $this->packageRegistered();

        return $this;
    }

    public function boot()
    {
        $this->bootingPackage();

        $this->bootPackageConfig()
            ->bootPackageCommand()
            ->bootPackageMigration()
            ->bootPackageRoute()
            ->packageBooted();

        return $this;
    }

    protected function getPackageBaseDir(): string
    {
        $reflector = new ReflectionClass(get_class($this));

        $packageBaseDir = dirname($reflector->getFileName());

        // Some packages like to keep ThinkPHP directory structure and place
        // the service providers in a Providers folder.
        // move up a level when this is the case.
        if (str_ends_with($packageBaseDir, DIRECTORY_SEPARATOR.'Service')) {
            $packageBaseDir = dirname($packageBaseDir);
        }

        return $packageBaseDir;
    }

    // 其他方法...
    private function registeringPackage()
    {
    }

    private function newPackage(): Package
    {
        return new Package();
    }

    private function packageRegistered()
    {
    }

    private function bootingPackage()
    {
    }



    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param string $path
     * @param string $key
     * @return void
     */
    protected function mergeConfigFrom(string $path, string $key): void
    {
        $config = $this->app->make('config');
        $config->set(array_merge(
            require $path, $config->get($key, [])
        ), $key);
    }

    private function packageBooted()
    {
    }

    public function packageView(?string $namespace): ?string
    {
        return is_null($namespace)
            ? $this->package->shortName()
            : $this->package->viewNamespace;
    }

    /**
     * Register a view file namespace.
     *
     * @param array|string $path
     * @param string $namespace
     * @return void
     */
    protected function loadViewsFrom(array|string $path, string $namespace): void
    {
        $this->callAfterResolving('view', function ($view) use ($path, $namespace) {
            if (isset($this->app->config['view']['paths']) &&
                is_array($this->app->config['view']['paths'])) {
                foreach ($this->app->config['view']['paths'] as $viewPath) {
                    if (is_dir($appPath = $viewPath.'/vendor/'.$namespace)) {
                        $view->addNamespace($namespace, $appPath);
                    }
                }
            }

            $view->addNamespace($namespace, $path);
        });
    }

    /**
     * Register a translation file namespace or path.
     *
     * @param string $path
     * @param string|null $namespace
     * @return void
     */
    protected function loadTranslationsFrom(string $path, string $namespace = null): void
    {
        $this->callAfterResolving('translator', fn ($translator) => is_null($namespace)
            ? $translator->addPath($path)
            : $translator->addNamespace($namespace, $path));
    }

    /**
     * Register a JSON translation file path.
     *
     * @param string $path
     * @return void
     */
    protected function loadJsonTranslationsFrom(string $path): void
    {
        $this->callAfterResolving('translator', function ($translator) use ($path) {
            $translator->addJsonPath($path);
        });
    }
}