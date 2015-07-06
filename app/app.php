<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;

// routing
require __DIR__.'/routing.php';

return $app;
