<?php

namespace Werkspot\Pinba\Doctrine;

/*
 * This is native Doctrine\DBAL\Driver\PDOConnection, the only difference is that
 * i's extended from PDOPinba instead of regular PDO
 */
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use PDOException;
use Werkspot\Pinba\PDO\PDO as PinbaPDO;

class PDOConnection extends PinbaPDO implements Connection, ServerInfoAwareConnection
{
    /**
     * @param string      $dsn
     * @param string|null $user
     * @param string|null $password
     * @param array|null  $options
     *
     * @throws PDOException in case of an error
     */
    public function __construct($dsn, $user = null, $password = null, array $options = null)
    {
        try {
            parent::__construct($dsn, $user, $password, $options);
            $this->setAttribute(PinbaPDO::ATTR_STATEMENT_CLASS, ['Doctrine\DBAL\Driver\PDOStatement', []]);
            $this->setAttribute(PinbaPDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $exception) {
            throw new PDOException($exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exec($statement)
    {
        try {
            return parent::exec($statement);
        } catch (\PDOException $exception) {
            throw new PDOException($exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getServerVersion()
    {
        return PinbaPDO::getAttribute(PinbaPDO::ATTR_SERVER_VERSION);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($prepareString, array $driverOptions = [])
    {
        try {
            return parent::prepare($prepareString, $driverOptions);
        } catch (\PDOException $exception) {
            throw new PDOException($exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $args = func_get_args();
        $argsCount = count($args);

        try {
            if ($argsCount == 4) {
                return parent::query($args[0], $args[1], $args[2], $args[3]);
            }

            if ($argsCount == 3) {
                return parent::query($args[0], $args[1], $args[2]);
            }

            if ($argsCount == 2) {
                return parent::query($args[0], $args[1]);
            }

            return parent::query($args[0]);
        } catch (\PDOException $exception) {
            throw new PDOException($exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function quote($input, $type = PinbaPDO::PARAM_STR)
    {
        return parent::quote($input, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId($name = null)
    {
        return parent::lastInsertId($name);
    }

    /**
     * {@inheritdoc}
     */
    public function requiresQueryForServerVersion()
    {
        return false;
    }
}
