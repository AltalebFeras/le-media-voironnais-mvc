<?php

namespace src\Repositories;

use src\Abstracts\AbstractRepository;
use src\Services\Database;
use PDO;

class AdminRepository extends AbstractRepository
{
    private $DBuser;

    public function __construct()
    {
        $this->DBuser = Database::getInstance()->getDB();
    }

    // Fetch a paginated list of users as associative arrays
    public function findAllUsers(int $currentPage = 1, int $usersPerPage = 10): array
    {
        $offset = max(0, ($currentPage - 1) * $usersPerPage);
        // use actual table `user` and correct column names
        $sql = "SELECT idUser AS id, firstName, lastName, email, avatarPath, isOnline, createdAt FROM `user` ORDER BY idUser DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->DBuser->prepare($sql);
        $stmt->bindValue(':limit', $usersPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Return total number of users
    public function countUsers(): int
    {
        $sql = "SELECT COUNT(*) FROM `user`";
        $stmt = $this->DBuser->query($sql);
        return (int)$stmt->fetchColumn();
    }
}
