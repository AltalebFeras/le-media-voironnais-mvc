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
    public function getUserAssociations($idUser, $currentPage, $associationsPerPage): array
    {
        try {
            // Get associations where user is owner or member
            $offset = max(0, ($currentPage - 1) * $associationsPerPage);
            $query = "SELECT a.* FROM association a 
                      LEFT JOIN user_association ua ON a.idAssociation = ua.idAssociation 
                      WHERE a.idUser = :idUser OR (ua.idUser = :idUser AND ua.isActive = 1 AND a.isDeleted = 0)
                      GROUP BY a.idAssociation
                      LIMIT :offset, :associationsPerPage";

            $stmt = $this->DB->prepare($query);
            $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':associationsPerPage', $associationsPerPage, PDO::PARAM_INT);
            $stmt->execute();

            $associations = [];
            while ($row = $stmt->fetchObject(Association::class)) {
                $associations[] = $row;
            }

            return $associations;
        } catch (Exception $e) {
            throw new Exception("Error fetching user associations: " . $e->getMessage());
        }
    }

    public function countUserAssociations($idUser): int
    {
        try {
            $query = "SELECT COUNT(DISTINCT a.idAssociation) FROM association a 
                      LEFT JOIN user_association ua ON a.idAssociation = ua.idAssociation 
                      WHERE a.idUser = :idUser OR (ua.idUser = :idUser AND ua.isActive = 1 AND a.isDeleted = 0)";

            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idUser' => $idUser]);

            return (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error counting user associations: " . $e->getMessage());
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
    public function isSlugExists($slug): bool
    {
        try {
            $query = "SELECT COUNT(*) FROM association WHERE slug = :slug";
            $stmt = $this->DB->prepare($query);
            $stmt->bindParam(':slug', $slug);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error checking slug existence: " . $e->getMessage());
        }
    }

    /**
     * Create a new association and declare the creator as admin
     */
    public function createAssociation(Association $association): Association
    {
        try {
            // Start transaction for both operations
            $this->DB->beginTransaction();

            // Create association
            $query = "INSERT INTO association 
                         (name, slug, description, logoPath, bannerPath, address, phone, email, website, isActive, idUser, idVille, createdAt) 
                     VALUES 
                     (:name, :slug, :description, :logoPath, :bannerPath, :address, :phone, :email, :website, :isActive, :idUser, :idVille, :createdAt)";

            $stmt = $this->DB->prepare($query);
            $stmt->execute([
                'name' => $association->getName(),
                'slug' => $association->getSlug(),
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

            $idAssociation = $this->DB->lastInsertId();
            $association->setIdAssociation($idAssociation);

            // Declare the creator as admin in user_association table
            $query = "INSERT INTO user_association (idUser, idAssociation, role, isActive, joinedAt) 
                      VALUES (:idUser, :idAssociation, 'admin', 1, :joinedAt)";
            $stmt = $this->DB->prepare($query);
            $stmt->execute([
                'idUser' => $association->getIdUser(),
                'idAssociation' => $idAssociation,
                'joinedAt' => $association->getCreatedAt()
            ]);

            $this->DB->commit();

            return $association;
        } catch (Exception $e) {
            $this->DB->rollback();
            throw new Exception("Error creating association: " . $e->getMessage());
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
                     slug = :slug,
                     description = :description,
                     address = :address, 
                     phone = :phone, 
                     email = :email, 
                     website = :website, 
                     isActive = :isActive,
                     idVille = :idVille,
                     logoPath = :logoPath,
                     bannerPath = :bannerPath,
                     updatedAt = :updatedAt
                     WHERE idAssociation = :idAssociation";

            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'name' => $association->getName(),
                'slug' => $association->getSlug(),
                'description' => $association->getDescription(),
                'address' => $association->getAddress(),
                'phone' => $association->getPhone(),
                'email' => $association->getEmail(),
                'website' => $association->getWebsite(),
                'isActive' => $association->getIsActive(),
                'idVille' => $association->getIdVille(),
                'logoPath' => $association->getLogoPath(),
                'bannerPath' => $association->getBannerPath(),
                'updatedAt' => $association->getUpdatedAt(),
                'idAssociation' => $association->getIdAssociation()
            ]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error updating association: " . $e->getMessage());
        }
    }
    //  make  association isDeleted  true 

    public function deleteAssociation($idAssociation, $idUser): bool
    {
        try {
            $query = "UPDATE association SET isDeleted = 1 WHERE idAssociation = :idAssociation AND idUser = :idUser";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idAssociation' => $idAssociation, 'idUser' => $idUser]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Error deleting association definitively: " . $e->getMessage());
        }
    }

    /**
     * Delete an association definitively
     */
    public function deleteAssociationDefinitive($idAssociation): bool
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

    /**
     * Get ville information by ID
     */
    public function getVilleById($idVille): ?array
    {
        try {
            $query = "SELECT idVille, ville_nom_reel, ville_code_postal FROM ville WHERE idVille = :idVille";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idVille' => $idVille]);

            $ville = $stmt->fetch(PDO::FETCH_ASSOC);
            return $ville !== false ? $ville : null;
        } catch (Exception $e) {
            throw new Exception("Error fetching ville: " . $e->getMessage());
        }
    }

    /**
     * Get association members with their roles
     */
    public function getAssociationMembers($idAssociation): array
    {
        try {
            $query = "SELECT u.idUser, u.firstName, u.lastName, u.email, ua.role, ua.joinedAt, ua.isActive
                      FROM user_association ua 
                      JOIN user u ON ua.idUser = u.idUser 
                      WHERE ua.idAssociation = :idAssociation AND ua.isActive = 1
                      ORDER BY ua.role DESC, u.lastName ASC";

            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idAssociation' => $idAssociation]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching association members: " . $e->getMessage());
        }
    }

    /**
     * Find association by ID (alias for getAssociationById for consistency)
     */
    public function findAssociationById($idAssociation): ?Association
    {
        return $this->getAssociationById($idAssociation);
    }
}
