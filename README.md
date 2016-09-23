# ResponsibleServiceProvider

A [Silex](http://silex.sensiolabs.org) ServiceProvider for automagic response formatting.

[![Build Status](https://travis-ci.org/tobiassjosten/ResponsibleServiceProvider.png?branch=master)](https://travis-ci.org/tobiassjosten/ResponsibleServiceProvider) 

## Installation

1) Add the package to you composer.json:

    $ composer require tobiassjosten/responsible-service-provider

(For Silex 1, add the *0.0.1* version to the end of that commend.)

2) Register it in your application.

    $app->register(new \Tobiassjosten\Silex\ResponsibleServiceProvider());

## Usage

Once enabled, just have your controllers return data as an array. `ResponsibleServiceProvider` will do the rest.

    $app->get('/foo', function () {
        return ['Bar'];
    });

In JSON:

    $ curl -I -H 'Accept: application/json' http://example.com/foo
    HTTP/1.1 200 OK
    Date: Tue, 07 May 2013 08:30:58 GMT
    Server: Apache/2.2.22 (Ubuntu)
    X-Powered-By: PHP/5.4.9-4ubuntu2
    Cache-Control: no-cache
    Transfer-Encoding: chunked
    Content-Type: application/json
    
    ["Bar"]

And in XML:

    $ curl -I -H 'Accept: application/xml' http://example.com/foo
    HTTP/1.1 200 OK
    Date: Tue, 07 May 2013 08:30:58 GMT
    Server: Apache/2.2.22 (Ubuntu)
    X-Powered-By: PHP/5.4.9-4ubuntu2
    Cache-Control: no-cache
    Transfer-Encoding: chunked
    Content-Type: application/xml
    
    <?xml version="1.0"?>
    <response><item key="0">Bar</item></response>
