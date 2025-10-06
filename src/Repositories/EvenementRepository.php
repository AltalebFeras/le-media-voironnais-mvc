<?php

namespace src\Repositories;

use Exception;
use PDO;
use src\Models\Evenement;
use src\Repositories\DatabaseConnection;
use src\Services\Database;

class EvenementRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getDB();
    }
    /**
     * Get events by user ID with pagination
     */
    public function getUserEvents(int $idUser, $currentPage = 1, $evenementsPerPage = 10): array
    {
        $offset = max(0, ($currentPage - 1) * $evenementsPerPage);
        $sql = "SELECT e.*, v.ville_nom_reel, ec.name as category_name, a.name as association_name 
                FROM evenement e 
                LEFT JOIN ville v ON e.idVille = v.idVille 
                LEFT JOIN event_category ec ON e.idEventCategory = ec.idEventCategory
                LEFT JOIN association a ON e.idAssociation = a.idAssociation
                WHERE e.idUser = :idUser AND e.isDeleted = 0 
                ORDER BY e.startDate ASC
                LIMIT :offset, :evenementsPerPage";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':evenementsPerPage', $evenementsPerPage, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count events by user ID
     */
    public function countUserEvents(int $idUser): int
    {
        $sql = "SELECT COUNT(*) FROM evenement WHERE idUser = :idUser AND isDeleted = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }
    public function getIdByUiid($uiid)
    {
        $sql = "SELECT idEvenement FROM evenement WHERE uiid = :uiid AND isDeleted = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':uiid', $uiid, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Get event by ID
     */
    public function getEventCompleteById(int $idEvenement): mixed
    {
        // Get basic event data with category and ville
        $sql = "SELECT e.*, v.ville_nom_reel, v.ville_slug, ec.name as category_name, ec.slug as category_slug, a.name as association_name, a.slug as association_slug, ent.name as entreprise_name, ent.slug as entreprise_slug
                FROM evenement e 
                LEFT JOIN ville v ON e.idVille = v.idVille 
                LEFT JOIN event_category ec ON e.idEventCategory = ec.idEventCategory
                LEFT JOIN association a ON e.idAssociation = a.idAssociation
                LEFT JOIN entreprise ent ON e.idEntreprise = ent.idEntreprise
                WHERE e.idEvenement = :idEvenement AND e.isDeleted = 0";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $stmt->execute();

        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            return null;
        }

        // Get event images
        $sqlImages = "SELECT * FROM event_image WHERE idEvenement = :idEvenement ORDER BY sortOrder ASC, isMain DESC";
        $stmtImages = $this->pdo->prepare($sqlImages);
        $stmtImages->bindParam(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $stmtImages->execute();
        $event['images'] = $stmtImages->fetchAll(PDO::FETCH_ASSOC);

        // Get event participants with user details
        $sqlParticipants = "SELECT ep.*, u.firstName, u.lastName, u.email, u.avatarPath 
                           FROM event_participant ep 
                           LEFT JOIN user u ON ep.idUser = u.idUser
                           WHERE ep.idEvenement = :idEvenement 
                           ORDER BY ep.joinedAt DESC";
        $stmtParticipants = $this->pdo->prepare($sqlParticipants);
        $stmtParticipants->bindParam(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $stmtParticipants->execute();
        $event['participants'] = $stmtParticipants->fetchAll(PDO::FETCH_ASSOC);

        // Get event invitations with user details
        $sqlInvitations = "SELECT ei.*, u.firstName, u.lastName, u.email, u.avatarPath, 
                                 inviter.firstName as inviter_firstName, inviter.lastName as inviter_lastName
                          FROM event_invitation ei 
                          LEFT JOIN user u ON ei.idUser = u.idUser
                          LEFT JOIN user inviter ON ei.idInviter = inviter.idUser
                          WHERE ei.idEvenement = :idEvenement 
                          ORDER BY ei.invitedAt DESC";
        $stmtInvitations = $this->pdo->prepare($sqlInvitations);
        $stmtInvitations->bindParam(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $stmtInvitations->execute();
        $event['invitations'] = $stmtInvitations->fetchAll(PDO::FETCH_ASSOC);

        return $event;
    }
    public function isSlugExists($slug): bool
    {
        $sql = "SELECT COUNT(*) FROM evenement WHERE slug = :slug";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }
    /**
     * Create new event
     */
    public function createEvent(Evenement $event): bool
    {
        $sql = "INSERT INTO evenement (uiid, title, slug, description, shortDescription, startDate, endDate, 
                registrationDeadline, maxParticipants, currentParticipants, address, bannerPath, isPublic, isDeleted, price, currency, createdAt, idUser, idAssociation, idVille, idEventCategory) 
                VALUES (:uiid, :title, :slug, :description, :shortDescription, :startDate, :endDate, 
                :registrationDeadline, :maxParticipants, :currentParticipants, :address, :bannerPath, :isPublic, :isDeleted, :price, :currency, :createdAt, :idUser, :idAssociation, :idVille, :idEventCategory)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':uiid' => $event->getUiid(),
            ':title' => $event->getTitle(),
            ':slug' => $event->getSlug(),
            ':description' => $event->getDescription(),
            ':shortDescription' => $event->getShortDescription(),
            ':startDate' => $event->getStartDate(),
            ':endDate' => $event->getEndDate(),
            ':registrationDeadline' => $event->getRegistrationDeadline(),
            ':maxParticipants' => $event->getMaxParticipants(),
            ':currentParticipants' => $event->getCurrentParticipants() ?? 0,
            ':address' => $event->getAddress(),
            ':bannerPath' => $event->getBannerPath(),
            ':isPublic' => $event->getIsPublic() ? 1 : 0,
            ':isDeleted' => 0,
            ':price' => $event->getPrice(),
            ':currency' => $event->getCurrency() ?? 'EUR',
            ':createdAt' => $event->getCreatedAt(),
            ':idUser' => $event->getIdUser(),
            ':idAssociation' => $event->getIdAssociation(),
            ':idVille' => $event->getIdVille(),
            ':idEventCategory' => $event->getIdEventCategory()
        ]);
    }

    /**
     * Update event
     */
    public function updateEvent(Evenement $event): bool
    {
        $sql = "UPDATE evenement SET 
                title = :title, slug = :slug, description = :description, shortDescription = :shortDescription,
                startDate = :startDate, endDate = :endDate, registrationDeadline = :registrationDeadline,
                maxParticipants = :maxParticipants, address = :address, isPublic = :isPublic, requiresApproval = :requiresApproval,
                price = :price, currency = :currency, updatedAt = :updatedAt, idVille = :idVille,
                idEventCategory = :idEventCategory, idAssociation = :idAssociation
                WHERE idEvenement = :idEvenement AND idUser = :idUser";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':title' => $event->getTitle(),
            ':slug' => $event->getSlug(),
            ':description' => $event->getDescription(),
            ':shortDescription' => $event->getShortDescription(),
            ':startDate' => $event->getStartDate(),
            ':endDate' => $event->getEndDate(),
            ':registrationDeadline' => $event->getRegistrationDeadline(),
            ':maxParticipants' => $event->getMaxParticipants(),
            ':address' => $event->getAddress(),
            ':isPublic' => $event->getIsPublic() ? 1 : 0,
            ':requiresApproval' => $event->getRequiresApproval() ? 1 : 0,
            ':price' => $event->getPrice(),
            ':currency' => $event->getCurrency(),
            ':updatedAt' => $event->getUpdatedAt(),
            ':idVille' => $event->getIdVille(),
            ':idEventCategory' => $event->getIdEventCategory(),
            ':idAssociation' => $event->getIdAssociation(),
            ':idEvenement' => $event->getIdEvenement(),
            ':idUser' => $event->getIdUser()
        ]);
    }

    /**
     * Delete event (soft delete)
     */
    public function deleteEvent(int $idEvenement): bool
    {
        $sql = "UPDATE evenement SET isDeleted = 1 WHERE idEvenement = :idEvenement";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idEvenement', $idEvenement, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Get all event categories
     */
    public function getEventCategories(): array
    {
        $sql = "SELECT * FROM event_category WHERE isActive = 1 ORDER BY name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get user associations for event creation
     */
    public function getUserAssociations(int $idUser): array
    {
        $sql = "SELECT a.idAssociation, a.name FROM association a 
                WHERE a.idUser = :idUser AND a.isActive = 1 AND a.isDeleted = 0
                ORDER BY a.name ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserEntreprise($idUser): array
    {
        $sql = "SELECT e.idEntreprise, e.name FROM entreprise e 
                WHERE e.idUser = :idUser  AND e.isDeleted = 0 AND e.isActive = 1
                ORDER BY e.name ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function isEntrepriseActiveAndPublic($idEntreprise): bool
    {
        $sql = "SELECT COUNT(*) FROM entreprise WHERE idEntreprise = :idEntreprise AND isActive = 1 AND isDeleted = 0 AND isPublic = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idEntreprise', $idEntreprise, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }
    /**
     * Get villes by postal code
     */
    public function getVillesByCp(string $codePostal): array
    {
        $sql = "SELECT idVille, ville_nom_reel FROM ville WHERE ville_code_postal = :codePostal ORDER BY ville_nom_reel ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':codePostal', $codePostal, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if ville exists
     */
    public function isVilleExists($idVille): mixed
    {
        try {
            $query = "SELECT ville_slug FROM ville WHERE idVille = :idVille";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['idVille' => $idVille]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error checking if ville exists: " . $e->getMessage());
        }
    }

    public function isEventCategoryExists(int $idEventCategory): mixed
    {
        try {
            $query = "SELECT name FROM event_category WHERE idEventCategory = :idEventCategory AND isActive = 1";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['idEventCategory' => $idEventCategory]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error checking if event category exists: " . $e->getMessage());
        }
    }

    /**
     * Get ville by ID
     */
    public function getVilleById(int $idVille): ?array
    {
        $sql = "SELECT * FROM ville WHERE idVille = :idVille";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idVille', $idVille, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    /**
     * Update event banner
     */
    public function updateEventBanner($idEvenement, $bannerPath): bool
    {
        try {
            $query = "UPDATE evenement SET bannerPath = :bannerPath, updatedAt = NOW() WHERE idEvenement = :idEvenement";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([
                'bannerPath' => $bannerPath,
                'idEvenement' => $idEvenement
            ]);
        } catch (Exception $e) {
            throw new Exception("Error updating event banner: " . $e->getMessage());
        }
    }

    public function addEventImage($idEvenement, $imagePath, $altText, $sortOrder): bool
    {
        try {
            $query = "INSERT INTO event_image (idEvenement, imagePath, altText, sortOrder, createdAt) 
                     VALUES (:idEvenement, :imagePath, :altText, :sortOrder, NOW())";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([
                'idEvenement' => $idEvenement,
                'imagePath' => $imagePath,
                'altText' => $altText,
                'sortOrder' => $sortOrder
            ]);
        } catch (Exception $e) {
            throw new Exception("Error adding event image: " . $e->getMessage());
        }
    }

    public function getEventImages($idEvenement): array
    {
        try {
            $query = "SELECT * FROM event_image WHERE idEvenement = :idEvenement ORDER BY sortOrder ASC, createdAt ASC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['idEvenement' => $idEvenement]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching event images: " . $e->getMessage());
        }
    }

    public function getEventImageById($idEventImage): ?array
    {
        try {
            $query = "SELECT * FROM event_image WHERE idEventImage = :idEventImage";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['idEventImage' => $idEventImage]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (Exception $e) {
            throw new Exception("Error fetching event image: " . $e->getMessage());
        }
    }

    public function deleteEventImage($idEventImage): bool
    {
        try {
            $query = "DELETE FROM event_image WHERE idEventImage = :idEventImage";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute(['idEventImage' => $idEventImage]);
        } catch (Exception $e) {
            throw new Exception("Error deleting event image: " . $e->getMessage());
        }
    }

    public function getMaxImageSortOrder($idEvenement): int
    {
        try {
            $query = "SELECT COALESCE(MAX(sortOrder), 0) FROM event_image WHERE idEvenement = :idEvenement";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['idEvenement' => $idEvenement]);
            return (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error getting max sort order: " . $e->getMessage());
        }
    }

    /**
     * Check if user is event owner
     */
    public function isEventOwner(int $idEvenement, int $idUser): bool
    {
        $sql = "SELECT COUNT(*) FROM evenement WHERE idEvenement = :idEvenement AND idUser = :idUser";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':idEvenement' => $idEvenement,
            ':idUser' => $idUser
        ]);

        return $stmt->fetchColumn() > 0;
    }
    // Check if title exists for user (to ensure uniqueness)(exclude current event)
    public function isTitleExistsForUser(string $title, int $idUser,  $idEvenement = null): bool
    {
        $sql = "SELECT COUNT(*) FROM evenement WHERE title = :title AND idUser = :idUser AND isDeleted = 0";
        if ($idEvenement) {
            $sql .= " AND idEvenement != :idEvenement";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        if ($idEvenement) {
            $stmt->bindParam(':idEvenement', $idEvenement, PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function getUpcomingEvents($limit = 3): array
    {
        $sql = "SELECT e.*, v.ville_nom_reel, v.ville_slug, ec.name as category_name, ec.slug as category_slug, a.name as association_name 
                FROM evenement e 
                LEFT JOIN ville v ON e.idVille = v.idVille 
                LEFT JOIN event_category ec ON e.idEventCategory = ec.idEventCategory
                LEFT JOIN association a ON e.idAssociation = a.idAssociation
                WHERE e.isDeleted = 0 AND e.isPublic = 1 AND e.startDate > NOW()
                ORDER BY e.startDate ASC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentEvents($limit = 4): array
    {
        $sql = "SELECT e.*, v.ville_nom_reel, v.ville_slug, ec.name as category_name, ec.slug as category_slug, a.name as association_name 
                FROM evenement e 
                LEFT JOIN ville v ON e.idVille = v.idVille 
                LEFT JOIN event_category ec ON e.idEventCategory = ec.idEventCategory
                LEFT JOIN association a ON e.idAssociation = a.idAssociation
                WHERE e.isDeleted = 0 AND e.isPublic = 1 AND e.endDate < NOW()
                ORDER BY e.endDate DESC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEvents($currentPage, $evenementsPerPage): array
    {
        $offset = max(0, ($currentPage - 1) * $evenementsPerPage);
        $sql = "SELECT e.*, v.ville_nom_reel, v.ville_slug, ec.name as category_name, ec.slug as category_slug, a.name as association_name
                FROM evenement e 
                LEFT JOIN ville v ON e.idVille = v.idVille 
                LEFT JOIN event_category ec ON e.idEventCategory = ec.idEventCategory
                LEFT JOIN association a ON e.idAssociation = a.idAssociation
                WHERE e.isDeleted = 0 AND e.isPublic = 1
                ORDER BY e.startDate DESC
                LIMIT :offset, :evenementsPerPage";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':evenementsPerPage', $evenementsPerPage, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function countEvents(): int
    {
        $sql = "SELECT COUNT(*) FROM evenement WHERE isDeleted = 0 AND isPublic = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }
    public function getEventBySlug($slug): mixed
    {
        $sql = "SELECT e.*, v.ville_nom_reel, v.ville_slug, ec.name as category_name, ec.slug as category_slug, a.name as association_name, ent.name as entreprise_name
                FROM evenement e 
                LEFT JOIN ville v ON e.idVille = v.idVille 
                LEFT JOIN event_category ec ON e.idEventCategory = ec.idEventCategory
                LEFT JOIN association a ON e.idAssociation = a.idAssociation
                LEFT JOIN entreprise ent ON e.idEntreprise = ent.idEntreprise
                WHERE e.slug = :slug AND e.isDeleted = 0 AND e.isPublic = 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();

        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            return null;
        }

        // Get event images
        $sqlImages = "SELECT * FROM event_image WHERE idEvenement = :idEvenement ORDER BY sortOrder ASC, isMain DESC";
        $stmtImages = $this->pdo->prepare($sqlImages);
        $stmtImages->bindParam(':idEvenement', $event['idEvenement'], PDO::PARAM_INT);
        $stmtImages->execute();
        $event['images'] = $stmtImages->fetchAll(PDO::FETCH_ASSOC);

        return $event;
    }
    public function getUserSubscription($idUser, $idEvenement)
    {
        $sql = "SELECT ep.*, e.title, e.slug, e.startDate, e.endDate 
                FROM event_participant ep
                JOIN evenement e ON ep.idEvenement = e.idEvenement
                WHERE ep.idUser = :idUser AND e.idEvenement = :idEvenement";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindParam(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $stmt->execute();

        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
        return $subscription ?: null;  
    }
    public function registerUserForEventAndIncrementEventParticipants($idUser, $idEvenement, $status): bool
    {
        try {
            // Start transaction
            $this->pdo->beginTransaction();

            // Insert participant
            $query = "INSERT INTO event_participant (idUser, idEvenement, status, joinedAt) 
                      VALUES (:idUser, :idEvenement, :status, NOW())";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'idUser' => $idUser,
                'idEvenement' => $idEvenement,
                'status' => $status
            ]);

            // Increment participant count if status is 'inscrit'
            $updateQuery = "UPDATE evenement SET currentParticipants = currentParticipants + 1 WHERE idEvenement = :idEvenement";
            $updateStmt = $this->pdo->prepare($updateQuery);
            $updateStmt->execute(['idEvenement' => $idEvenement]);

            // Commit transaction
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->pdo->rollBack();
            throw new Exception("Error registering user for event: " . $e->getMessage());
        }
    }
    public function isUserOnWaitingList($idUser, $slug): bool
    {
        $sql = "SELECT COUNT(*) 
                FROM event_participant ep
                JOIN evenement e ON ep.idEvenement = e.idEvenement
                WHERE ep.idUser = :idUser AND e.slug = :slug AND ep.status = 'liste_attente'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }
    public function getEventParticipantsUponStatus($idEvenement, $idUser, $status): array
    {
        try {
            $query = "SELECT ep.*, u.firstName, u.lastName, u.email, u.avatarPath 
                      FROM event_participant ep 
                      LEFT JOIN user u ON ep.idUser = u.idUser
                      WHERE ep.idEvenement = :idEvenement AND ep.status = :status
                      ORDER BY ep.joinedAt DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['idEvenement' => $idEvenement, 'status' => $status]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching event participants: " . $e->getMessage());
        }
    }
    public function getSubscriptionById($idEventParticipant): ?array
    {
        try {
            $query = "SELECT ep.*, u.firstName, u.lastName, u.email, u.avatarPath 
                      FROM event_participant ep
                      LEFT JOIN user u ON ep.idUser = u.idUser
                      WHERE ep.idEventParticipant = :idEventParticipant";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['idEventParticipant' => $idEventParticipant]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (Exception $e) {
            throw new Exception("Error fetching subscription: " . $e->getMessage());
        }
    }
    public function updateSubscriptionStatus($idEventParticipant, $idEvenement, $newStatus): bool
    {
        try {
            // Start transaction
            $this->pdo->beginTransaction();

            // Get current status
            $currentSubscription = $this->getSubscriptionById($idEventParticipant);
            if (!$currentSubscription) {
                throw new Exception("Subscription not found");
            }
            $currentStatus = $currentSubscription['status'];

            // Update subscription status
            $query = "UPDATE event_participant SET status = :newStatus WHERE idEventParticipant = :idEventParticipant";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'newStatus' => $newStatus,
                'idEventParticipant' => $idEventParticipant
            ]);
            if ($newStatus === 'annule') {
                // Decrement participant count
                $updateQuery = "UPDATE evenement SET currentParticipants = GREATEST(currentParticipants - 1, 0) WHERE idEvenement = :idEvenement";
                $updateStmt = $this->pdo->prepare($updateQuery);
                $updateStmt->execute(['idEvenement' => $idEvenement]);
            }

            // Commit transaction
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            throw new Exception("Error updating subscription status: " . $e->getMessage());
        }
    }

    // Save contact message
    public function saveContactMessage(array $data): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO contact (idUser, name, email, title, message, type, createdAt) VALUES (:idUser, :name, :email, :title, :message, :type, NOW())");
        return $stmt->execute([
            'idUser' => $data['idUser'],
            'name' => $data['name'],
            'email' => $data['email'],
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type']
        ]);
    }

    // Toggle event like
    public function toggleEventLike($idUser, $idEvenement): bool
    {
        $stmt = $this->pdo->prepare("SELECT idEventLike FROM event_like WHERE idUser = ? AND idEvenement = ?");
        $stmt->execute([$idUser, $idEvenement]);
        if ($stmt->fetch()) {
            $this->pdo->prepare("DELETE FROM event_like WHERE idUser = ? AND idEvenement = ?")->execute([$idUser, $idEvenement]);
            return false;
        } else {
            $this->pdo->prepare("INSERT INTO event_like (idUser, idEvenement, createdAt) VALUES (?, ?, NOW())")->execute([$idUser, $idEvenement]);
            return true;
        }
    }

    // Toggle event favourite
    public function toggleEventFavourite($idUser, $idEvenement): bool
    {
        $stmt = $this->pdo->prepare("SELECT idEventFavourite FROM event_favourite WHERE idUser = ? AND idEvenement = ?");
        $stmt->execute([$idUser, $idEvenement]);
        if ($stmt->fetch()) {
            $this->pdo->prepare("DELETE FROM event_favourite WHERE idUser = ? AND idEvenement = ?")->execute([$idUser, $idEvenement]);
            return false;
        } else {
            $this->pdo->prepare("INSERT INTO event_favourite (idUser, idEvenement, createdAt) VALUES (?, ?, NOW())")->execute([$idUser, $idEvenement]);
            return true;
        }
    }

    // Add event comment
    public function addEventComment($idUser, $idEvenement, $content, $parentId = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO event_comment (idEvenement, idUser, content, parentId, createdAt) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$idEvenement, $idUser, $content, $parentId]);
        return $this->pdo->lastInsertId();
    }

    // Toggle like on comment
    public function toggleEventCommentLike($idUser, $idEventComment): bool
    {
        $stmt = $this->pdo->prepare("SELECT idEventCommentLike FROM event_comment_like WHERE idUser = ? AND idEventComment = ?");
        $stmt->execute([$idUser, $idEventComment]);
        if ($stmt->fetch()) {
            $this->pdo->prepare("DELETE FROM event_comment_like WHERE idUser = ? AND idEventComment = ?")->execute([$idUser, $idEventComment]);
            return false;
        } else {
            $this->pdo->prepare("INSERT INTO event_comment_like (idUser, idEventComment, createdAt) VALUES (?, ?, NOW())")->execute([$idUser, $idEventComment]);
            return true;
        }
    }

    // Report a comment
    public function reportEventComment($idUser, $idEventComment, $reason = null): bool
    {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO event_comment_report (idUser, idEventComment, reason, createdAt) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$idUser, $idEventComment, $reason]);
    }

    // Fetch comments for an event (with likes count, replies, etc.)
    public function getEventComments($idEvenement)
    {
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.firstName, u.lastName,
                (SELECT COUNT(*) FROM event_comment_like l WHERE l.idEventComment = c.idEventComment) as likesCount,
                (SELECT COUNT(*) FROM event_comment_report r WHERE r.idEventComment = c.idEventComment) as reportsCount
            FROM event_comment c
            JOIN user u ON u.idUser = c.idUser
            WHERE c.idEvenement = ? AND c.parentId IS NULL AND c.isDeleted = 0
            ORDER BY c.createdAt DESC
        ");
        $stmt->execute([$idEvenement]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Optionally, fetch replies for each comment
        return $comments;
    }

    // Get a single comment by id
    public function getEventCommentById($idEventComment)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM event_comment WHERE idEventComment = ?");
        $stmt->execute([$idEventComment]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Delete a comment and its replies (soft delete)
    public function deleteEventCommentWithReplies($idEventComment)
    {
        // Soft delete the comment
        $this->pdo->prepare("UPDATE event_comment SET isDeleted = 1 WHERE idEventComment = ?")->execute([$idEventComment]);
        // Soft delete all replies
        $this->pdo->prepare("UPDATE event_comment SET isDeleted = 1 WHERE parentId = ?")->execute([$idEventComment]);
    }

    // Count likes for an event
    public function countEventLikes($idEvenement): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM event_like WHERE idEvenement = ?");
        $stmt->execute([$idEvenement]);
        return (int)$stmt->fetchColumn();
    }

    // Count comments (not deleted) for an event (top-level only or all)
    public function countEventComments($idEvenement): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM event_comment WHERE idEvenement = ? AND isDeleted = 0");
        $stmt->execute([$idEvenement]);
        return (int)$stmt->fetchColumn();
    }

    // Check if user has liked the event
    public function hasUserLikedEvent($idUser, $idEvenement): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM event_like WHERE idUser = ? AND idEvenement = ?");
        $stmt->execute([$idUser, $idEvenement]);
        return (bool)$stmt->fetchColumn();
    }

    // Check if user has favourited the event
    public function hasUserFavouritedEvent($idUser, $idEvenement): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM event_favourite WHERE idUser = ? AND idEvenement = ?");
        $stmt->execute([$idUser, $idEvenement]);
        return (bool)$stmt->fetchColumn();
    }
}
