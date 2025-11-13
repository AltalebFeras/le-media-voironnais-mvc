<?php

namespace src\Repositories;

use src\Models\User;
use src\Services\Database;
use PDO;
use Exception;

class FriendRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getDB();
    }

    /**
     * Send a friend request
     */
    public function sendFriendRequest(int $idUser, int $idFriend): bool
    {
        try {
            // Check if friendship already exists
            if ($this->friendshipExists($idUser, $idFriend)) {
                throw new Exception("Une demande d'amitié existe déjà ou vous êtes déjà amis");
            }

            $sql = "INSERT INTO user_friend (idUser, idFriend, status, requestedAt) 
                    VALUES (:idUser, :idFriend, 'en_attente', NOW())";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'idUser' => $idUser,
                'idFriend' => $idFriend
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Accept a friend request
     */
    public function acceptFriendRequest(int $idUser, int $idFriend): bool
    {
        try {
            $this->db->beginTransaction();

            // Update the original request
            $sql = "UPDATE user_friend 
                    SET status = 'accepte', respondedAt = NOW() 
                    WHERE idUser = :idFriend AND idFriend = :idUser AND status = 'en_attente'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['idUser' => $idUser, 'idFriend' => $idFriend]);

            // Create reciprocal friendship
            $sql2 = "INSERT INTO user_friend (idUser, idFriend, status, requestedAt, respondedAt) 
                     VALUES (:idUser, :idFriend, 'accepte', NOW(), NOW())";
            
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute(['idUser' => $idUser, 'idFriend' => $idFriend]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Refuse a friend request
     */
    public function refuseFriendRequest(int $idUser, int $idFriend): bool
    {
        try {
            $sql = "UPDATE user_friend 
                    SET status = 'refuse', respondedAt = NOW() 
                    WHERE idUser = :idFriend AND idFriend = :idUser AND status = 'en_attente'";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['idUser' => $idUser, 'idFriend' => $idFriend]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Block a friend
     */
    public function blockFriend(int $idUser, int $idFriend): bool
    {
        try {
            $this->db->beginTransaction();

            // Update or insert blocking relationship
            $sql = "INSERT INTO user_friend (idUser, idFriend, status, requestedAt) 
                    VALUES (:idUser, :idFriend, 'bloque', NOW())
                    ON DUPLICATE KEY UPDATE status = 'bloque', respondedAt = NOW()";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['idUser' => $idUser, 'idFriend' => $idFriend]);

            // Remove any existing friendship from the other side
            $sql2 = "DELETE FROM user_friend 
                     WHERE idUser = :idFriend AND idFriend = :idUser";
            
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute(['idUser' => $idUser, 'idFriend' => $idFriend]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Remove friend
     */
    public function removeFriend(int $idUser, int $idFriend): bool
    {
        try {
            $this->db->beginTransaction();

            // Remove friendship from both sides
            $sql = "DELETE FROM user_friend 
                    WHERE (idUser = :idUser AND idFriend = :idFriend) 
                    OR (idUser = :idFriend AND idFriend = :idUser)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['idUser' => $idUser, 'idFriend' => $idFriend]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Get user's friends list
     */
    public function getUserFriends(int $idUser, int $page = 1, int $limit = 12): array
    {
        try {
            $offset = ($page - 1) * $limit;
            
            $sql = "SELECT u.idUser, u.uiid, u.slug, u.firstName, u.lastName, 
                           u.avatarPath, u.bio, u.isOnline, u.lastSeen,
                           uf.requestedAt as friendSince
                    FROM user_friend uf
                    JOIN user u ON u.idUser = uf.idFriend
                    WHERE uf.idUser = :idUser AND uf.status = 'accepte' 
                    AND u.isActivated = 1 AND u.isDeleted = 0 AND u.isBanned = 0
                    ORDER BY u.firstName, u.lastName
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get pending friend requests sent to user
     */
    public function getPendingRequests(int $idUser): array
    {
        try {
            $sql = "SELECT u.idUser, u.uiid, u.slug, u.firstName, u.lastName, 
                           u.avatarPath, u.bio, uf.requestedAt
                    FROM user_friend uf
                    JOIN user u ON u.idUser = uf.idUser
                    WHERE uf.idFriend = :idUser AND uf.status = 'en_attente'
                    AND u.isActivated = 1 AND u.isDeleted = 0 AND u.isBanned = 0
                    ORDER BY uf.requestedAt DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['idUser' => $idUser]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get sent friend requests
     */
    public function getSentRequests(int $idUser): array
    {
        try {
            $sql = "SELECT u.idUser, u.uiid, u.slug, u.firstName, u.lastName, 
                           u.avatarPath, u.bio, uf.requestedAt, uf.status
                    FROM user_friend uf
                    JOIN user u ON u.idUser = uf.idFriend
                    WHERE uf.idUser = :idUser AND uf.status IN ('en_attente', 'refuse')
                    AND u.isActivated = 1 AND u.isDeleted = 0 AND u.isBanned = 0
                    ORDER BY uf.requestedAt DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['idUser' => $idUser]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Check friendship status between two users
     */
    public function getFriendshipStatus(int $idUser, int $idFriend): ?string
    {
        try {
            $sql = "SELECT status FROM user_friend 
                    WHERE idUser = :idUser AND idFriend = :idFriend";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['idUser' => $idUser, 'idFriend' => $idFriend]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['status'] : null;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Check if friendship exists in any form
     */
    private function friendshipExists(int $idUser, int $idFriend): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM user_friend 
                    WHERE (idUser = :idUser AND idFriend = :idFriend) 
                    OR (idUser = :idFriend AND idFriend = :idUser)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['idUser' => $idUser, 'idFriend' => $idFriend]);

            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Count user's friends
     */
    public function countUserFriends(int $idUser): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM user_friend uf
                    JOIN user u ON u.idUser = uf.idFriend
                    WHERE uf.idUser = :idUser AND uf.status = 'accepte'
                    AND u.isActivated = 1 AND u.isDeleted = 0 AND u.isBanned = 0";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['idUser' => $idUser]);

            return (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Search for potential friends
     */
    public function searchUsers(int $currentUserId, string $query, int $limit = 10): array
    {
        try {
            $sql = "SELECT u.idUser, u.uiid, u.slug, u.firstName, u.lastName, 
                           u.avatarPath, u.bio
                    FROM user u
                    WHERE u.idUser != :currentUserId 
                    AND u.isActivated = 1 AND u.isDeleted = 0 AND u.isBanned = 0
                    AND (u.firstName LIKE :query OR u.lastName LIKE :query 
                         OR CONCAT(u.firstName, ' ', u.lastName) LIKE :query)
                    AND u.idUser NOT IN (
                        SELECT DISTINCT CASE 
                            WHEN uf.idUser = :currentUserId THEN uf.idFriend 
                            WHEN uf.idFriend = :currentUserId THEN uf.idUser 
                        END
                        FROM user_friend uf
                        WHERE (uf.idUser = :currentUserId OR uf.idFriend = :currentUserId)
                        AND uf.status IN ('accepte', 'en_attente', 'bloque')
                    )
                    ORDER BY u.firstName, u.lastName
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $searchTerm = '%' . $query . '%';
            $stmt->bindParam(':currentUserId', $currentUserId, PDO::PARAM_INT);
            $stmt->bindParam(':query', $searchTerm, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
