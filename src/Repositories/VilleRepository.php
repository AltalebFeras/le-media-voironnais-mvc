<?php

namespace src\Repositories;

use Exception;
use PDO;
use PDOException;
use src\Services\Database;

class VilleRepository
{
    private $DB;

    public function __construct()
    {
        $this->DB = Database::getInstance()->getDB();
    }

    /**
     * Get ville by ID
     */
    public function getCompleteVilleById(int $idVille): ?array
    {
        $sql = "SELECT * FROM ville WHERE idVille = :idVille";
        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':idVille', $idVille, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get ville information by ID
     */
    public function getVilleById($idVille): ?array
    {
        try {
            $query = "SELECT idVille, ville_slug, ville_nom_reel, ville_code_postal FROM ville WHERE idVille = :idVille";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idVille' => $idVille]);

            $ville = $stmt->fetch(PDO::FETCH_ASSOC);
            return $ville !== false ? $ville : null;
        } catch (Exception $e) {
            throw new Exception("Error fetching ville: " . $e->getMessage());
        }
    }

    /**
     * Summary of getVillesByCp
     * @param mixed $cp
     * @throws \Exception
     * @return array
     */
    public function getVillesByCp($cp)
    {
        try {
            $query = "SELECT idVille, ville_slug, ville_nom_reel, ville_code_postal FROM ville WHERE ville_code_postal = :cp";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['cp' => $cp]);

            $villes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $villes;
        } catch (Exception $e) {
            throw new Exception("Error fetching villes by CP: " . $e->getMessage());
        }
    }
    /**
     * Check if ville exists
     */
    public function isVilleExists($idVille): mixed
    {
        try {
            $query = "SELECT ville_slug FROM ville WHERE idVille = :idVille";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['idVille' => $idVille]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error checking if ville exists: " . $e->getMessage());
        }
    }
    public function getCityBySlug($villeSlug)
    {
        try {
            $query = "SELECT idVille, ville_slug, ville_nom_reel, ville_code_postal FROM ville WHERE ville_slug = :villeSlug";
            $stmt = $this->DB->prepare($query);
            $stmt->execute(['villeSlug' => $villeSlug]);

            $ville = $stmt->fetch(PDO::FETCH_ASSOC);
            return $ville !== false ? $ville : null;
        } catch (PDOException $e) {
            throw new Exception("Error fetching ville by slug: " . $e->getMessage());
        }
    }
}
