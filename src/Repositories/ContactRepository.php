<?php

namespace src\Repositories;

use Exception;
use PDO;
use PDOException;
use src\Services\Database;

class ContactRepository
{
    private $DB;

    public function __construct()
    {
        $this->DB = Database::getInstance()->getDB();
    }
}
