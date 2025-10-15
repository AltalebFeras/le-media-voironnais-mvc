<?php

namespace src\Repositories;

use Exception;
use PDO;
use PDOException;
use src\Models\Contact;
use src\Services\Database;

class ContactRepository
{
    private $DB;

    public function __construct()
    {
        $this->DB = Database::getInstance()->getDB();
    }

    /**
     * Create a new contact message
     */
    public function createContact(Contact $contact): bool
    {
        try {
            $sql = "INSERT INTO contact (firstName, lastName, email, phone, subject, message, status, createdAt, uiid) 
                    VALUES (:firstName, :lastName, :email, :phone, :subject, :message, :status, :createdAt, :uiid)";
            
            $stmt = $this->DB->prepare($sql);
            
            return $stmt->execute([
                ':firstName' => $contact->getFirstName(),
                ':lastName' => $contact->getLastName(),
                ':email' => $contact->getEmail(),
                ':phone' => $contact->getPhone(),
                ':subject' => $contact->getSubject(),
                ':message' => $contact->getMessage(),
                ':status' => $contact->getStatus(),
                ':createdAt' => $contact->getCreatedAt(),
                ':uiid' => $contact->getUiid()
            ]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la création du contact : " . $e->getMessage());
        }
    }

    /**
     * Get contact by ID
     */
    public function getContactById(int $idContact): ?Contact
    {
        try {
            $sql = "SELECT * FROM contact WHERE idContact = :idContact";
            $stmt = $this->DB->prepare($sql);
            $stmt->execute([':idContact' => $idContact]);
            
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$data) {
                return null;
            }
            
            return $this->hydrateContact($data);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération du contact : " . $e->getMessage());
        }
    }

    /**
     * Get contact by UIID
     */
    public function getContactByUiid(string $uiid): ?Contact
    {
        try {
            $sql = "SELECT * FROM contact WHERE uiid = :uiid";
            $stmt = $this->DB->prepare($sql);
            $stmt->execute([':uiid' => $uiid]);
            
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$data) {
                return null;
            }
            
            return $this->hydrateContact($data);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération du contact : " . $e->getMessage());
        }
    }

    /**
     * Get ID by UIID
     */
    public function getIdByUiid(?string $uiid): ?int
    {
        if (!$uiid) return null;
        
        try {
            $sql = "SELECT idContact FROM contact WHERE uiid = :uiid";
            $stmt = $this->DB->prepare($sql);
            $stmt->execute([':uiid' => $uiid]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['idContact'] : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get all contact with pagination
     */
    public function getAllContacts(int $page = 1, int $limit = 20, string $status = null): array
    {
        try {
            $offset = ($page - 1) * $limit;
            
            $sql = "SELECT * FROM contact";
            $params = [];
            
            if ($status) {
                $sql .= " WHERE status = :status";
                $params[':status'] = $status;
            }
            
            $sql .= " ORDER BY createdAt DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->DB->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $contacts = [];
            foreach ($data as $row) {
                $contacts[] = $this->hydrateContact($row);
            }
            
            return $contacts;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des contacts : " . $e->getMessage());
        }
    }

    /**
     * Count total contacts
     */
    public function countContacts(string $status = null): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM contact";
            $params = [];
            
            if ($status) {
                $sql .= " WHERE status = :status";
                $params[':status'] = $status;
            }

            $stmt = $this->DB->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (Exception $e) {
            throw new Exception("Erreur lors du comptage des contacts : " . $e->getMessage());
        }
    }

    /**
     * Update contact status
     */
    public function updateContactStatus(int $idContact, string $status): bool
    {
        try {
            $sql = "UPDATE contact SET status = :status, repliedAt = :repliedAt WHERE idContact = :idContact";
            $stmt = $this->DB->prepare($sql);
            
            return $stmt->execute([
                ':status' => $status,
                ':repliedAt' => (new \DateTime())->format('Y-m-d H:i:s'),
                ':idContact' => $idContact
            ]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la mise à jour du statut : " . $e->getMessage());
        }
    }

    /**
     * Add response to contact
     */
    public function addResponse(int $idContact, string $response): bool
    {
        try {
            $sql = "UPDATE contact SET response = :response, status = 'traite', repliedAt = :repliedAt WHERE idContact = :idContact";
            $stmt = $this->DB->prepare($sql);
            
            return $stmt->execute([
                ':response' => $response,
                ':repliedAt' => (new \DateTime())->format('Y-m-d H:i:s'),
                ':idContact' => $idContact
            ]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'ajout de la réponse : " . $e->getMessage());
        }
    }

    /**
     * Delete contact
     */
    public function deleteContact(int $idContact): bool
    {
        try {
            $sql = "DELETE FROM contact WHERE idContact = :idContact";
            $stmt = $this->DB->prepare($sql);

            return $stmt->execute([':idContact' => $idContact]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la suppression du contact : " . $e->getMessage());
        }
    }

    /**
     * Get contacts statistics
     */
    public function getContactsStats(): array
    {
        try {
            $sql = "SELECT 
                        status,
                        COUNT(*) as count,
                        DATE(createdAt) as date
                    FROM contact 
                    WHERE createdAt >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY status, DATE(createdAt)
                    ORDER BY date DESC";

            $stmt = $this->DB->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des statistiques : " . $e->getMessage());
        }
    }

    /**
     * Search contacts
     */
    public function searchContacts(string $query, int $page = 1, int $limit = 20): array
    {
        try {
            $offset = ($page - 1) * $limit;
            
            $sql = "SELECT * FROM contact 
                    WHERE firstName LIKE :query 
                    OR lastName LIKE :query 
                    OR email LIKE :query 
                    OR subject LIKE :query 
                    OR message LIKE :query
                    ORDER BY createdAt DESC 
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->DB->prepare($sql);
            $stmt->bindValue(':query', "%$query%");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $contacts = [];
            foreach ($data as $row) {
                $contacts[] = $this->hydrateContact($row);
            }
            
            return $contacts;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la recherche : " . $e->getMessage());
        }
    }

    /**
     * Hydrate Contact object from array data
     */
    private function hydrateContact(array $data): Contact
    {
        $contact = new Contact();
        
        return $contact->setIdContact($data['idContact'] ?? null)
                      ->setFirstName($data['firstName'] ?? null)
                      ->setLastName($data['lastName'] ?? null)
                      ->setEmail($data['email'] ?? null)
                      ->setPhone($data['phone'] ?? null)
                      ->setSubject($data['subject'] ?? null)
                      ->setMessage($data['message'] ?? null)
                      ->setStatus($data['status'] ?? 'nouveau')
                      ->setResponse($data['response'] ?? null)
                      ->setCreatedAt($data['createdAt'] ?? null)
                      ->setRepliedAt($data['repliedAt'] ?? null)
                      ->setUiid($data['uiid'] ?? null);
    }
}
