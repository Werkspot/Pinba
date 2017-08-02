<?php

namespace Werkspot\Pinba\Timer;

interface TimerFactoryInterface
{
    public function start(array $tags, array $data = []): TimerInterface;
}
