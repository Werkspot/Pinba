<?php

namespace Werkspot\Pinba\PDO;

/*
 * Wrapper on top of Doctrine\DBAL\Driver\PDOMySql\Driver to use PinbaPDOConnection
 */
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use PDOException;

class PDODriver extends Driver
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
    {
        try {
            $conn = new PDOConnection(
                $this->constructPdoDsn($params),
                $username,
                $password,
                $driverOptions
            );
        } catch (PDOException $e) {
            throw DBALException::driverException($this, $e);
        }

        return $conn;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pdo_mysql_pinba';
    }
}
