<?php

namespace Werkspot\Pinba\Timer;

final class TimerFactory implements TimerFactoryInterface
{
    public function start(array $tags, array $data = []): TimerInterface
    {
        return Timer::start($tags, $data);
    }
}
