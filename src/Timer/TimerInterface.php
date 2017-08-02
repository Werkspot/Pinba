<?php

namespace Werkspot\Pinba\Timer;

interface TimerInterface
{
    public static function start(array $tags, array $data): TimerInterface;

    public function addTag(string $name, string $value): void;

    public function stop(): void;
}
