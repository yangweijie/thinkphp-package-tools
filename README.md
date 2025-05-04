# thinkphp-package-tools
参考laravel-package-tools 实现的开发ThinkPHP扩展的包

This package contains a `PackageService` that you can use in your packages to easily register config files,
migrations, and more.

Here's an example of how it can be used.

```php
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Package;
use MyPackage\ViewComponents\Alert;
use Spatie\LaravelPackageTools\Commands\Concerns;

class YourPackageServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('your-package-name')
            ->hasConfigFile()
            ->hasViews()
            ->hasViewComponent('spatie', Alert::class)  // 仅laravel 迁移过来的扩展
            ->hasViewComposer('*', MyViewComposer::class) // 仅laravel 迁移过来的扩展
            ->sharesDataWithAllViews('downloads', 3)   // 仅laravel 迁移过来的扩展
            ->hasTranslations()      // 仅laravel 迁移过来的扩展
            ->hasAssets()
            ->publishesServiceProvider('MyProviderName')
            ->hasRoute('web')
            ->hasMigration('create_package_tables')
            ->hasCommand(YourCoolPackageCommand::class)             // 无需在 composer.json 中注册命令
            ->hasInstallCommand(function(InstallCommand $command) {   // 添加独立的安装命令
                $command
                    ->publishConfigFile()
                    ->publishAssets()
                    ->publishMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub();
            });
    }
}
```

Under the hood it will do the necessary work to register the necessary things and make all sorts of files publishable.