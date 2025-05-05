<?php
namespace yangweijie\thinkphpPackageTools\adapter\laravel;
use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Console\Parser;
use Illuminate\Console\View\Components\Factory;
use InvalidArgumentException;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\console\output\driver\Buffer;
use think\console\Table;

/**
 * Laravel命令适配器
 *
 * 此trait用于将Laravel风格的命令适配到ThinkPHP的命令系统
 * 使用方法：
 * 1. 将原Laravel命令类的继承改为 think\console\Command
 * 2. 使用此trait
 * 3. 保留原有的signature和description属性
 */
trait LaravelCommand
{
    use InteractsWithIO;

    /**
     * 命令签名
     *
     * @var string
     */
    protected string $signature;

    /**
     * 命令描述
     *
     * @var string
     */
    protected string $description;

    /**
     * The console components factory.
     *
     * @var Factory
     *
     * @internal This property is not meant to be used or overwritten outside the framework.
     */
    protected $components;

    /**
     * 配置命令
     *
     * 自动将Laravel风格的signature转换为ThinkPHP的configure方法配置
     */
    protected function configure(): void
    {
        if (isset($this->signature)) {
            $this->configureUsingFluentDefinition();
        }
        if(isset($this->description)){
            $this->setDescription($this->description);
        }
    }

    /**
     * Configure the console command using a fluent definition.
     *
     * @return void
     */
    protected function configureUsingFluentDefinition(): void
    {
        [$name, $arguments, $options] = Parser::parse($this->signature);
        $this->setName($name);
        $thinkArguments = [];
        foreach ($arguments as $argument) {
            $thinkArguments[] = new Argument($argument->getName(), $this->getArgumentMode($argument), $argument->getDescription(), $argument->getDefault());
        }
        $this->getDefinition()->addArguments($thinkArguments);
        $thinkOptions = [];
        foreach ($options as $option) {
            $thinkOptions[] = new Option($option->getName(), $option->getShortcut(), $this->getOptionMode($option), $option->getDescription(), $option->getDefault() == false? null : $option->getDefault());
        }
        $this->getDefinition()->addOptions($thinkOptions);
    }

    protected function getArgumentMode(InputArgument $argument): int
    {
        if($argument->isRequired()){
            return $argument->isArray()? Argument::REQUIRED | Argument::IS_ARRAY : Argument::REQUIRED;
        }else{
            return $argument->isArray()? Argument::OPTIONAL | Argument::IS_ARRAY : Argument::OPTIONAL;
        }
    }

    protected function getOptionMode(InputOption $option): int
    {
        if($option->isValueOptional()){
            return $option->isArray()? Option::VALUE_OPTIONAL | Option::VALUE_IS_ARRAY : Option::VALUE_OPTIONAL;
        }else{
            return $option->isValueRequired()? Option::VALUE_REQUIRED : Option::VALUE_NONE;
        }
    }

    /**
     * 执行命令
     *
     * 将ThinkPHP的Input和Output对象适配到Laravel命令的handle方法
     * @throws ReflectionException
     */
    protected function execute(Input $input, Output $output)
    {
        // 如果存在handle方法，则调用它
        if (method_exists($this, 'handle')) {
            // 获取handle方法的参数
            $reflectionMethod = new ReflectionMethod($this, 'handle');
            $parameters = $reflectionMethod->getParameters();

            $args = [];
            foreach ($parameters as $parameter) {
                $paramType = $parameter->getType();

                // 如果参数类型是Input或Output，则传入对应对象
                if ($paramType && !$paramType->isBuiltin()) {
                    $typeName = $paramType->getName();
                    if (is_a(Input::class, $typeName, true)) {
                        $args[] = $input;
                    } elseif (is_a(Output::class, $typeName, true)) {
                        $args[] = $output;
                    } else {
                        // 尝试从容器中解析其他依赖
                        $args[] = app($typeName);
                    }
                } else {
                    // 对于其他类型的参数，传入null
                    $args[] = null;
                }
            }

            // 调用handle方法
            return $reflectionMethod->invokeArgs($this, $args);
        }

        return 0;
    }


    /**
     * 输出成功信息
     *
     * @param string $string 输出内容
     * @return void
     */
    public function success(string $string): void
    {
        $this->line($string, 'success');
    }

    /**
     * 调用另一个命令
     *
     * 此方法兼容Laravel的Artisan::call方法，将调用转发到ThinkPHP的命令系统
     *
     * @param string $command 命令名称
     * @param array $arguments 命令参数和选项
     * @return Output|Buffer 命令执行的返回值
     */
    public function call(string $command, array $arguments = [], string $driver = 'buffer'): Buffer|Output
    {
        // 获取应用实例
        $app = app();

        // 创建命令行应用实例
        $console = $app->console;

        // 准备参数数组
        $parameters = [$command];

        // 添加参数
        foreach ($arguments as $key => $value) {
            if (is_numeric($key)) {
                // 位置参数
                $parameters[] = $value;
            } else {
                // 选项参数
                $key = ltrim($key, '-');

                if (is_bool($value)) {
                    if ($value) {
                        $parameters[] = $key;
                    }
                } else {
                    $parameters[$key] = $value;
                }
            }
        }

        // 执行命令并返回结果
        return $console->call($command, $parameters, $driver);
    }

    /**
     * Call another console command without output.
     *
     * @param string|Command $command
     * @param  array  $arguments
     * @return Buffer|Output
     */
    public function callSilent(Command|string $command, array $arguments = []): Buffer|Output
    {
        return $this->call($command, $arguments, 'Nothing');
    }

    /**
     * 输出表格
     * @param Table $table
     * @return string
     */
    protected function table(Table $table): string
    {
        $content = $table->render();
        $this->output->writeln($content);
        return $content;
    }
}