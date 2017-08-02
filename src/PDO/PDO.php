<?php

namespace Werkspot\Pinba\PDO;

use Werkspot\Pinba\PinbaTimer;

class PDO extends \PDO
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
        $tags = ['group' => 'database', 'op' => self::getQueryType($statement)];
        $data = ['sql' => $statement];
        $timer = PinbaTimer::start($tags, $data);
        $result = parent::exec($statement);
        $timer->stop();

        return $result;
    }

    public function prepare($statement, array $driver_options = array())
    {
        $statement = parent::prepare($statement, $driver_options);

        return new PDOStatement($statement);
    }

    public static function getQueryType($queryText)
    {
        $tmp = strtolower(substr(ltrim($queryText), 0, 8));
        $types = ['begin', 'commit', 'rollback', 'insert', 'update', 'delete', 'select'];
        foreach ($types as $type) {
            if (0 === strpos($tmp, $type)) {
                return $type;
            }
        }

        return 'unrecognized';
    }
}
