<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Martiis\BitbucketCli\DependencyInjection\Extension;

$container = new ContainerBuilder();
$container
    ->register(Application::class, Application::class)
    ->setArguments(['Bitbucket command line tool', '0.1']);
$container->registerExtension(new Extension());
$container->addCompilerPass(new class implements CompilerPassInterface {
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('console.command') as $id => $tags) {
            $container
                ->getDefinition(Application::class)
                ->addMethodCall('add', [new Reference($id)]);
        }
    }
});

$container->loadFromExtension('martiis_bitbucket_cli');
$container->compile();

$container->get(Application::class)->run();
