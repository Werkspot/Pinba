<?php

namespace Werkspot\Pinba\Guzzle;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Werkspot\Pinba\PinbaTimer;

class TimedGuzzleClient implements ClientInterface
{
    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    public function __construct(ClientInterface $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * {@inheritdoc}
     */
    public function createRequest($method, $url = null, array $options = [])
    {
        return $this->guzzleClient->request($method, $url, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function get($url = null, $options = [])
    {
        return $this->measure('get', $url, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function head($url = null, array $options = [])
    {
        return $this->measure('head', $url, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url = null, array $options = [])
    {
        return $this->measure('delete', $url, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function put($url = null, array $options = [])
    {
        return $this->measure('put', $url, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function patch($url = null, array $options = [])
    {
        return $this->measure('patch', $url, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function post($url = null, array $options = [])
    {
        return $this->measure('post', $url, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function options($url = null, array $options = [])
    {
        return $this->measure('options', $url, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function send(RequestInterface $request, array $options = [])
    {
        $timer = $this->start('send', '');

        $result = $this->guzzleClient->send($request, $options);

        $this->stop($timer);

        return $result;
    }

    public function sendAsync(RequestInterface $request, array $options = [])
    {
        $timer = $this->start('sendAsync', '');

        $result = $this->guzzleClient->sendAsync($request, $options);

        $this->stop($timer);

        return $result;
    }

    public function request($method, $uri, array $options = [])
    {
        return $this->guzzleClient->request($method, $uri, $options);
    }

    public function requestAsync($method, $uri, array $options = [])
    {
        return $this->guzzleClient->requestAsync($method, $uri, $options);
    }

    public function getConfig($option = null)
    {
        return $this->guzzleClient->getConfig($option);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption($keyOrPath = null)
    {
        return $this->guzzleClient->getConfig($keyOrPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl()
    {
        return $this->guzzleClient->getConfig('base_uri');
    }

    /**
     * @param string $method
     * @param null $url
     * @param array $options
     *
     * @return mixed
     */
    private function measure($method, $url = null, $options = [])
    {
        $timer = $this->start($method, $url);

        $result = $this->guzzleClient->$method($url, $options);

        $this->stop($timer);

        return $result;
    }

    /**
     * @param string $operation
     * @param string $url
     *
     * @return PinbaTimer
     */
    private function start($operation, $url)
    {
        return PinbaTimer::start([
            'group' => 'guzzle',
            'op' => $operation,
            'meta' => $url,
        ]);
    }

    /**
     * @param PinbaTimer $timer
     */
    private function stop(PinbaTimer $timer)
    {
        $timer->stop();
    }
}
