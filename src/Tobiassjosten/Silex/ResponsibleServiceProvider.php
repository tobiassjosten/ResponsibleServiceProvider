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

use Silex\Application;
use Silex\Provider\SerializerServiceProvider;
use Silex\ServiceProviderInterface;

class ResponsibleServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        if (empty($app['serializer'])) {
            $app->register(new SerializerServiceProvider());
        }
    }

    public function boot(Application $app)
    {
        $app['dispatcher']->addSubscriber(
            new ResponsibleListener($app['serializer'])
        );
    }
}
