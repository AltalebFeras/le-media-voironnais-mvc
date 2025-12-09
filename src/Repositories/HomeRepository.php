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

    /**
     * Get upcoming events
     */
    public function getUpcomingEvents(int $limit = 6): array
    {
        $sql = "SELECT e.*, v.ville_nom_reel, v.ville_slug, ec.name as category_name, ec.slug as category_slug
                FROM evenement e
                LEFT JOIN ville v ON e.idVille = v.idVille
                LEFT JOIN event_category ec ON e.idEventCategory = ec.idEventCategory
                WHERE e.isPublic = 1 
                AND e.isDeleted = 0 
                AND e.startDate >= NOW()
                ORDER BY e.startDate ASC
                LIMIT :limit";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent past events
     */
    public function getRecentEvents(int $limit = 6): array
    {
        $sql = "SELECT e.*, v.ville_nom_reel, v.ville_slug, ec.name as category_name, ec.slug as category_slug
                FROM evenement e
                LEFT JOIN ville v ON e.idVille = v.idVille
                LEFT JOIN event_category ec ON e.idEventCategory = ec.idEventCategory
                WHERE e.isPublic = 1 
                AND e.isDeleted = 0 
                AND e.endDate < NOW()
                ORDER BY e.endDate DESC
                LIMIT :limit";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent active enterprises
     */
    public function getRecentActiveEnterprises(int $limit = 6): array
    {
        $sql = "SELECT e.*, v.ville_nom_reel, v.ville_slug
                FROM entreprise e
                LEFT JOIN ville v ON e.idVille = v.idVille
                WHERE e.isActive = 1 
                AND e.isDeleted = 0
                ORDER BY e.createdAt DESC
                LIMIT :limit";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent active associations
     */
    public function getRecentActiveAssociations(int $limit = 6): array
    {
        $sql = "SELECT a.*, v.ville_nom_reel, v.ville_slug
                FROM association a
                LEFT JOIN ville v ON a.idVille = v.idVille
                WHERE a.isActive = 1 
                AND a.isDeleted = 0
                ORDER BY a.createdAt DESC
                LIMIT :limit";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get featured cities (cities with most events)
     */
    public function getFeaturedCities(int $limit = 6): array
    {
        $sql = "SELECT v.*, COUNT(e.idEvenement) as events_count
                FROM ville v
                LEFT JOIN evenement e ON v.idVille = e.idVille 
                    AND e.isPublic = 1 
                    AND e.isDeleted = 0
                GROUP BY v.idVille
                HAVING events_count > 0
                ORDER BY events_count DESC, v.ville_nom_reel ASC
                LIMIT :limit";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total events count
     */
    public function getTotalEventsCount(): int
    {
        $sql = "SELECT COUNT(*) FROM evenement WHERE isPublic = 1 AND isDeleted = 0";
        $stmt = $this->DB->query($sql);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get total active enterprises count
     */
    public function getTotalActiveEnterprisesCount(): int
    {
        $sql = "SELECT COUNT(*) FROM entreprise WHERE isActive = 1 AND isDeleted = 0";
        $stmt = $this->DB->query($sql);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get total active associations count
     */
    public function getTotalActiveAssociationsCount(): int
    {
        $sql = "SELECT COUNT(*) FROM association WHERE isActive = 1 AND isDeleted = 0";
        $stmt = $this->DB->query($sql);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get total cities count
     */
    public function getTotalCitiesCount(): int
    {
        $sql = "SELECT COUNT(*) FROM ville";
        $stmt = $this->DB->query($sql);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Search users
     */
    public function searchUsers(string $query, int $limit = 5): array
    {
        try {
            $sql = "SELECT idUser, firstName, lastName, avatarPath, slug
                    FROM user
                    WHERE (firstName LIKE :query OR lastName LIKE :query)
                    AND isActivated = 1
                    AND isDeleted = 0
                    AND isBanned = 0
                    LIMIT :limit";

            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Search events
     */
    public function searchEvents(string $query, int $limit = 5): array
    {
        try {
            $sql = "SELECT e.title, e.slug, e.bannerPath, v.ville_slug, ec.slug as category_slug
                    FROM evenement e
                    LEFT JOIN ville v ON e.idVille = v.idVille
                    LEFT JOIN event_category ec ON e.idEventCategory = ec.idEventCategory
                    WHERE e.title LIKE :query
                    AND e.isPublic = 1
                    AND e.isDeleted = 0
                    LIMIT :limit";

            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Search enterprises
     */
    public function searchEntreprises(string $query, int $limit = 5): array
    {
        try {
            $sql = "SELECT name, slug, logoPath
                    FROM entreprise
                    WHERE name LIKE :query
                    AND isActive = 1
                    AND isDeleted = 0
                    LIMIT :limit";

            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Search associations
     */
    public function searchAssociations(string $query, int $limit = 5): array
    {
        try {
            $sql = "SELECT name, slug, logoPath
                    FROM association
                    WHERE name LIKE :query
                    AND isActive = 1
                    AND isDeleted = 0
                    LIMIT :limit";

            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Search villes
     */
    public function searchVilles(string $query, int $limit = 5): array
    {
        try {
            $sql = "SELECT ville_nom_reel as name, ville_slug as slug, ville_code_postal as code_postal
                    FROM ville
                    WHERE ville_nom_reel LIKE :query
                    OR ville_code_postal LIKE :query
                    LIMIT :limit";

            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get ville by slug
     */
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

    /**
     * Get events by ville
     */
    public function getEventsByVille(int $idVille, int $limit = 10): array
    {
        try {
            $sql = "SELECT e.*, ec.name as category_name, ec.slug as category_slug ,v.ville_nom_reel, v.ville_slug
                    FROM evenement e
                    LEFT JOIN event_category ec ON e.idEventCategory = ec.idEventCategory
                    LEFT JOIN ville v ON e.idVille = v.idVille
                    WHERE e.idVille = :idVille
                    AND e.isPublic = 1
                    AND e.isDeleted = 0
                    ORDER BY e.startDate DESC
                    LIMIT :limit";

            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':idVille', $idVille, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get enterprises by ville
     */
    public function getEntreprisesByVille(int $idVille, int $limit = 10): array
    {
        try {
            $sql = "SELECT * FROM entreprise
                    WHERE idVille = :idVille
                    AND isActive = 1
                    AND isDeleted = 0
                    ORDER BY name ASC
                    LIMIT :limit";

            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':idVille', $idVille, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get associations by ville
     */
    public function getAssociationsByVille(int $idVille, int $limit = 10): array
    {
        try {
            $sql = "SELECT * FROM association
                    WHERE idVille = :idVille
                    AND isActive = 1
                    AND isDeleted = 0
                    ORDER BY name ASC
                    LIMIT :limit";

            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':idVille', $idVille, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get all villes with pagination
     */
    public function getAllVilles(int $itemsPerPage, int $offset): array
    {
        try {
            $sql = "SELECT * FROM ville
                    ORDER BY ville_nom_reel ASC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get total villes count
     */
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
