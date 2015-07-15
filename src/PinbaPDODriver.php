<?php
namespace Werkspot\Pinba;

/*
 * Wrapper on top of Doctrine\DBAL\Driver\PDOMySql\Driver to use PinbaPDOConnection
 */
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use PDOException;

class PinbaPDODriver extends Driver
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        try {
            $conn = new PinbaPDOConnection(
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