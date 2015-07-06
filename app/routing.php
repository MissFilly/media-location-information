<?php

$app->get('/', 'Api\\Controller\\ApiController::indexAction');

$app->get('/media/{media_id}', 'Api\\Controller\\ApiController::apiAction');
