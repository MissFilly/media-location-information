<?php

namespace Api\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use MetzWeb\Instagram\Instagram;

class ApiController
{
    private function getInstagramData($media_id)
    {
        /**
         * Uses Instagram-PHP-API to retrieve data
         * from Instagram for a particular media ID.
         */

        $instagram = new Instagram(array(
            'apiKey' => 'de9fb3e8f248428e9e56733ed74c7010',
            'apiSecret' => 'bc3a9f6313f3491aa109de875602293b',
            'apiCallback' => 'http://localhost:8080'
        ));
        return $instagram->getMedia($media_id);
    }

    private function  getNominatimData($media_location)
    {
        /**
         * Tries to retrieve more information
         * about the given geographic coordinates,
         * using Nominatim's reverse geocoding.
         */
        $curl = new \Ivory\HttpAdapter\CurlHttpAdapter();
        $geocoder = new \Geocoder\Provider\Nominatim($curl, 'http://open.mapquestapi.com/nominatim/v1/');
        try {
            $reverse_location = $geocoder->reverse($media_location->latitude, $media_location->longitude);
            $address = $reverse_location->first();
            $location_data = array('street_name' => $address->getStreetName(),
                'street_number' => $address->getStreetNumber(),
                'sublocality' => $address->getSublocality(),
                'locality' => $address->getLocality(),
                'postal_code' => $address->getPostalCode(),
                'country' => $address->getCountry()->getName());
        } catch (Exception $e) {
            $location_data = array();
        }

        return $location_data;
    }

    public function apiAction(Request $request, Application $app, $media_id)
    {

        $media_info = $this->getInstagramData($media_id);
        $response_code = $media_info->meta->code;

        /**
         * If Instagram's response is not successful, raise the error.
         */
        if ($response_code !== 200) {
            $error = array('message' => $media_info->meta->error_message);
            return $app->json($error, $response_code);
        }

        $media_location = $media_info->data->location;

        /**
         * If the media object doesn't contain location information, return 404.
         */
        if ($media_location === null) {
            $error = array('message' => 'No location information was found for this media ID.');
            return $app->json($error, 404);
        }
        $instagram_data = array('id' => $media_info->data->id, 'location' => $media_location);
        $location_data = $this->getNominatimData($media_location);

        /**
         * Merge Instagram's and Nominatim's information.
         */
        $complete_data = array_merge($instagram_data, $location_data);
        return $app->json($complete_data, 200);
    }
}
