<?php

namespace Werkspot\Pinba\Timer;

final class Timer implements TimerInterface
{
    private $timer;

    public static function start(array $tags, array $data): TimerInterface
    {
        $self = new self;
        $self->timer = pinba_timer_start($tags, $data);

        return $self;
    }

    public function addTag(string $name, string $value): void
    {
        pinba_timer_tags_merge($this->timer, [$name, $value]);
    }

    public function stop(): void
    {
        pinba_timer_stop($this->timer);
    }
}
