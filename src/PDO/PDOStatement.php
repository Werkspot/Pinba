<?php
namespace Werkspot\Pinba\PDO;

use Werkspot\Pinba\PinbaTimer;

class PDOStatement
{
    private $PDOStatement;

    public function __construct(\PDOStatement $PDOStatement)
    {
        $this->PDOStatement = $PDOStatement;
    }

    public function execute(array $input_parameters = null)
    {
        $tags = array(
            'group' => 'mysql',
            'op' => PDO::getQueryType($this->PDOStatement->queryString),
        );
        $data = array('sql' => $this->PDOStatement->queryString);
        $timer = PinbaTimer::start($tags, $data);

        $result = $this->PDOStatement->execute($input_parameters);

        $timer->stop();
        return $result;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->PDOStatement, $method), $args);
    }

    public function __get($name)
    {
        return $this->PDOStatement->$name;
    }
}
