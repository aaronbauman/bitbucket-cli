<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Martiis\BitbucketCli\DependencyInjection\Extension;

$container = new ContainerBuilder();
$container
    ->register(Application::class, Application::class)
    ->setArguments(['Bitbucket command line tool', '0.1']);
$container->registerExtension(new Extension());
$container->compile();

$container->get(Application::class)->run();
