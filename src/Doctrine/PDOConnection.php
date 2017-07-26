<?php

namespace Werkspot\Pinba\Doctrine;

use Doctrine\DBAL\Driver\PDOConnection as DBALConnection;
use Werkspot\Pinba\PDO\PDO as PinbaPDO;
use Werkspot\Pinba\PinbaTimer;

class PDOConnection extends DBALConnection
{
    public function __construct(...$arguments)
    {
        $tags = ['group' => 'database', 'op' => 'connect'];
        $timer = PinbaTimer::start($tags);
        $result = call_user_func_array(['parent', '__construct'], $arguments);
        $timer->stop();

        return $result;
    }

    public function beginTransaction()
    {
        $tags = ['group' => 'database', 'op' => 'begin'];
        $timer = PinbaTimer::start($tags);
        $result = parent::beginTransaction();
        $timer->stop();

        return $result;
    }

    public function commit()
    {
        $tags = ['group' => 'database', 'op' => 'commit'];
        $timer = PinbaTimer::start($tags);
        $result = parent::commit();
        $timer->stop();

        return $result;
    }

    public function rollBack()
    {
        $tags = ['group' => 'database', 'op' => 'rollback'];
        $timer = PinbaTimer::start($tags);
        $result = parent::rollBack();
        $timer->stop();

        return $result;
    }

    public function exec($statement)
    {
        $tags = ['group' => 'database', 'op' => PinbaPDO::getQueryType($statement)];
        $data = ['sql' => $statement];
        $timer = PinbaTimer::start($tags, $data);
        $result = parent::exec($statement);
        $timer->stop();

        return $result;
    }
}
