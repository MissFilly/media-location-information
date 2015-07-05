<?php

use Symfony\Component\HttpFoundation\Request;

$app = require_once __DIR__ . '/../app/app.php';

$request = Request::createFromGlobals();
$response = $app->handle($request);
$response->send();
$app->terminate($request, $response);
