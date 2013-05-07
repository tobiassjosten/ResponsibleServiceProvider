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

        $supported = array('json', 'xml');
        $default = reset($supported);
        $accepted = $request->getAcceptableContentTypes() ?: array($request->getMimeType($default));

        foreach ($accepted as $type) {
            if (in_array($format = $request->getFormat($type), $supported)) {
                $event->setResponse(new Response(
                    $this->encoder->encode($result, $format),
                    200,
                    array('Content-Type' => $type)
                ));

                return;
            }
        }

        // HTTP/1.1 recommends returning some data over giving a 406 error,
        // even if that data is not supported by the Accept header.
        if ('HTTP/1.1' === $request->get('SERVER_PROTOCOL')) {
            $event->setResponse(new Response(
                $this->encoder->encode($result, $default),
                200,
                array('Content-Type' => $request->getMimeType($default))
            ));

            return;
        }

        $event->setResponse(new Response(
            'Unsupported media type',
            406,
            array('Content-Type' => 'text/plain')
        ));
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::VIEW => array('onKernelView', -10));
    }
}
