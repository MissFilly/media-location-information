<?php

require __DIR__ . '/../vendor/autoload.php';

use Silex\WebTestCase;

class WebTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../app/app.php';
    }

    public function testIndex()
    {
        $client = $this->createClient();
        $client->request('GET', '/');
        $response = $client->getResponse();
        $this->assertEquals(
            200,
            $response->getStatusCode(),
            'Successfully got index response.'
        );
        $this->assertContains('Use the `/media/{media_id}` endpoint.', $response->getContent());
    }

    public function testMediaDataResponse()
    {
        $client = $this->createClient();

        // Test media with location information
        $client->request('GET', '/media/764');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            'Successfully got information for media with ID 764.'
        );

        // Test media without location information
        $client->request('GET', '/media/500');
        $this->assertEquals(
            404,
            $client->getResponse()->getStatusCode(),
            'Correct 404 response from media without location information.'
        );

        // Test invalid media ID
        $client->request('GET', '/media/invalid_id');
        $this->assertEquals(
            400,
            $client->getResponse()->getStatusCode(),
            'Correct 400 response from invalid media ID.'
        );
    }
}
