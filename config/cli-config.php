<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require __DIR__ . '/../vendor/autoload.php';

$settings = include __DIR__ . '/../src/settings.php';
$settings = $settings['settings'];

$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
    $settings['doctrine']['meta']['paths'],
    $settings['doctrine']['meta']['isDevMode'],
    $settings['doctrine']['meta']['proxyDir'],
    $settings['doctrine']['meta']['cache'],
    $settings['doctrine']['meta']['useSimpleAnnotationReader']
);

$em = \Doctrine\ORM\EntityManager::create($settings['doctrine']['connection'], $config);

return ConsoleRunner::createHelperSet($em);
