<?php

namespace src\Repositories;

use Exception;
use PDO;
use PDOException;
use src\Abstracts\AbstractRepository;
use src\Models\User;
use src\Services\Database;

class AdminRepository extends AbstractRepository
{
    private $DBuser;

    public function __construct()
    {
        $database = new Database();
        $this->DBuser = $database->getDB();

        require_once __DIR__ . '/../../config.php';
    }
}
