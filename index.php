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

    $mediainfo = $instagram->getMedia($mediaid);
    $responsecode = $mediainfo->meta->code;

    if ($responsecode !== 200) {
        $app->abort($responsecode, $mediainfo->meta->error_message);
    }
    
    $media_location = $mediainfo->data->location;

    if($media_location === NULL) {
        $app->abort(404, 'No location information was found for this media ID.');
    }

    $instagram_data = array('id' => $mediainfo->data->id, 'location' => $media_location);

    $curl = new \Ivory\HttpAdapter\CurlHttpAdapter();
    $geocoder = new \Geocoder\Provider\Nominatim($curl, 'http://open.mapquestapi.com/nominatim/v1/');

    try {
        $reverselocation = $geocoder->reverse($media_location->latitude, $media_location->longitude);
        $address = $reverselocation->first();
        $location_data = array('street_name' => $address->getStreetName(),
                               'street_number' => $address->getStreetNumber(),
                               'sublocality' => $address->getSublocality(),
                               'locality' => $address->getLocality(),
                               'postal_code' => $address->getPostalCode(),
                               'country' => $address->getCountry()->getName());
    } catch (Exception $e) {
        $location_data = array();
    }
    
    $complete_data = array_merge($instagram_data, $location_data);
    return $app->json($complete_data, 200);
 });
 
 $app->run();
