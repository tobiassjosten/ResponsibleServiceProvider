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
        $app['exception_handler']->disable();

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
}
