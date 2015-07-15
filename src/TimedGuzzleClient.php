<?php
namespace Werkspot\Pinba;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Event\EmitterInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Url;

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
     * {@inheritDoc}
     */
    public function createRequest($method, $url = null, array $options = [])
    {
        return $this->guzzleClient->createRequest($method, $url, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function get($url = null, $options = [])
    {
        return $this->measure('get', $url, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function head($url = null, array $options = [])
    {
        return $this->measure('head', $url, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($url = null, array $options = [])
    {
        return $this->measure('delete', $url, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function put($url = null, array $options = [])
    {
        return $this->measure('put', $url, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function patch($url = null, array $options = [])
    {
        return $this->measure('patch', $url, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function post($url = null, array $options = [])
    {
        return $this->measure('post', $url, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function options($url = null, array $options = [])
    {
        return $this->measure('options', $url, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function send(RequestInterface $request)
    {
        $timer = $this->start('send', '');

        $result = $this->guzzleClient->send($request);

        $this->stop($timer);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultOption($keyOrPath = null)
    {
        return $this->guzzleClient->getDefaultOption($keyOrPath);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOption($keyOrPath, $value)
    {
        return $this->guzzleClient->setDefaultOption($keyOrPath, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getBaseUrl()
    {
        return $this->guzzleClient->getBaseUrl();
    }

    /**
     * {@inheritDoc}
     */
    public function getEmitter()
    {
        return $this->guzzleClient->getEmitter();
    }

    /**
     * @param string $method
     * @param null $url
     * @param array $options
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
     * @return PinbaTimer
     */
    private function start($operation, $url)
    {
        return PinbaTimer::start([
            'group' => 'guzzle',
            'operation' => $operation,
            'url' => $url,
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