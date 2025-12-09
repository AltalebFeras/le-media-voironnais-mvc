<?php

namespace src\Services;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private $DB;
    private static $instance = null;

    // make constructor private for singleton
    private function __construct()
    {
        require_once __DIR__ . '/../../config.php';
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
            $this->DB = new PDO($dsn, DB_USER, DB_PWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            // Register shutdown handler to ensure a single, explicit close at script end
            register_shutdown_function([self::class, 'shutdownClose']);
        } catch (PDOException $error) {
            echo "Error while connecting to Database: " . $error->getMessage();
        }
    }

    // get singleton instance
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getDB(): PDO
    {
        if ($this->DB instanceof PDO) {
            return $this->DB;
        } else {
            throw new RuntimeException("Database connection failed.");
        }
    }
    public function closeConnection(): void
    {
        if ($this->DB instanceof PDO) {
            $this->DB = null;
            self::$instance = null;
        } else {
            // connection already closed - do nothing (prevents duplicate logs)
        }
    }
    public static function shutdownClose(): void
    {
        if (self::$instance !== null) {
            self::$instance->closeConnection();
        }
    }
}
