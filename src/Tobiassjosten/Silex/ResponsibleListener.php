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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class ResponsibleListener implements EventSubscriberInterface
{
    private $encoder;

    public function __construct(EncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $result = $event->getControllerResult();

        if (!is_array($result)) {
            return;
        }

        $supported = ['json', 'xml'];
        foreach ($request->getAcceptableContentTypes() as $type) {
            if (in_array($format = $request->getFormat($type), $supported)) {
                $event->setResponse(new Response(
                    $this->encoder->encode($result, $format),
                    200,
                    ['Content-Type' => $type]
                ));

                break;
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::VIEW => ['onKernelView', -10]];
    }
}
