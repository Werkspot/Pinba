<?php
namespace Werkspot\Pinba\PDO;

use Werkspot\Pinba\PinbaTimer;

class PDO extends \PDO
{
    public function __construct()
    {
        $tags = array('group' => 'mysql', 'op' => 'connect');
        $timer = PinbaTimer::start($tags);

        $args = func_get_args();
        $result = call_user_func_array(array('parent', '__construct'), $args);

        $timer->stop();
        return $result;
    }

    public function beginTransaction()
    {
        $tags = array('group' => 'mysql', 'op' => 'begin');
        $timer = PinbaTimer::start($tags);
        $result = parent::beginTransaction();
        $timer->stop();
        return $result;
    }

    public function commit()
    {
        $tags = array('group' => 'mysql', 'op' => 'commit');
        $timer = PinbaTimer::start($tags);
        $result = parent::commit();
        $timer->stop();
        return $result;
    }

    public function rollBack()
    {
        $tags = array('group' => 'mysql', 'op' => 'rollback');
        $timer = PinbaTimer::start($tags);
        $result = parent::rollBack();
        $timer->stop();
        return $result;
    }

    public function exec($statement)
    {
        $tags = array('group' => 'mysql', 'op' => self::getQueryType($statement));
        $data = array('sql' => $statement);
        $timer = PinbaTimer::start($tags, $data);
        $result = parent::exec($statement);
        $timer->stop();
        return $result;
    }

    public function query()
    {
        $args = func_get_args();
        $result = call_user_func_array(array('parent', 'query'), $args);
        return new PDOStatement($result);
    }

    public function prepare($statement, $driver_options = array())
    {
        $result = parent::prepare($statement, $driver_options);
        return new PDOStatement($result);
    }

    public static function getQueryType($queryText)
    {
        $tmp = strtolower(substr(ltrim($queryText), 0, 8));
        $types = array('begin', 'commit', 'rollback', 'insert', 'update', 'delete', 'select');
        foreach ($types as $type) {
            if (0 === strpos($tmp, $type)) {
                return $type;
            }
        }
        return 'unrecognized';
    }
}
