<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'outputBuffering' => 'prepend',

        // Cache
        'routerCacheFile' => __DIR__ . '/../cache/routes.php',

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'application',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        'doctrine' => [
            'meta' => [
                'paths' => [
                    'src/Entity'
                ],
                'isDevMode' => false,
                'autoGenerateProxies' => true,
                'proxyDir' =>  __DIR__ . '/../cache/proxies',
                'cache' => null,
                'useSimpleAnnotationReader' => false,
            ],
            'connection' => [
                'driver'   => 'pdo_mysql',
                'host'     => '127.0.0.1',
                'dbname'   => 'db_name',
                'user'     => 'db_username',
                'password' => 'db_password',
            ]
        ]
    ],
];
