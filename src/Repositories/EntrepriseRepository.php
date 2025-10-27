<?php

namespace src\Repositories;

use Exception;
use PDO;
use PDOException;
use src\Models\Entreprise;
use src\Services\Database;

class EntrepriseRepository
{
    private $DB;

    public function __construct()
    {
        $this->DB = Database::getInstance()->getDB();
    }

    public function getIdEntrepriseByUiid($uiid)
    {
        try {
            $query = "SELECT idEntreprise FROM entreprise WHERE uiid = :uiid";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['uiid' => $uiid]);

            return $stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error fetching company by uiid: " . $e->getMessage());
        }
    }
    /**
     * Get companies for a specific user with pagination
     */
    public function getUserEntreprises($idUser, $currentPage = 1, $entreprisesPerPage = 10): array
    {
        try {
            $offset = max(0, ($currentPage - 1) * $entreprisesPerPage);
            $query = "SELECT * FROM entreprise WHERE idUser = :idUser AND isDeleted = 0 ORDER BY name ASC LIMIT :offset, :entreprisesPerPage";
            $stmt = $this->DB->prepare($query);
            $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':entreprisesPerPage', $entreprisesPerPage, PDO::PARAM_INT);
            $stmt->execute();

            $entreprises = [];
            while ($row = $stmt->fetchObject(Entreprise::class)) {
                $entreprises[] = $row;
            }

            return $entreprises;
        } catch (Exception $e) {
            throw new Exception("Error fetching user companies: " . $e->getMessage());
        }
    }

    /**
     * Count companies for a specific user
     */
    public function countUserEntreprises($idUser): int
    {
        try {
            $query = "SELECT COUNT(*) FROM entreprise WHERE idUser = :idUser";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idUser' => $idUser]);

            return (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error counting user companies: " . $e->getMessage());
        }
    }

    /**
     * Get a specific company by ID
     */
    public function getEntrepriseById($idEntreprise): ?Entreprise
    {
        try {
            $query = "SELECT * FROM entreprise WHERE idEntreprise = :idEntreprise";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idEntreprise' => $idEntreprise]);

            $entreprise = $stmt->fetchObject(Entreprise::class);
            return $entreprise !== false ? $entreprise : null;
        } catch (Exception $e) {
            throw new Exception("Error fetching company: " . $e->getMessage());
        }
    }
    public function getAllRealisationByEntrepriseId($idEntreprise)
    {
        try {
            $query = 'SELECT * FROM realisation WHERE idEntreprise = :idEntreprise AND isDeleted = 0';
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idEntreprise' => $idEntreprise]);

            $realisations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $realisations !== false ? $realisations : null;
        } catch (Exception $e) {
            throw new Exception("Error fetching company with realisations: " . $e->getMessage());
        }
    }
    public function getEntrepriseByName($name): ?Entreprise
    {
        try {
            $query = "SELECT * FROM entreprise WHERE name = :name";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['name' => $name]);

            $entreprise = $stmt->fetchObject(Entreprise::class);
            return $entreprise !== false ? $entreprise : null;
        } catch (Exception $e) {
            throw new Exception("Error fetching company by name: " . $e->getMessage());
        }
    }
    public function getAllEntreprises(): array
    {
        try {
            $query = "SELECT * FROM entreprise ORDER BY name ASC";
            $stmt = $this->DB->prepare($query);
            $stmt->execute();

            $entreprises = [];
            while ($row = $stmt->fetchObject(Entreprise::class)) {
                $entreprises[] = $row;
            }

            return $entreprises;
        } catch (Exception $e) {
            throw new Exception("Error fetching all companies: " . $e->getMessage());
        }
    }
    public function getListPublicEntreprises(): array
    {
        //get uiid,name,slug,logoPath, ville_nom_reel for all entreprises where isActive = 1 and isDeleted = 0 
        try {
            $query = "SELECT uiid, name, slug, logoPath, (SELECT ville_nom_reel FROM ville WHERE idVille = entreprise.idVille) AS ville_nom_reel FROM entreprise WHERE isActive = 1 AND isDeleted = 0 ORDER BY name ASC";
            $stmt = $this->DB->prepare($query);
            $stmt->execute();

            $entreprises = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $entreprises;
        } catch (Exception $e) {
            throw new Exception("Error fetching public companies: " . $e->getMessage());
        }
    }

    public function getEntrepriseBySlug($slug)
    {
        try {
            $query = "SELECT e.*, v.ville_nom_reel, u.firstName as creator_firstName, u.lastName as creator_lastName, u.slug as creator_slug
                      FROM entreprise e 
                      LEFT JOIN ville v ON e.idVille = v.idVille 
                      LEFT JOIN user u ON e.idUser = u.idUser 
                      WHERE e.slug = :slug AND e.isDeleted = 0";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['slug' => $slug]);
            $entreprise = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$entreprise) {
                return null;
            }

            // Fetch evenements
            $query = "SELECT title as evenement_title, slug as evenement_slug , bannerPath as evenement_bannerPath FROM evenement WHERE idEntreprise = :idEntreprise AND isDeleted = 0";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idEntreprise' => $entreprise['idEntreprise']]);
            $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $entreprise['evenements'] = $evenements;

            // Fetch realisations with images from realisation_image table
            $query = "SELECT r.title as realisation_title, r.slug as realisation_slug
                      FROM realisation r
                      LEFT JOIN realisation_image ri ON r.idRealisation = ri.idRealisation
                      WHERE r.idEntreprise = :idEntreprise AND r.isDeleted = 0";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idEntreprise' => $entreprise['idEntreprise']]);
            $realisations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $entreprise['realisations'] = $realisations;

            return $entreprise;
        } catch (Exception $e) {
            throw new Exception("Error fetching company by slug: " . $e->getMessage());
        }
    }
    public function isSlugExists($slug): bool
    {
        try {
            $query = "SELECT COUNT(*) FROM entreprise WHERE slug = :slug";
            $stmt = $this->DB->prepare($query);
            $stmt->bindParam(':slug', $slug);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            throw new Exception("Error checking slug existence: " . $e->getMessage());
        }
    }
    /**
     * Create a new company
     */
    public function createEntreprise(Entreprise $entreprise): Entreprise
    {
        try {
            $query = "INSERT INTO entreprise 
                     (uiid, name, slug, description, logoPath, bannerPath, address, phone, email, website, siret, isActive, idUser, idVille, createdAt) 
                     VALUES 
                     (:uiid, :name, :slug, :description, :logoPath, :bannerPath, :address, :phone, :email, :website, :siret, :isActive, :idUser, :idVille, :createdAt)";

            $stmt = $this->DB->prepare($query);
            $stmt->execute([
                'uiid' => $entreprise->getUiid(),
                'name' => $entreprise->getName(),
                'slug' => $entreprise->getSlug(),
                'description' => $entreprise->getDescription(),
                'logoPath' => $entreprise->getLogoPath(),
                'bannerPath' => $entreprise->getBannerPath(),
                'address' => $entreprise->getAddress(),
                'phone' => $entreprise->getPhone(),
                'email' => $entreprise->getEmail(),
                'website' => $entreprise->getWebsite(),
                'siret' => $entreprise->getSiret(),
                'isActive' => $entreprise->getIsActive(),
                'idUser' => $entreprise->getIdUser(),
                'idVille' => $entreprise->getIdVille(),
                'createdAt' => $entreprise->getCreatedAt()
            ]);

            $entreprise->setIdEntreprise($this->DB->lastInsertId());
            return $entreprise;
        } catch (Exception $e) {
            throw new Exception("Error creating company: " . $e->getMessage());
        }
    }

    /**
     * Update an existing company
     */
    public function updateEntreprise(Entreprise $entreprise): bool
    {
        try {
            $query = "UPDATE entreprise SET 
                     name = :name, 
                     slug = :slug,
                     description = :description,
                     address = :address, 
                     phone = :phone, 
                     email = :email, 
                     website = :website,
                     siret = :siret,
                     idVille = :idVille,
                     updatedAt = :updatedAt
                     WHERE idEntreprise = :idEntreprise";

            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'name' => $entreprise->getName(),
                'slug' => $entreprise->getSlug(),
                'description' => $entreprise->getDescription(),
                'address' => $entreprise->getAddress(),
                'phone' => $entreprise->getPhone(),
                'email' => $entreprise->getEmail(),
                'website' => $entreprise->getWebsite(),
                'siret' => $entreprise->getSiret(),
                'idVille' => $entreprise->getIdVille(),
                'updatedAt' => $entreprise->getUpdatedAt(),
                'idEntreprise' => $entreprise->getIdEntreprise()
            ]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error updating company: " . $e->getMessage());
        }
    }

    /**
     * Delete a company
     */
    public function deleteEntreprise($idEntreprise): bool
    {
        try {
            $query = "UPDATE entreprise SET isDeleted = 1 WHERE idEntreprise = :idEntreprise";
            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute(['idEntreprise' => $idEntreprise]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error deleting company: " . $e->getMessage());
        }
    }

    /**
     * Update company logo
     */
    public function updateLogo($idEntreprise, $logoPath): bool
    {
        try {
            $query = "UPDATE entreprise SET logoPath = :logoPath WHERE idEntreprise = :idEntreprise";
            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'logoPath' => $logoPath,
                'idEntreprise' => $idEntreprise
            ]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error updating company logo: " . $e->getMessage());
        }
    }

    /**
     * Update company banner
     */
    public function updateBanner($idEntreprise, $bannerPath): bool
    {
        try {
            $query = "UPDATE entreprise SET bannerPath = :bannerPath WHERE idEntreprise = :idEntreprise";
            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'bannerPath' => $bannerPath,
                'idEntreprise' => $idEntreprise
            ]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error updating company banner: " . $e->getMessage());
        }
    }

    /**
     * Check if user is the owner of a company
     */
    public function isEntrepriseOwner($idEntreprise, $idUser): bool
    {
        try {
            $query = "SELECT COUNT(*) FROM entreprise WHERE idEntreprise = :idEntreprise AND idUser = :idUser";
            $stmt = $this->DB->prepare($query);
            $stmt->execute([
                'idEntreprise' => $idEntreprise,
                'idUser' => $idUser
            ]);

            return (int)$stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            throw new Exception("Error checking company ownership: " . $e->getMessage());
        }
    }

    public function findAll($includeDeleted = false)
    {
        $sql = "SELECT e.*, v.ville_nom, u.firstName, u.lastName 
                FROM entreprise e 
                LEFT JOIN ville v ON e.idVille = v.idVille 
                LEFT JOIN user u ON e.idUser = u.idUser";

        if (!$includeDeleted) {
            $sql .= " WHERE e.isDeleted = 0";
        }

        $sql .= " ORDER BY e.createdAt DESC";

        $stmt = $this->DB->prepare($sql);
        $stmt->execute();

        $entreprises = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entreprise = new Entreprise($row);
            $entreprise->ville_nom = $row['ville_nom'];
            $entreprise->owner_name = $row['firstName'] . ' ' . $row['lastName'];
            $entreprises[] = $entreprise;
        }

        return $entreprises;
    }

    public function findByUser($idUser, $includeDeleted = false)
    {
        $sql = "SELECT e.*, v.ville_nom 
                FROM entreprise e 
                LEFT JOIN ville v ON e.idVille = v.idVille 
                WHERE e.idUser = :idUser";

        if (!$includeDeleted) {
            $sql .= " AND e.isDeleted = 0";
        }

        $sql .= " ORDER BY e.createdAt DESC";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();

        $entreprises = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entreprise = new Entreprise($row);
            $entreprise->ville_nom = $row['ville_nom'];
            $entreprises[] = $entreprise;
        }

        return $entreprises;
    }

    public function findById($id)
    {
        $sql = "SELECT e.*, v.ville_nom, u.firstName, u.lastName 
                FROM entreprise e 
                LEFT JOIN ville v ON e.idVille = v.idVille 
                LEFT JOIN user u ON e.idUser = u.idUser 
                WHERE e.idEntreprise = :id";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $entreprise = new Entreprise($row);
            $entreprise->ville_nom = $row['ville_nom'];
            $entreprise->owner_name = $row['firstName'] . ' ' . $row['lastName'];
            return $entreprise;
        }

        return null;
    }

    public function create(Entreprise $entreprise)
    {
        $sql = "INSERT INTO entreprise (name, description, logoPath, bannerPath, address, phone, email, website, siret, isActive, idUser, idVille, createdAt) 
                VALUES (:name, :description, :logoPath, :bannerPath, :address, :phone, :email, :website, :siret, :isActive, :idUser, :idVille, NOW())";

        $stmt = $this->DB->prepare($sql);

        $stmt->bindParam(':name', $entreprise->getName());
        $stmt->bindParam(':description', $entreprise->getDescription());
        $stmt->bindParam(':logoPath', $entreprise->getLogoPath());
        $stmt->bindParam(':bannerPath', $entreprise->getBannerPath());
        $stmt->bindParam(':address', $entreprise->getAddress());
        $stmt->bindParam(':phone', $entreprise->getPhone());
        $stmt->bindParam(':email', $entreprise->getEmail());
        $stmt->bindParam(':website', $entreprise->getWebsite());
        $stmt->bindParam(':siret', $entreprise->getSiret());
        $stmt->bindParam(':isActive', $entreprise->getIsActive(), PDO::PARAM_BOOL);
        $stmt->bindParam(':idUser', $entreprise->getIdUser(), PDO::PARAM_INT);
        $stmt->bindParam(':idVille', $entreprise->getIdVille(), PDO::PARAM_INT);

        if ($stmt->execute()) {
            $entreprise->setIdEntreprise($this->DB->lastInsertId());
            return $entreprise;
        }

        return false;
    }

    public function update(Entreprise $entreprise)
    {
        $sql = "UPDATE entreprise SET 
                name = :name, 
                description = :description, 
                logoPath = :logoPath, 
                bannerPath = :bannerPath, 
                address = :address, 
                phone = :phone, 
                email = :email, 
                website = :website, 
                siret = :siret, 
                idVille = :idVille, 
                updatedAt = NOW() 
                WHERE idEntreprise = :id";

        $stmt = $this->DB->prepare($sql);

        $stmt->bindParam(':name', $entreprise->getName());
        $stmt->bindParam(':description', $entreprise->getDescription());
        $stmt->bindParam(':logoPath', $entreprise->getLogoPath());
        $stmt->bindParam(':bannerPath', $entreprise->getBannerPath());
        $stmt->bindParam(':address', $entreprise->getAddress());
        $stmt->bindParam(':phone', $entreprise->getPhone());
        $stmt->bindParam(':email', $entreprise->getEmail());
        $stmt->bindParam(':website', $entreprise->getWebsite());
        $stmt->bindParam(':siret', $entreprise->getSiret());
        $stmt->bindParam(':idVille', $entreprise->getIdVille(), PDO::PARAM_INT);
        $stmt->bindParam(':id', $entreprise->getIdEntreprise(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function isEntrepriseHasEvents($idEntreprise): bool
    {
        try {
            $query = "SELECT COUNT(*) FROM evenement WHERE idEntreprise = :idEntreprise AND isDeleted = 0 AND startDate >= DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idEntreprise' => $idEntreprise]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            throw new Exception("Error checking if entreprise has events: " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $sql = "UPDATE entreprise SET isDeleted = 1, deletedAt = NOW() WHERE idEntreprise = :id";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function permanentDelete($id)
    {
        $sql = "DELETE FROM entreprise WHERE idEntreprise = :id";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function restore($id)
    {
        $sql = "UPDATE entreprise SET isDeleted = 0, deletedAt = NULL WHERE idEntreprise = :id";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function searchByName($query)
    {
        $sql = "SELECT e.*, v.ville_nom, u.firstName, u.lastName 
                FROM entreprise e 
                LEFT JOIN ville v ON e.idVille = v.idVille 
                LEFT JOIN user u ON e.idUser = u.idUser 
                WHERE e.name LIKE :query AND e.isDeleted = 0 
                ORDER BY e.name ASC";

        $stmt = $this->DB->prepare($sql);
        $searchTerm = '%' . $query . '%';
        $stmt->bindParam(':query', $searchTerm);
        $stmt->execute();

        $entreprises = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entreprise = new Entreprise($row);
            $entreprise->ville_nom = $row['ville_nom'];
            $entreprise->owner_name = $row['firstName'] . ' ' . $row['lastName'];
            $entreprises[] = $entreprise;
        }

        return $entreprises;
    }

    public function updateEntrepriseBanner(Entreprise $entreprise): bool
    {
        try {
            $query = "UPDATE entreprise SET 
                     bannerPath = :bannerPath,
                     updatedAt = :updatedAt
                     WHERE idEntreprise = :idEntreprise";

            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'bannerPath' => $entreprise->getBannerPath(),
                'updatedAt' => $entreprise->getUpdatedAt(),
                'idEntreprise' => $entreprise->getIdEntreprise()
            ]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error updating entreprise banner: " . $e->getMessage());
        }
    }

    public function updateEntrepriseLogo(Entreprise $entreprise): bool
    {
        try {
            $query = "UPDATE entreprise SET 
                     logoPath = :logoPath,
                     updatedAt = :updatedAt
                     WHERE idEntreprise = :idEntreprise";

            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'logoPath' => $entreprise->getLogoPath(),
                'updatedAt' => $entreprise->getUpdatedAt(),
                'idEntreprise' => $entreprise->getIdEntreprise()
            ]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error updating entreprise logo: " . $e->getMessage());
        }
    }
    public function markActivationRequested($idEntreprise, $requestDate): bool
    {
        try {
            $query = "UPDATE entreprise SET 
                     hasRequestForActivation = 1,
                     requestDate = :requestDate,
                     updatedAt = NOW()
                     WHERE idEntreprise = :idEntreprise";

            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'requestDate' => $requestDate,
                'idEntreprise' => $idEntreprise
            ]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error marking activation request: " . $e->getMessage());
        }
    }
    public function activateEntreprise($idEntreprise): bool
    {
        try {
            $query = "UPDATE entreprise SET 
                     isActive = 1,
                     hasRequestForActivation = 0,
                     requestDate = NULL,
                     updatedAt = NOW()
                     WHERE idEntreprise = :idEntreprise";

            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'idEntreprise' => $idEntreprise
            ]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error activating entreprise: " . $e->getMessage());
        }
    }
}
