<?php

require __DIR__ . '/../vendor/autoload.php';

use Silex\WebTestCase;

class WebTest extends WebTestCase {
    public function createApplication() {
        $app_env = 'test';
        return require __DIR__ . '/../index.php';
    }

    public function testMediaDataResponse() {
        $client = $this->createClient();

        // Test media with location information
        $crawler = $client->request('GET','/media/764');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            'Successfully got information for media with ID 764.'
        );

        // Test media without location information
        $crawler = $client->request('GET','/media/500');
        $this->assertEquals(
            404,
            $client->getResponse()->getStatusCode(),
            'Correct 404 response from media without location information.'
        );

        // Test invalid media ID
        $crawler = $client->request('GET','/media/invalid_id');
        $this->assertEquals(
            400,
            $client->getResponse()->getStatusCode(),
            'Correct 400 response from invalid media ID.'
        );
    }
}
