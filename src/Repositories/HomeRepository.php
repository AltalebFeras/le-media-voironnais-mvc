<?php

namespace src\Repositories;

use Exception;
use PDO;
use PDOException;
use src\Models\Contact;
use src\Services\Database;

class HomeRepository
{
    private $DB;

    public function __construct()
    {
        $this->DB = Database::getInstance()->getDB();
    }

    public function searchUsers(string $query, int $limit = 5): array
    {
        try {
            $sql = "SELECT firstName, lastName, avatarPath, slug FROM user WHERE (firstName LIKE :query OR lastName LIKE :query) AND isActivated = 1 AND isBanned = 0 AND isDeleted = 0 LIMIT :limit";
            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function searchEvents(string $query, int $limit = 5): array
    {
        try {
            $sql = "SELECT title, slug, bannerPath FROM evenement WHERE title LIKE :query AND isPublic = 1 AND isDeleted = 0 LIMIT :limit";
            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function searchEntreprises(string $query, int $limit = 5): array
    {
        try {
            $sql = "SELECT name, slug, logoPath FROM entreprise WHERE name LIKE :query AND isActive = 1 AND isDeleted = 0 LIMIT :limit";
            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function searchAssociations(string $query, int $limit = 5): array
    {
        try {
            $sql = "SELECT name, slug, logoPath FROM association WHERE name LIKE :query AND isActive = 1 AND isDeleted = 0 LIMIT :limit";
            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function searchVilles(string $query, int $limit = 5): array
    {
        try {
            $sql = "SELECT ville_nom as name, ville_slug as slug, ville_code_postal as code_postal FROM ville WHERE (ville_nom LIKE :query OR ville_nom_simple LIKE :query) LIMIT :limit";
            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
