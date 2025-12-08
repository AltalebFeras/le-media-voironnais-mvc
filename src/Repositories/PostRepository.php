<?php

namespace src\Repositories;

use src\Models\Post;
use src\Services\Database;

class PostRepository
{
    private $DB;

    public function __construct()
    {
        $db = Database::getInstance();
        $this->DB = $db->getDB();
    }

    public function createPost(Post $post): bool
    {
        $sql = "INSERT INTO post (uiid, title, content, imagePath, idUser, idAssociation, idEntreprise, authorType, isPublished, createdAt) 
                VALUES (:uiid, :title, :content, :imagePath, :idUser, :idAssociation, :idEntreprise, :authorType, :isPublished, :createdAt)";
        
        $stmt = $this->DB->prepare($sql);
        return $stmt->execute([
            'uiid' => $post->getUiid(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'imagePath' => $post->getImagePath(),
            'idUser' => $post->getIdUser(),
            'idAssociation' => $post->getIdAssociation(),
            'idEntreprise' => $post->getIdEntreprise(),
            'authorType' => $post->getAuthorType(),
            'isPublished' => $post->getIsPublished(),
            'createdAt' => $post->getCreatedAt()
        ]);
    }

    public function getAllPublicPosts(int $page = 1, int $limit = 12, ?string $filter = null): array
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT p.*, 
                u.firstName as user_firstName, u.lastName as user_lastName, u.avatarPath as user_avatar, u.slug as user_slug,
                a.name as association_name, a.logoPath as association_logo, a.slug as association_slug,
                e.name as entreprise_name, e.logoPath as entreprise_logo, e.slug as entreprise_slug
                FROM post p
                LEFT JOIN user u ON p.idUser = u.idUser
                LEFT JOIN association a ON p.idAssociation = a.idAssociation
                LEFT JOIN entreprise e ON p.idEntreprise = e.idEntreprise
                WHERE p.isPublished = 1";
        
        if ($filter && in_array($filter, ['user', 'association', 'entreprise'])) {
            $sql .= " AND p.authorType = :filter";
        }
        
        $sql .= " ORDER BY p.createdAt DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->DB->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        
        if ($filter) {
            $stmt->bindValue(':filter', $filter);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countPublicPosts(?string $filter = null): int
    {
        $sql = "SELECT COUNT(*) FROM post WHERE isPublished = 1";
        
        if ($filter && in_array($filter, ['user', 'association', 'entreprise'])) {
            $sql .= " AND authorType = :filter";
        }
        
        $stmt = $this->DB->prepare($sql);
        
        if ($filter) {
            $stmt->bindValue(':filter', $filter);
        }
        
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getPostByUiid(string $uiid): ?array
    {
        $sql = "SELECT p.*, 
                u.firstName as user_firstName, u.lastName as user_lastName, u.avatarPath as user_avatar, u.slug as user_slug,
                a.name as association_name, a.logoPath as association_logo, a.slug as association_slug,
                e.name as entreprise_name, e.logoPath as entreprise_logo, e.slug as entreprise_slug
                FROM post p
                LEFT JOIN user u ON p.idUser = u.idUser
                LEFT JOIN association a ON p.idAssociation = a.idAssociation
                LEFT JOIN entreprise e ON p.idEntreprise = e.idEntreprise
                WHERE p.uiid = :uiid";
        
        $stmt = $this->DB->prepare($sql);
        $stmt->execute(['uiid' => $uiid]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    public function getUserPosts(int $idUser, int $page = 1, int $limit = 12): array
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT p.*, 
                a.name as association_name,
                e.name as entreprise_name
                FROM post p
                LEFT JOIN association a ON p.idAssociation = a.idAssociation
                LEFT JOIN entreprise e ON p.idEntreprise = e.idEntreprise
                WHERE p.idUser = :idUser 
                ORDER BY p.createdAt DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->DB->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countUserPosts(int $idUser): int
    {
        $sql = "SELECT COUNT(*) FROM post WHERE idUser = :idUser";
        $stmt = $this->DB->prepare($sql);
        $stmt->execute(['idUser' => $idUser]);
        return (int)$stmt->fetchColumn();
    }

    public function updatePost(Post $post): bool
    {
        $sql = "UPDATE post SET 
                title = :title, 
                content = :content, 
                imagePath = :imagePath, 
                isPublished = :isPublished, 
                idAssociation = :idAssociation,
                idEntreprise = :idEntreprise,
                authorType = :authorType,
                updatedAt = :updatedAt 
                WHERE idPost = :idPost";
        
        $stmt = $this->DB->prepare($sql);
        return $stmt->execute([
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'imagePath' => $post->getImagePath(),
            'isPublished' => $post->getIsPublished(),
            'idAssociation' => $post->getIdAssociation(),
            'idEntreprise' => $post->getIdEntreprise(),
            'authorType' => $post->getAuthorType(),
            'updatedAt' => $post->getUpdatedAt(),
            'idPost' => $post->getIdPost()
        ]);
    }

    public function deletePost(int $idPost): bool
    {
        $sql = "DELETE FROM post WHERE idPost = :idPost";
        $stmt = $this->DB->prepare($sql);
        return $stmt->execute(['idPost' => $idPost]);
    }

    public function getIdPostByUiid(string $uiid): ?int
    {
        $sql = "SELECT idPost FROM posts WHERE uiid = :uiid";
        $stmt = $this->DB->prepare($sql);
        $stmt->execute(['uiid' => $uiid]);
        $result = $stmt->fetchColumn();
        
        return $result ? (int)$result : null;
    }

    public function isTitleExistsForUser(string $title, int $idUser, ?int $excludeIdPost = null): bool
    {
        $sql = "SELECT COUNT(*) FROM post WHERE title = :title AND idUser = :idUser";
        
        if ($excludeIdPost) {
            $sql .= " AND idPost != :excludeIdPost";
        }
        
        $stmt = $this->DB->prepare($sql);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':idUser', $idUser, \PDO::PARAM_INT);
        
        if ($excludeIdPost) {
            $stmt->bindValue(':excludeIdPost', $excludeIdPost, \PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }
}
