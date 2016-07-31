<?php

namespace Foodora;

/**
 * PDO database connection class
 *
 * Class PDOService
 * @package Foodora
 */
class PDOService
{

    private $connection;

    public function __construct()
    {
        $this->setConnection();
    }

    /**
     * Connect to a database. Uses config/db_pdo.php for database settings
     *
     * @throws \Exception
     */
    private function setConnection()
    {
        //load db array from config file
        $params = require __DIR__ . "/../config/db_pdo.php";

        $db_dsn = $params['pdo_dsn'] . ':dbname=' . $params['name'] . ';host=' . $params['host']
            . ';charset=' . $params['charset'];
        $db_pdo = null;

        try {
            $db_pdo = new \PDO($db_dsn, $params['user'], $params['password']);
            $db_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception('Database connection failed.' . $e->getMessage());
        }

        $this->connection = $db_pdo;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
