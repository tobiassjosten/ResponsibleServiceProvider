<?php

/*
 * This file is part of ResponsibleServiceProvider.
 *
 * (c) Tobias Sjösten <tobias@tobiassjosten.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobiassjosten\Silex;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Silex\Provider\SerializerServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ResponsibleServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    public function register(Container $app)
    {
        if (empty($app['serializer'])) {
            $app->register(new SerializerServiceProvider());
        }
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(
            new ResponsibleListener($app['serializer'])
        );
    }
}
