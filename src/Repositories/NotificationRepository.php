<?php

namespace src\Repositories;

use Exception;
use PDO;
use PDOException;
use src\Models\User;
use src\Services\Database;

class NotificationRepository
{
    private PDO $pdo;

    public function __construct()
    {
        // FIX: initialize PDO once
        $this->pdo = Database::getInstance()->getDB();
    }

    public function fetchUnreadNotificationCount(int $idUser): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM notification WHERE idUser = :idUser AND isRead = 0');
        $stmt->execute([':idUser' => $idUser]);
        return (int)$stmt->fetchColumn();
    }

    public function fetchNotifications(int $idUser, int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT idNotification, type, title, message, url, isRead, readAt, createdAt
             FROM notification
             WHERE idUser = :idUser
             ORDER BY isRead ASC, createdAt DESC
             LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function markNotificationAsRead(int $idNotification, int $idUser): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE notification
             SET isRead = 1, readAt = NOW()
             WHERE idNotification = :id AND idUser = :idUser AND isRead = 0'
        );
        return $stmt->execute([':id' => $idNotification, ':idUser' => $idUser]);
    }

    public function markAllNotificationsAsRead(int $idUser): int
    {
        $stmt = $this->pdo->prepare(
            'UPDATE notification
             SET isRead = 1, readAt = NOW()
             WHERE idUser = :idUser AND isRead = 0'
        );
        $stmt->execute([':idUser' => $idUser]);
        return (int)$stmt->rowCount();
    }

    // Controller-facing wrappers (names expected by UserController)
    public function getUnreadNotificationsCount(int $idUser): int
    {
        return $this->fetchUnreadNotificationCount($idUser);
    }

    public function getNotificationsPage(int $idUser, int $limit, int $offset): array
    {
        return $this->fetchNotifications($idUser, $limit, $offset);
    }

    public function markNotificationRead(int $idUser, int $idNotification): bool
    {
        return $this->markNotificationAsRead($idNotification, $idUser);
    }

    public function markAllNotificationsRead(int $idUser): int
    {
        return $this->markAllNotificationsAsRead($idUser);
    }
}
