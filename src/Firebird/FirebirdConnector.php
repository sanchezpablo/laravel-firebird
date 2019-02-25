<?php

namespace Firebird;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Illuminate\Database\Connectors\Connector;
use Illuminate\Database\Connectors\ConnectorInterface;


class FirebirdConnector extends Connector implements ConnectorInterface
{

    /**
     * Establish a database connection.
     *
     * @param  array $config
     * @return \PDO
     */
    public function connect(array $config)
    {
        $dsn = $this->getDsn($config);

        $options = $this->getOptions($config);

        $connection = $this->createConnection($dsn, $config, $options);

        return $connection;
    }

    private function getDsn(array $config)
    {
        $dsn = '';

        if (isset($config['host'])) {
            $dsn .= $config['host'];
        }

        if (isset($config['port'])) {
            $dsn .= "/" . $config['port'];
        }

        if (!isset($config['database'])) {
            throw new InvalidArgumentException('Требуется указать базуданных.');
        }
        if($dsn) {
            $dsn .= ':';
        }

        $dsn .= $config['database'] . ';';
        if (isset($config['charset'])) {
            $dsn .= "charset=" . $config['charset'];
        }

        if (isset($config['role'])) {
            $dsn .= "; role=" . $config['role'];
        }

        $dsn = 'firebird:dbname=' . $dsn;

        return $dsn;
    }
}