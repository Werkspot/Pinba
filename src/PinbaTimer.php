<?php
namespace Werkspot\Pinba;

class PinbaTimer
{
    private $timer;
    private static $isPinbaInstalled;

    /**
     * @param array $tags
     */
    private function __construct(array $tags)
    {
        if (self::isPinbaInstalled()) {
            $this->timer = pinba_timer_start($tags);
        }
    }

    /**
     * Stop the timer
     */
    public function stop()
    {
        if ($this->timer) {
            pinba_timer_stop($this->timer);
        }
    }

    /**
     * Add a tag to the existing timer
     * @param string $name
     * @param string $value
     */
    public function addTag($name, $value)
    {
        if ($this->timer) {
            pinba_timer_tags_merge($this->timer, [$name, $value]);
        }
    }

    /**
     * Start a new pinba timer
     *
     * @param array $tags like ['group' => 'symfony', 'operation' => 'initialize']
     * @return PinbaTimer
     */
    public static function start(array $tags)
    {
        return new self($tags);
    }

    /**
     * @return bool
     */
    private static function isPinbaInstalled()
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