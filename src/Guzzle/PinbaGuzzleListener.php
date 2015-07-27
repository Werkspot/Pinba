<?php
namespace Werkspot\Pinba\Guzzle;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\EndEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use Werkspot\Pinba\PinbaTimer;

/**
 * Class to hook into guzzle to measure performance, this is more 'low level' as the TimedGuzzleClient, but
 * also precise and it's unknown when the listener is exactly excecuted.
 *
 * So it's probably better to use the TimedGuzzleClient if you want to measure the time it takes for the application
 * to do some request.
 *
 * If you really want to measure how long the raw curl request takes we should write some custom HTTP transport plugin
 * that wraps around curl and measures the raw requests
 *
 * @package Werkspot\Library
 */
class PinbaGuzzleListener implements SubscriberInterface
{
    /**
     * @var PinbaTimer
     */
    private $timer;

    public function getEvents()
    {
        return [
            'before' => ['onBefore', RequestEvents::EARLY],
            // 'complete' => ['onComplete', RequestEvents::LATE],
            // 'error'    => ['onError', RequestEvents::LATE],
            'end' => ['onEnd', RequestEvents::LATE],
        ];
    }

    public function onBefore(BeforeEvent $event)
    {
        $this->timer = PinbaTimer::start([
            'group' => 'guzzle',
            'operation' => $event->getRequest()->getMethod(),
            'uri' => $event->getRequest()->getUrl()
        ]);
    }

    public function onComplete(CompleteEvent $event)
    {
        $this->timer->addTag('response', $event->getResponse()->getStatusCode());
        $this->timer->stop();
    }

    public function onError(ErrorEvent $event)
    {
        $this->timer->addTag('response', $event->getResponse()->getStatusCode());
        $this->timer->stop();
    }

    public function onEnd(EndEvent $event)
    {
        $this->timer->addTag('response', $event->getResponse()->getStatusCode());
        $this->timer->stop();
    }
}