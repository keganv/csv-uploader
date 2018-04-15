<?php

// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Doctrine ORM
$container['entity_manager'] = function ($c) {
    $settings = $c->get('settings');
    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
        $settings['doctrine']['meta']['paths'],
        $settings['doctrine']['meta']['isDevMode'],
        $settings['doctrine']['meta']['proxyDir'],
        $settings['doctrine']['meta']['cache'],
        $settings['doctrine']['meta']['useSimpleAnnotationReader']
    );
    return \Doctrine\ORM\EntityManager::create($settings['doctrine']['connection'], $config);
};
