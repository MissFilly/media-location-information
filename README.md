## Requirements

- [PHP](http://php.net/manual/en/install.php)

In Linux you may also need to install the following packages:

    $ sudo apt-get install curl libcurl3 libcurl3-dev php5-curl


## Setup

Clone the repository:

    $ git clone https://github.com/MissFilly/media-location-information.git && cd media-location-information
    
Download [Composer](https://getcomposer.org/download/) and use it to download the dependencies:

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar update


## Test it

Run the PHP built-in server:

    $ php -S localhost:8080 index.php
    
Now if you browse `http://localhost:8080/media/764`, you should get a response like:

    {
        "id": "764_25",
        "location": 
    
        {
            "latitude": 41.88942497,
            "name": "Google",
            "longitude": -87.629016724,
            "id": 333
        },
        "street_name": "North Dearborn Street",
        "street_number": "401",
        "sublocality": "Near North Side",
        "locality": "Chicago",
        "postal_code": "60654",
        "country": "United States of America"
    }

## Run tests

    $ vendor/bin/phpunit
    PHPUnit 4.7.5 by Sebastian Bergmann and contributors.
    
    .
    
    Time: 8.77 seconds, Memory: 7.25Mb
    
    OK (1 test, 3 assertions)

## Code explanation

I used a [third-party library](https://github.com/cosenary/Instagram-PHP-API)
to get Instagram data. This library is but a wrapper that simplifies the access
to the Instagram API. In this particular case, I used the `getMedia` method, which hits the
[/media/media-id](https://instagram.com/developer/endpoints/media/) endpoint.

Because the core functionality of this application is to retrieve
location information from Instagram, if the previously mentioned API call
doesn't return a successful response, this application should reflect the error.
That is also the case when the call to the Instagram API is successful, but
the media object doesn't contain any location information. In that case,
I raised a 404 response.

For the sake of completeness and readability, I used reverse geocoding through
[Nominatim](http://open.mapquestapi.com/nominatim/#reverse) to get more data
from the coordinates Instagram retrieves (country, postal code, locality, etc.).
Because this is an "extra" functionality, I decided to ignore any exception that
may raise while trying to retrieve information from Nominatim.