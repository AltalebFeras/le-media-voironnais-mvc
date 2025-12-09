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
    public function getIdAssociationByUiid($uiid): int|null
    {
        try {
            $query = "SELECT idAssociation FROM association WHERE uiid = :uiid";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['uiid' => $uiid]);
            $idAssociation = $stmt->fetchColumn();
            return $idAssociation !== false ? (int)$idAssociation : null;
        } catch (Exception $e) {
            throw new Exception("Error fetching association ID by UIID: " . $e->getMessage());
        }
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
                      WHERE (a.idUser = :idUser AND a.isDeleted = 0) OR (ua.idUser = :idUser AND ua.isActive = 1 AND a.isDeleted = 0)
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
                      WHERE (a.idUser = :idUser AND a.isDeleted = 0) OR (ua.idUser = :idUser AND ua.isActive = 1 AND a.isDeleted = 0)";

            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idUser' => $idUser]);

            return (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error counting user associations: " . $e->getMessage());
        }
    }
    public function countAllActiveAssociations(): int
    {
        try {
            $query = "SELECT COUNT(*) FROM association WHERE isActive = 1 AND isDeleted = 0";
            $stmt = $this->DB->prepare($query);
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error counting active associations: " . $e->getMessage());
        }
    }
    public function getAllActiveAssociations($offset, $itemsPerPage): array
    {
        try {
            $query = "SELECT a.uiid, a.name, a.slug, a.logoPath, a.bannerPath, v.ville_nom_reel, v.ville_slug
                      FROM association a
                      LEFT JOIN ville v ON v.idVille = a.idVille
                      WHERE a.isActive = 1 AND a.isDeleted = 0
                      ORDER BY a.name ASC
                      LIMIT :offset, :itemsPerPage";
            $stmt = $this->DB->prepare($query);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
            $stmt->execute();
            $associations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $associations;
        } catch (Exception $e) {
            throw new Exception("Error fetching active associations: " . $e->getMessage());
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
public function getAssociationBySlug($slug)
    {
        try {
            $query = "SELECT a.*, v.ville_nom_reel, v.ville_slug, u.firstName AS creator_firstName, u.lastName AS creator_lastName, u.slug AS creator_slug
                      FROM association a
                      LEFT JOIN ville v ON v.idVille = a.idVille
                      LEFT JOIN user u ON u.idUser = a.idUser
                      WHERE a.slug = :slug AND a.isDeleted = 0";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['slug' => $slug]);
            $association = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($association) {
                // Fetch members
                $query = "SELECT u.idUser, u.firstName, u.lastName, u.slug AS user_slug, ua.role, u.avatarPath
                          FROM user u
                          INNER JOIN user_association ua ON u.idUser = ua.idUser
                          WHERE ua.idAssociation = :idAssociation AND ua.isActive = 1 AND u.isDeleted = 0";
                $stmt = $this->DB->prepare($query);
                $stmt->execute(['idAssociation' => $association['idAssociation']]);
                $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $association['members'] = $members;

                // Fetch events
                $query = "SELECT title, slug, bannerPath, startDate FROM evenement 
                          WHERE idAssociation = :idAssociation AND isDeleted = 0 AND isPublic = 1 
                          ORDER BY startDate DESC LIMIT 10";
                $stmt = $this->DB->prepare($query);
                $stmt->execute(['idAssociation' => $association['idAssociation']]);
                $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $association['associationEvents'] = $events;

                return $association;
            }
            
            return null;
        } catch (Exception $e) {
            throw new Exception("Error fetching association by slug: " . $e->getMessage());
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
                         (uiid, name, slug, description, logoPath, bannerPath, address, phone, email, website, isActive, idUser, idVille, createdAt) 
                     VALUES 
                     (:uiid, :name, :slug, :description, :logoPath, :bannerPath, :address, :phone, :email, :website, :isActive, :idUser, :idVille, :createdAt)";

            $stmt = $this->DB->prepare($query);
            $stmt->execute([
                'uiid' => $association->getUiid(),
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

    public function isAssociationHasEvents($idAssociation): bool
    {
        try {
            $query = "SELECT COUNT(*) FROM evenement WHERE idAssociation = :idAssociation AND isDeleted = 0 AND startDate >= DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idAssociation' => $idAssociation]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            throw new Exception("Error checking if association has events: " . $e->getMessage());
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
                     idVille = :idVille,
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
                'idVille' => $association->getIdVille(),
                'updatedAt' => $association->getUpdatedAt(),
                'idAssociation' => $association->getIdAssociation()
            ]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error updating association: " . $e->getMessage());
        }
    }
    //  update  association banner 
    public function updateAssociationBanner(Association $association): bool
    {
        try {
            $query = "UPDATE association SET 
                     bannerPath = :bannerPath,
                     updatedAt = :updatedAt
                     WHERE idAssociation = :idAssociation";

            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'bannerPath' => $association->getBannerPath(),
                'updatedAt' => $association->getUpdatedAt(),
                'idAssociation' => $association->getIdAssociation()
            ]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error updating association: " . $e->getMessage());
        }
    }

    public function updateAssociationLogo(Association $association): bool
    {
        try {
            $query = "UPDATE association SET 
                     logoPath = :logoPath,
                     updatedAt = :updatedAt
                     WHERE idAssociation = :idAssociation";

            $stmt = $this->DB->prepare($query);
            $result = $stmt->execute([
                'logoPath' => $association->getLogoPath(),
                'updatedAt' => $association->getUpdatedAt(),
                'idAssociation' => $association->getIdAssociation()
            ]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error updating association: " . $e->getMessage());
        }
    }
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
     * Get association members with their roles
     */
    public function getAssociationMembers($idAssociation): array
    {
        try {
            $query = "SELECT u.idUser, u.firstName, u.lastName, u.email, u.avatarPath, u.slug, ua.role, ua.joinedAt, ua.isActive
                      FROM user_association ua 
                      JOIN user u ON ua.idUser = u.idUser 
                      WHERE ua.idAssociation = :idAssociation AND ua.isActive = 1 AND u.isDeleted = 0
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
