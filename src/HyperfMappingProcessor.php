<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Hyperf;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Composer;
use Hyperf\Utils\Filesystem\Filesystem;
use Kkguan\PHPMapstruct\Processor\Internal\Option\Options;
use Kkguan\PHPMapstruct\Processor\MappingProcessor;
use Kkguan\PHPMapstruct\Processor\MappingProcessorBuilder;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

#[Listener]
class HyperfMappingProcessor implements ListenerInterface
{
    private array $config = [
        'verbose' => true,
        'generated_dir' => BASE_PATH . '/runtime/mapstruct',
        'enable_cache' => false,
    ];

    public function __construct(private ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event): void
    {
        $classes = AnnotationCollector::getClassesByAnnotation(Mapper::class);

        $parserFactory = new ParserFactory();
        $parser = $parserFactory->create(ParserFactory::ONLY_PHP7);
        $dumper = new NodeDumper();
        $standard = new Standard();
        $filesystem = new Filesystem();
        $logger = $this->container->get(StdoutLoggerInterface::class);
        // TODO: configFile
        $HyperfConfig = $this->container->get(ConfigInterface::class);
        $config = $HyperfConfig->get('mapstruct', []);
        $config = array_merge($this->config, $config);
        $options = new Options();
        $runtimePath = $config['generated_dir'];
        $options->setGeneratedSourcesDirectory($runtimePath);
        $options->setLogger($logger);
        $options->setVerbose($config['verbose'] ?? false);
        $builder = (new MappingProcessorBuilder())->setAnnotationClasses(array_keys($classes));

        (new MappingProcessor())->init($options)->process($builder);

        $container = ApplicationContext::getContainer();
        foreach ($classes as $className => $annotation) {
            $name = $className . 'Impl';
            Composer::getLoader()->addClassMap([
                $name => $runtimePath . '/gem/' . str_replace('\\', '/', $className) . 'Impl.php',
                $className => $runtimePath . '/gem/' . str_replace('\\', '/', $className) . 'Impl.php',
            ]);
            $container->set($className, new $name());
        }

        // $csFixConfigFile = __DIR__ . '/../.php-cs-fixer.php';

        // shell_exec(sprintf('./vendor/bin/php-cs-fixer fix --config=%s %s', $csFixConfigFile, $runtimePath));
    }
}
