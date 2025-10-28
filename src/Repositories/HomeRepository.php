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
            // get the category slug and the ville slug and evenement slug
            $sql = "SELECT e.*, ec.slug AS category_slug, v.ville_slug AS ville_slug FROM evenement e
                    LEFT JOIN event_category ec ON e.idEventCategory = ec.idEventCategory
                    LEFT JOIN ville v ON e.idVille = v.idVille
                    WHERE e.title LIKE :query AND e.isPublic = 1 AND e.isDeleted = 0 LIMIT :limit";
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

    public function getVilleBySlug(string $slug): ?array
    {
        try {
            $sql = "SELECT * FROM ville WHERE ville_slug = :slug LIMIT 1";
            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getEventsByVille(int $villeId, int $limit = 10): array
    {
        try {
            $sql = "SELECT e.*, u.firstName, u.lastName, v.ville_slug, v.ville_nom_reel, c.slug as category_slug FROM evenement e 
                    LEFT JOIN user u ON e.idUser = u.idUser 
                    LEFT JOIN ville v ON e.idVille = v.idVille
                    LEFT JOIN event_category c ON e.idEventCategory = c.idEventCategory
                    WHERE e.idVille = :villeId AND e.isPublic = 1 AND e.isDeleted = 0 
                    ORDER BY e.startDate ASC LIMIT :limit";
            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':villeId', $villeId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getEntreprisesByVille(int $villeId, int $limit = 10): array
    {
        try {
            $sql = "SELECT * FROM entreprise WHERE idVille = :villeId AND isActive = 1 AND isDeleted = 0 ORDER BY name ASC LIMIT :limit";
            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':villeId', $villeId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAssociationsByVille(int $villeId, int $limit = 10): array
    {
        try {
            $sql = "SELECT * FROM association WHERE idVille = :villeId AND isActive = 1 AND isDeleted = 0 ORDER BY name ASC LIMIT :limit";
            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':villeId', $villeId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllVilles(int $itemsPerPage, int $offset): array
    {
        try {
            $sql = "SELECT ville_nom as name, ville_slug as slug, ville_code_postal as code_postal, ville_population_2012 as population 
                    FROM ville ORDER BY ville_nom ASC LIMIT :limit OFFSET :offset";
            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    public function getTotalVillesCount(): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM ville";
            $stmt = $this->DB->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
}
