<?php

class Database
{
    private static $instance;
    private $connection;

    private function __construct()
    {
        // Use your actual database credentials
        $host = 'terrain.cdx1x2gzjgqp.eu-west-3.rds.amazonaws.com';
        $dbname = 'vigie_test';
        $user = 'upwork';
        $password = '5SwBzuzyq99iA2e';

        $this->connection = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}

// Example of usage
$database = Database::getInstance();
$connection = $database->getConnection();

// Now, you can use $connection to perform queries
