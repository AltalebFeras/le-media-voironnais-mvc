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

    /**
     * Get companies for a specific user
     */
    public function getUserEntreprises($idUser): array
    {
        try {
            $query = "SELECT * FROM entreprise WHERE idUser = :idUser ORDER BY name ASC";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idUser' => $idUser]);
            
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

    /**
     * Create a new company
     */
    public function createEntreprise(Entreprise $entreprise): Entreprise
    {
        try {
            $query = "INSERT INTO entreprise 
                     (name, description, logoPath, bannerPath, address, phone, email, website, siret, status, isActive, idUser, idVille, createdAt) 
                     VALUES 
                     (:name, :description, :logoPath, :bannerPath, :address, :phone, :email, :website, :siret, :status, :isActive, :idUser, :idVille, :createdAt)";
            
            $stmt = $this->DB->prepare($query);
            $stmt->execute([
                'name' => $entreprise->getName(),
                'description' => $entreprise->getDescription(),
                'logoPath' => $entreprise->getLogoPath(),
                'bannerPath' => $entreprise->getBannerPath(),
                'address' => $entreprise->getAddress(),
                'phone' => $entreprise->getPhone(),
                'email' => $entreprise->getEmail(),
                'website' => $entreprise->getWebsite(),
                'siret' => $entreprise->getSiret(),
                'status' => $entreprise->getStatus(),
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
                     description = :description,
                     address = :address, 
                     phone = :phone, 
                     email = :email, 
                     website = :website,
                     siret = :siret,
                     status = :status,
                     isActive = :isActive,
                     updatedAt = :updatedAt
                     WHERE idEntreprise = :idEntreprise";
            
            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'name' => $entreprise->getName(),
                'description' => $entreprise->getDescription(),
                'address' => $entreprise->getAddress(),
                'phone' => $entreprise->getPhone(),
                'email' => $entreprise->getEmail(),
                'website' => $entreprise->getWebsite(),
                'siret' => $entreprise->getSiret(),
                'status' => $entreprise->getStatus(),
                'isActive' => $entreprise->getIsActive(),
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
            $query = "DELETE FROM entreprise WHERE idEntreprise = :idEntreprise";
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

    public function findAll($includeDeleted = false) {
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

    public function findByUser($idUser, $includeDeleted = false) {
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

    public function findById($id) {
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

    public function create(Entreprise $entreprise) {
        $sql = "INSERT INTO entreprise (name, description, logoPath, bannerPath, address, phone, email, website, siret, status, isActive, isPublic, idUser, idVille, createdAt) 
                VALUES (:name, :description, :logoPath, :bannerPath, :address, :phone, :email, :website, :siret, :status, :isActive, :isPublic, :idUser, :idVille, NOW())";
        
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
        $stmt->bindParam(':status', $entreprise->getStatus());
        $stmt->bindParam(':isActive', $entreprise->getIsActive(), PDO::PARAM_BOOL);
        $stmt->bindParam(':isPublic', $entreprise->getIsPublic(), PDO::PARAM_BOOL);
        $stmt->bindParam(':idUser', $entreprise->getIdUser(), PDO::PARAM_INT);
        $stmt->bindParam(':idVille', $entreprise->getIdVille(), PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $entreprise->setIdEntreprise($this->DB->lastInsertId());
            return $entreprise;
        }
        
        return false;
    }

    public function update(Entreprise $entreprise) {
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
                status = :status, 
                isActive = :isActive, 
                isPublic = :isPublic, 
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
        $stmt->bindParam(':status', $entreprise->getStatus());
        $stmt->bindParam(':isActive', $entreprise->getIsActive(), PDO::PARAM_BOOL);
        $stmt->bindParam(':isPublic', $entreprise->getIsPublic(), PDO::PARAM_BOOL);
        $stmt->bindParam(':idVille', $entreprise->getIdVille(), PDO::PARAM_INT);
        $stmt->bindParam(':id', $entreprise->getIdEntreprise(), PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "UPDATE entreprise SET isDeleted = 1, deletedAt = NOW() WHERE idEntreprise = :id";
        
        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function permanentDelete($id) {
        $sql = "DELETE FROM entreprise WHERE idEntreprise = :id";
        
        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function restore($id) {
        $sql = "UPDATE entreprise SET isDeleted = 0, deletedAt = NULL WHERE idEntreprise = :id";
        
        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function findByStatus($status, $includeDeleted = false) {
        $sql = "SELECT e.*, v.ville_nom, u.firstName, u.lastName 
                FROM entreprise e 
                LEFT JOIN ville v ON e.idVille = v.idVille 
                LEFT JOIN user u ON e.idUser = u.idUser 
                WHERE e.status = :status";
        
        if (!$includeDeleted) {
            $sql .= " AND e.isDeleted = 0";
        }
        
        $sql .= " ORDER BY e.createdAt DESC";
        
        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':status', $status);
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

    public function searchByName($query) {
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

    public function getStatistics() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'actif' THEN 1 ELSE 0 END) as actives,
                    SUM(CASE WHEN status = 'brouillon' THEN 1 ELSE 0 END) as brouillons,
                    SUM(CASE WHEN status = 'suspendu' THEN 1 ELSE 0 END) as suspendues,
                    SUM(CASE WHEN isDeleted = 1 THEN 1 ELSE 0 END) as supprimees
                FROM entreprise";
        
        $stmt = $this->DB->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
