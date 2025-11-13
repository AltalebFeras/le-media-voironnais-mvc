<?php

namespace src\Repositories;

use Exception;
use PDO;
use src\Models\Realisation;
use src\Services\Database;

class RealisationRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getDB();
    }

    public function getEntrepriseRealisations(int $idEntreprise, int $currentPage = 1, int $realisationsPerPage = 9): array
    {
        $offset = ($currentPage - 1) * $realisationsPerPage;

        $sql = "SELECT * FROM realisation 
                WHERE idEntreprise = :idEntreprise AND isDeleted = 0 
                ORDER BY isFeatured DESC, createdAt DESC 
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idEntreprise', $idEntreprise, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $realisationsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $realisations = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $realisation = new Realisation();
            $realisation->setIdRealisation($row['idRealisation'])
                ->setUiid($row['uiid'])
                ->setTitle($row['title'])
                ->setSlug($row['slug'])
                ->setDescription($row['description'])
                ->setDateRealized($row['dateRealized'])
                ->setIsPublic((bool)$row['isPublic'])
                ->setIsFeatured((bool)$row['isFeatured'])
                ->setIsDeleted((bool)$row['isDeleted'])
                ->setIdEntreprise($row['idEntreprise'])
                ->setCreatedAt($row['createdAt']);

            if ($row['updatedAt']) {
                $realisation->setUpdatedAt($row['updatedAt']);
            }

            $realisations[] = $realisation;
        }

        return $realisations;
    }

    public function countEntrepriseRealisations(int $idEntreprise): int
    {
        $sql = "SELECT COUNT(*) FROM realisation WHERE idEntreprise = :idEntreprise AND isDeleted = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idEntreprise', $idEntreprise, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getRealisationById(int $idRealisation): ?Realisation
    {
        $sql = "SELECT * FROM realisation WHERE idRealisation = :idRealisation AND isDeleted = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idRealisation', $idRealisation, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        $realisation = new Realisation();
        $realisation->setIdRealisation($row['idRealisation'])
            ->setUiid($row['uiid'])
            ->setTitle($row['title'])
            ->setSlug($row['slug'])
            ->setDescription($row['description'])
            ->setDateRealized($row['dateRealized'])
            ->setIsPublic((bool)$row['isPublic'])
            ->setIsFeatured((bool)$row['isFeatured'])
            ->setIsDeleted((bool)$row['isDeleted'])
            ->setIdEntreprise($row['idEntreprise'])
            ->setCreatedAt($row['createdAt']);

        if ($row['updatedAt']) {
            $realisation->setUpdatedAt($row['updatedAt']);
        }

        return $realisation;
    }

    public function getIdRealisationByUiid(string $uiid): ?int
    {
        $sql = "SELECT idRealisation FROM realisation WHERE uiid = :uiid AND isDeleted = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':uiid', $uiid, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetchColumn();
        return $result !== false ? (int) $result : null;
    }

    public function createRealisation(Realisation $realisation): bool
    {
        $sql = "INSERT INTO realisation (uiid, title, slug, description, dateRealized, isPublic, isFeatured, idEntreprise, createdAt) 
                VALUES (:uiid, :title, :slug, :description, :dateRealized, :isPublic, :isFeatured, :idEntreprise, :createdAt)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':uiid' => $realisation->getUiid(),
            ':title' => $realisation->getTitle(),
            ':slug' => $realisation->getSlug(),
            ':description' => $realisation->getDescription(),
            ':dateRealized' => $realisation->getDateRealized(),
            ':isPublic' => $realisation->getIsPublic() ? 1 : 0,
            ':isFeatured' => $realisation->getIsFeatured() ? 1 : 0,
            ':idEntreprise' => $realisation->getIdEntreprise(),
            ':createdAt' => $realisation->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    public function updateRealisation(Realisation $realisation): bool
    {
        $sql = "UPDATE realisation SET title = :title, slug = :slug, description = :description, 
                dateRealized = :dateRealized, isPublic = :isPublic, isFeatured = :isFeatured, 
                updatedAt = :updatedAt 
                WHERE idRealisation = :idRealisation";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':title' => $realisation->getTitle(),
            ':slug' => $realisation->getSlug(),
            ':description' => $realisation->getDescription(),
            ':dateRealized' => $realisation->getDateRealized(),
            ':isPublic' => $realisation->getIsPublic() ? 1 : 0,
            ':isFeatured' => $realisation->getIsFeatured() ? 1 : 0,
            ':updatedAt' => $realisation->getUpdatedAt()->format('Y-m-d H:i:s'),
            ':idRealisation' => $realisation->getIdRealisation()
        ]);
    }

    public function deleteRealisation(int $idRealisation): bool
    {
        $sql = "UPDATE realisation SET isDeleted = 1, updatedAt = NOW() WHERE idRealisation = :idRealisation";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':idRealisation' => $idRealisation]);
    }

    public function isSlugExists(string $slug): bool
    {
        $sql = "SELECT COUNT(*) FROM realisation WHERE slug = :slug AND isDeleted = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function isTitleExistsForEntreprise(string $title, int $idEntreprise, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM realisation WHERE title = :title AND idEntreprise = :idEntreprise AND isDeleted = 0";
        $params = [':title' => $title, ':idEntreprise' => $idEntreprise];

        if ($excludeId) {
            $sql .= " AND idRealisation != :excludeId";
            $params[':excludeId'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    // Realisation Images
    public function getRealisationImages(int $idRealisation): array
    {
        $sql = "SELECT ri.idRealisationImage, ri.idRealisation, ri.uiid as realisation_image_uiid, ri.imagePath, ri.altText, ri.sortOrder FROM realisation_image ri JOIN realisation r ON ri.idRealisation = r.idRealisation WHERE ri.idRealisation = :idRealisation AND r.isDeleted = 0 ORDER BY ri.sortOrder ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idRealisation', $idRealisation, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addRealisationImage(string $uiid, int $idRealisation, string $imagePath, string $altText, int $sortOrder): bool
    {
        $sql = "INSERT INTO realisation_image (uiid,idRealisation, imagePath, altText, sortOrder) 
                VALUES (:uiid, :idRealisation, :imagePath, :altText, :sortOrder)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':uiid' => $uiid,
            ':idRealisation' => $idRealisation,
            ':imagePath' => $imagePath,
            ':altText' => $altText,
            ':sortOrder' => $sortOrder
        ]);
    }

    public function getMaxImageSortOrder(int $idRealisation): int
    {
        $sql = "SELECT COALESCE(MAX(sortOrder), 0) FROM realisation_image WHERE idRealisation = :idRealisation";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idRealisation', $idRealisation, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getRealisationImageByUiid(string $realisationImageUiid): ?array
    {
        $sql = "SELECT * FROM realisation_image WHERE uiid = :uiid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':uiid', $realisationImageUiid, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function deleteRealisationImage(string $realisationImageUiid): bool
    {
        $sql = "DELETE FROM realisation_image WHERE uiid = :uiid";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':uiid' => $realisationImageUiid]);
    }
}
