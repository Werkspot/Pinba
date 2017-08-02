<?php

namespace Werkspot\Pinba;

use Werkspot\Pinba\Timer\TimerFactory;
use Werkspot\Pinba\Timer\TimerInterface;

final class PinbaTimer
{
    /**
     * @var TimerInterface
     */
    private $timer;

    /**
     * @var boolean
     */
    private static $isPinbaInstalled;

    /**
     * @param array $tags
     */
    private function __construct(array $tags, array $data = [])
    {
        if (self::isPinbaInstalled()) {
            $this->timer = (new TimerFactory)->start($tags, $data);
        }
    }

    /**
     * Stop the timer
     */
    public function stop()
    {
        if ($this->timer) {
            $this->timer->stop();
        }
    }

    /**
     * Add a tag to the existing timer
     *
     * @param string $name
     * @param string $value
     */
    public function addTag($name, $value)
    {
        if ($this->timer) {
            $this->timer->addTag($name, $value);
        }
    }

    /**
     * Start a new pinba timer
     *
     * @param array $tags like ['group' => 'symfony', 'op' => 'initialize']
     *
     * @return PinbaTimer
     */
    public static function start(array $tags, array $data = [])
    {
        return new self($tags, $data);
    }

    public static function isPinbaInstalled(): bool
    {
        if (null === self::$isPinbaInstalled) {
            self::$isPinbaInstalled =
                function_exists('pinba_timer_start') &&
                function_exists('pinba_timer_stop') &&
                function_exists('pinba_timer_add') &&
                function_exists('pinba_get_info');
        }

        return self::$isPinbaInstalled;
    }
}
