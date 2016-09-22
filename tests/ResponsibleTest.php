<?php

/*
 * This file is part of ResponsibleServiceProvider.
 *
 * (c) Tobias Sjösten <tobias@tobiassjosten.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Silex\Application;
use Silex\WebTestCase;
use Tobiassjosten\Silex\ResponsibleServiceProvider;

class ResponsibleTest extends WebTestCase
{
    public function createApplication()
    {
        $app = new Application();
        $app->register(new ResponsibleServiceProvider());
        $app['debug'] = true;
        unset($app['exception_handler']);

        $app->get('/foo', function () {
            return array('bar');
        });

        return $app;
    }

    public function testArrayResponseWithJSON()
    {
        $client = $this->createClient();
        $client->request('GET', '/foo', array(), array(), array(
            'HTTP_ACCEPT' => 'application/json',
        ));

        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertEquals('["bar"]', $response->getContent());
    }

    public function testArrayResponseWithXML()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/foo', array(), array(), array(
            'HTTP_ACCEPT' => 'text/xml',
        ));

        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertEquals(
            "<?xml version=\"1.0\"?>\n<response><item key=\"0\">bar</item></response>\n",
            $response->getContent()
        );
    }

    public function testArrayResponseWithEmpty()
    {
        $client = $this->createClient();
        $client->request('GET', '/foo', array(), array(), array(
            'HTTP_ACCEPT' => '',
        ));

        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertEquals('["bar"]', $response->getContent());
    }

    public function testArrayResponseWithInvalidHTTP10()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/foo', array(), array(), array(
            'SERVER_PROTOCOL' => 'HTTP/1.0',
            'HTTP_ACCEPT' => 'text/html',
        ));

        $response = $client->getResponse();

        $this->assertFalse($response->isOk());
        $this->assertEquals(406, $response->getStatusCode());
    }

    public function testArrayResponseWithInvalidHTTP11()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/foo', array(), array(), array(
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/html',
        ));

        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
