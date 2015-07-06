## Requirements

- [PHP](http://php.net/manual/en/install.php)
- [cURL](http://curl.haxx.se/docs/install.html)

## Setup

* Clone the repository:

        $ git clone https://github.com/MissFilly/media-location-information.git && cd media-location-information
    
* Download [Composer](https://getcomposer.org/download/) and use it to download the dependencies:

        $ curl -sS https://getcomposer.org/installer | php
        $ php composer.phar update

    In Linux, if this problem arises:

        Problem 1
            - Installation request for cosenary/instagram ~2.3 -> satisfiable by cosenary/instagram[v2.3].
            - cosenary/instagram v2.3 requires ext-curl * -> the requested PHP extension curl is missing from your system

    install the following packages:

        $ sudo apt-get install curl libcurl3 libcurl3-dev php5-curl

* Use your own [Instagram API credentials](https://instagram.com/developer/register/) 
in the `src/Api/Controller/instagram_credentials.php` file.

## Test it

Run the PHP built-in server:

    $ php -S localhost:8080 web/index.php
    
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

    $ bin/phpunit
    PHPUnit 4.7.6 by Sebastian Bergmann and contributors.

    ..

    Time: 2.82 seconds, Memory: 7.00Mb

    OK (2 tests, 5 assertions)

## Code explanation

I followed [this tutorial](http://sleep-er.co.uk/blog/2013/Creating-a-simple-REST-application-with-Silex/)
for the initial setup,
and [this one](http://whateverthing.com/blog/2013/09/01/quick-web-apps-part-five/) for writing the unit test.

For improving the application's project structure I read
[this post](http://php-and-symfony.matthiasnoback.nl/2012/01/silex-getting-your-project-structure-right/)
and [these slides](http://www.slideshare.net/ctankersley/complex-sites-with-silex), and I used
[this project](https://github.com/willgarcia/silex-api-boilerplate) as boilerplate.

The code and the instructions were tested under OSX 10.10.1 and Linux Mint 17.1.

The `app/routing.php` file maps a route pattern with a controller, i.e., it determines
the action (`Controller`) to be taken when a URL is accessed. `app/app.php` is the
bootstrap file that returns an instance of `Silex\Application` that uses the
previously mentioned routes. `web/index.php` runs that application.

I used a [third-party library](https://github.com/cosenary/Instagram-PHP-API)
to get Instagram data. This library is but a wrapper that simplifies the access
to the Instagram API. In this particular case, I used the `getMedia` method, which hits the
[/media/media-id](https://instagram.com/developer/endpoints/media/) endpoint.

Because the core functionality of this application is to retrieve
location information from Instagram, if the previously mentioned API call
doesn't return a successful response, this application should reflect the error.
That is also the case when the call to the Instagram API is successful, but
the media object doesn't contain any location information. In that case,
I return a 404 response code.

For the sake of completeness and readability, I used reverse geocoding through
[Nominatim](http://open.mapquestapi.com/nominatim/#reverse) to get more data
from the coordinates Instagram retrieves (country, postal code, locality, etc.).
Because this is an "extra" functionality, I decided to ignore any exception that
may raise while trying to retrieve information from Nominatim.