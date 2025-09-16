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
                     (name, description, logoPath, bannerPath, address, phone, email, website, siret, status, isActive, idUser, createdAt) 
                     VALUES 
                     (:name, :description, :logoPath, :bannerPath, :address, :phone, :email, :website, :siret, :status, :isActive, :idUser, :createdAt)";
            
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
}
