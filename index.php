<?php
 require_once __DIR__.'/../vendor/autoload.php';
 
 use MetzWeb\Instagram\Instagram;

 $app = new Silex\Application();
 // Please set to false in a production environment
 $app['debug'] = true;
 
 $app->get('/media/{mediaid}', function (Silex\Application $app, $mediaid)  {

    $instagram = new Instagram(array(
	    'apiKey'      => 'de9fb3e8f248428e9e56733ed74c7010',
	    'apiSecret'   => 'bc3a9f6313f3491aa109de875602293b',
	    'apiCallback' => 'http://localhost:8080'
	));

    $code = $_GET['code'];
    $data = $instagram->getOAuthToken($code);

    $instagram->setAccessToken($data);

    $mediainfo = $instagram->getMedia($mediaid);
    $responsecode = $mediainfo->meta->code;

    if ($responsecode !== 200) {
        $app->abort($responsecode, $mediainfo->meta->error_message);
    }
    
    $medialocation = $mediainfo->data->location;

    if($medialocation === NULL) {
        $app->abort(404, 'No location information was found for this media ID.');
    }
    
    return $app->json($mediainfo);
 });
 
 $app->run();
