<?php

namespace Werkspot\Pinba\PDO;

use Werkspot\Pinba\PinbaTimer;
use Werkspot\Pinba\PDO\PDO as PinbaPDO;

class PDOStatement
{
    private $PDOStatement;

    public function __construct(\PDOStatement $PDOStatement)
    {
        $this->PDOStatement = $PDOStatement;
    }

    public function execute(array $input_parameters = null)
    {
        $tags = [
            'group' => 'database',
            'op' => PinbaPDO::getQueryType($this->PDOStatement->queryString),
        ];
        $data = ['sql' => $this->PDOStatement->queryString];
        $timer = PinbaTimer::start($tags, $data);

        $result = $this->PDOStatement->execute($input_parameters);

        $timer->stop();

        return $result;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->PDOStatement, $method], $args);
    }

    public function __get($name)
    {
        return $this->PDOStatement->$name;
    }
}
