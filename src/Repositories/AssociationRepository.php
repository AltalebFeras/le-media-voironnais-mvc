<?php

namespace src\Repositories;

use Exception;
use PDO;
use PDOException;
use src\Models\Association;
use src\Services\Database;

class AssociationRepository
{
    private $DB;

    public function __construct()
    {
        $this->DB = Database::getInstance()->getDB();
    }

    /**
     * Get associations for a specific user
     */
    public function getUserAssociations($idUser): array
    {
        try {
            // Get associations where user is owner or member
            $query = "SELECT a.* FROM association a 
                      LEFT JOIN user_association ua ON a.idAssociation = ua.idAssociation 
                      WHERE a.idUser = :idUser OR (ua.idUser = :idUser AND ua.isActive = 1)
                      GROUP BY a.idAssociation";
            
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idUser' => $idUser]);
            
            $associations = [];
            while ($row = $stmt->fetchObject(Association::class)) {
                $associations[] = $row;
            }
            
            return $associations;
        } catch (Exception $e) {
            throw new Exception("Error fetching user associations: " . $e->getMessage());
        }
    }

    /**
     * Get a specific association by ID
     */
    public function getAssociationById($idAssociation): ?Association
    {
        try {
            $query = "SELECT * FROM association WHERE idAssociation = :idAssociation";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idAssociation' => $idAssociation]);
            
            $association = $stmt->fetchObject(Association::class);
            return $association !== false ? $association : null;
        } catch (Exception $e) {
            throw new Exception("Error fetching association: " . $e->getMessage());
        }
    }

    public function getVillesByCp($cp)
    {
        try {
            $query = "SELECT idVille, ville_nom_reel, ville_code_postal FROM ville WHERE ville_code_postal = :cp";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['cp' => $cp]);
            
            $villes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $villes;
        } catch (Exception $e) {
            throw new Exception("Error fetching villes by CP: " . $e->getMessage());
        }
    }
    /**
     * Create a new association
     */
    public function createAssociation(Association $association): Association
    {
        try {
            $query = "INSERT INTO association 
                     (name, description, logoPath, bannerPath, address, phone, email, website, isActive, idUser, idVille, createdAt) 
                     VALUES 
                     (:name, :description, :logoPath, :bannerPath, :address, :phone, :email, :website, :isActive, :idUser, :idVille, :createdAt)";
            
            $stmt = $this->DB->prepare($query);
            $stmt->execute([
                'name' => $association->getName(),
                'description' => $association->getDescription(),
                'logoPath' => $association->getLogoPath(),
                'bannerPath' => $association->getBannerPath(),
                'address' => $association->getAddress(),
                'phone' => $association->getPhone(),
                'email' => $association->getEmail(),
                'website' => $association->getWebsite(),
                'isActive' => $association->getIsActive(),
                'idUser' => $association->getIdUser(),
                'idVille' => $association->getIdVille(),
                'createdAt' => $association->getCreatedAt()
            ]);
            
            $association->setIdAssociation($this->DB->lastInsertId());
            return $association;
        } catch (Exception $e) {
            throw new Exception("Error creating association: " . $e->getMessage());
        }
    }
    /**
     * Declare the role of the association creator as 'admin' in user_association table
     */
    public function declareTheRoleOfTheAssociationCreator($idUser, $idAssociation , $joinedAt): bool
    {
        try {
            $query = "INSERT INTO user_association (idUser, idAssociation, role, isActive, joinedAt) 
                      VALUES (:idUser, :idAssociation, 'admin', 1, :joinedAt)";
            $stmt = $this->DB->prepare($query);
            return $stmt->execute([
                'idUser' => $idUser,
                'idAssociation' => $idAssociation,
                'joinedAt' => $joinedAt
            ]);
        } catch (Exception $e) {
            throw new Exception("Error declaring association creator role: " . $e->getMessage());
        }
    }
    public function getAssociationByNameForThisUser($name, $idUser): ?Association
    {
        try {
            $query = "SELECT * FROM association WHERE name = :name AND idUser = :idUser";
            $stmt = $this->DB->prepare($query);
            $stmt->execute([
                'name' => $name,
                'idUser' => $idUser
            ]);
            
            $association = $stmt->fetchObject(Association::class);
            return $association !== false ? $association : null;
        } catch (Exception $e) {
            throw new Exception("Error fetching association by name for user: " . $e->getMessage());
        }
    }
    public function isVilleExists($idVille)
    {
        try {
            $query = "SELECT COUNT(*) FROM ville WHERE idVille = :idVille";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idVille' => $idVille]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            throw new Exception("Error checking if ville exists: " . $e->getMessage());
        }
    }
    /**
     * Update an existing association
     */
    public function updateAssociation(Association $association): bool
    {
        try {
            $query = "UPDATE association SET 
                     name = :name, 
                     description = :description,
                     address = :address, 
                     phone = :phone, 
                     email = :email, 
                     website = :website, 
                     isActive = :isActive,
                     updatedAt = :updatedAt
                     WHERE idAssociation = :idAssociation";
            
            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'name' => $association->getName(),
                'description' => $association->getDescription(),
                'address' => $association->getAddress(),
                'phone' => $association->getPhone(),
                'email' => $association->getEmail(),
                'website' => $association->getWebsite(),
                'isActive' => $association->getIsActive(),
                'updatedAt' => $association->getUpdatedAt(),
                'idAssociation' => $association->getIdAssociation()
            ]);
            
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error updating association: " . $e->getMessage());
        }
    }

    /**
     * Delete an association
     */
    public function deleteAssociation($idAssociation): bool
    {
        try {
            // First delete related records in user_association
            $query = "DELETE FROM user_association WHERE idAssociation = :idAssociation";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idAssociation' => $idAssociation]);
            
            // Then delete related records in association_invitation
            $query = "DELETE FROM association_invitation WHERE idAssociation = :idAssociation";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idAssociation' => $idAssociation]);
            
            // Finally delete the association
            $query = "DELETE FROM association WHERE idAssociation = :idAssociation";
            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute(['idAssociation' => $idAssociation]);
            
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error deleting association: " . $e->getMessage());
        }
    }

    /**
     * Update association logo
     */
    public function updateLogo($idAssociation, $logoPath): bool
    {
        try {
            $query = "UPDATE association SET logoPath = :logoPath WHERE idAssociation = :idAssociation";
            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'logoPath' => $logoPath,
                'idAssociation' => $idAssociation
            ]);
            
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error updating association logo: " . $e->getMessage());
        }
    }

    /**
     * Update association banner
     */
    public function updateBanner($idAssociation, $bannerPath): bool
    {
        try {
            $query = "UPDATE association SET bannerPath = :bannerPath WHERE idAssociation = :idAssociation";
            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'bannerPath' => $bannerPath,
                'idAssociation' => $idAssociation
            ]);
            
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error updating association banner: " . $e->getMessage());
        }
    }

    /**
     * Check if user is the owner of an association
     */
    public function isAssociationOwner($idAssociation, $idUser): bool
    {
        try {
            $query = "SELECT COUNT(*) FROM association WHERE idAssociation = :idAssociation AND idUser = :idUser";
            $stmt = $this->DB->prepare($query);
            $stmt->execute([
                'idAssociation' => $idAssociation,
                'idUser' => $idUser
            ]);
            
            return (int)$stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            throw new Exception("Error checking association ownership: " . $e->getMessage());
        }
    }
}
