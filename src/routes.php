<?php

// Routes
$app->any('/[{path:.*}]', \Service\RouteGenerator::class . ':buildRoute');