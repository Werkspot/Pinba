<?php

namespace Werkspot\Pinba\Doctrine;

use Doctrine\DBAL\Logging\SQLLogger as SQLLoggerInterface;
use Werkspot\Pinba\Timer\TimerFactoryInterface;
use Werkspot\Pinba\Timer\TimerInterface;

final class SQLLogger implements SQLLoggerInterface
{
    /**
     * @var TimerFactoryInterface
     */
    private $timerFactory;

    /**
     * @var TimerInterface
     */
    private $currentTimer;

    public function __construct(TimerFactoryInterface $timerFactory)
    {
        $this->timerFactory = $timerFactory;
    }

    public function startQuery($sql, array $params = null, array $types = null)
    {
        $tags = ['group' => 'database', 'op' => $this->getQueryType($sql)];
        $data = ['sql' => $sql];

        $this->currentTimer = $this->timerFactory->start($tags, $data);
    }

    public function stopQuery()
    {
        $this->currentTimer->stop();

        unset($this->currentTimer);
    }

    private function getQueryType(string $sql): string
    {
        $doctrineTypeMap = [
            '"START TRANSACTION"' => 'begin',
            '"COMMIT"' => 'commit',
            '"ROLLBACK"' => 'rollback',
            '"SAVEPOINT"' => 'savepoint',
            '"RELEASE SAVEPOINT"' => 'release_savepoint',
            '"ROLLBACK TO SAVEPOINT"' => 'rollback_to_savepoint',
        ];
        foreach ($doctrineTypeMap as $doctrineType => $type) {
            if (strcasecmp($sql, $doctrineType) === 0) {
                return $type;
            }
        }

        $sqlTypes = ['insert', 'update', 'delete', 'select'];
        foreach ($sqlTypes as $type) {
            if (strncasecmp($sql, $type, strlen($type)) === 0) {
                return $type;
            }
        }

        return 'unrecognized';
    }
}
