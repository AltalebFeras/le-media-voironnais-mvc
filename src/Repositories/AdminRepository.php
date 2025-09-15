<?php

namespace src\Repositories;

use src\Abstracts\AbstractRepository;
use src\Services\Database;
use PDO;

class AdminRepository
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
        $sql = "SELECT idUser AS id, firstName, lastName, email, avatarPath, isOnline, createdAt FROM `user` WHERE idRole=3 ORDER BY idUser DESC LIMIT :limit OFFSET :offset";
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
   
    public function findById(int $id): ?array
    {
        // select columns and join role table to include role name (role.name as roleName)
        $sql = "SELECT
					u.idUser AS id,
					u.idRole,
					r.name AS roleName,
					u.firstName,
					u.lastName,
					u.email,
					u.phone,
					u.avatarPath,
					u.bannerPath,
					u.bio,
					u.dateOfBirth,
					u.isActivated,
					u.isBanned,
					u.isDeleted,
					u.isOnline,
					u.lastSeen,
					u.rgpdAcceptedDate,
					u.createdAt,
					u.updatedAt,
					u.emailChangedAt,
					u.passwordResetAt,
					u.deletedAt
				FROM `user` u
				LEFT JOIN `role` r ON u.idRole = r.idRole
				WHERE u.idUser = :id AND u.idRole = 3";
        $stmt = $this->DBuser->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
}
