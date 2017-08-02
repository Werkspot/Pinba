<?php

namespace Werkspot\Pinba\Test\Doctrine;

use Mockery;
use PHPUnit\Framework\TestCase;
use Werkspot\Pinba\Doctrine\SQLLogger;
use Werkspot\Pinba\Timer\TimerFactory;
use Werkspot\Pinba\Timer\TimerFactoryInterface;
use Werkspot\Pinba\Timer\TimerInterface;

final class SQLLoggerTest extends TestCase
{
    /**
     * @dataProvider provideQueries
     */
    public function testLogQueryTimes(string $sql, string $operation): void
    {
        $tags = [
            'group' => 'database',
            'op' => $operation,
        ];
        $data = [
            'sql' => $sql,
        ];

        $timer = Mockery::mock(TimerInterface::class);
        $timer->shouldReceive('stop');

        $timerFactory = Mockery::mock(TimerFactoryInterface::class);
        $timerFactory
            ->shouldReceive('start')
            ->with($tags, $data)
            ->andReturn($timer);

        $logger = new SQLLogger($timerFactory);
        $logger->startQuery($sql);
        $logger->stopQuery();
    }

    public function provideQueries(): array
    {
        return [
            [
                'sql' => '"START TRANSACTION"',
                'op' => 'begin',
            ],
            [
                'sql' => '"COMMIT"',
                'op' => 'commit',
            ],
            [
                'sql' => 'INSERT INTO Foo (bar) VALUES (:bar)',
                'op' => 'insert',
            ],
            [
                'sql' => 'SELECT bar FROM Foo',
                'op' => 'select',
            ],
        ];
    }
}
