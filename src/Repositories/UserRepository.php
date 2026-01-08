<?php

namespace src\Repositories;

use Exception;
use PDO;
use PDOException;
use src\Models\User;
use src\Services\Database;

class UserRepository
{
    private $DBuser;

    public function __construct()
    {
        $this->DBuser = Database::getInstance()->getDB();
    }
    public function getUser($email): ?User
    {
        try {
            $query = 'SELECT u.*, r.name AS roleName FROM user u JOIN role r ON u.idRole = r.idRole WHERE u.email = :email AND u.isDeleted = 0';
            $req = $this->DBuser->prepare($query);
            $req->execute(['email' => $email]);
            $user = $req->fetchObject(User::class);
            return $user !== false ? $user : null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getUserByUiid($uiid): ?User
    {
        try {
            $query = 'SELECT * FROM user WHERE uiid = :uiid';
            $req = $this->DBuser->prepare($query);
            $req->execute(['uiid' => $uiid]);
            $user = $req->fetchObject(User::class);
            return $user !== false ? $user : null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getUserBySlug($slug): ?User
    {
        try {
            $query = 'SELECT * FROM user WHERE slug = :slug';
            $req = $this->DBuser->prepare($query);
            $req->execute(['slug' => $slug]);
            $user = $req->fetchObject(User::class);
            return $user !== false ? $user : null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getUserByToken($token): ?User
    {
        try {
            $query  = 'SELECT * FROM user WHERE token = :token';
            $req = $this->DBuser->prepare($query);
            $req->execute(['token' => $token]);
            $user = $req->fetchObject(User::class);
            return $user !== false ? $user : null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getUserById($idUser): ?User
    {
        try {
            $query = 'SELECT * FROM user WHERE idUser = :idUser';
            $req = $this->DBuser->prepare($query);
            $req->execute(['idUser' => $idUser]);
            $user = $req->fetchObject(User::class);
            return $user !== false ? $user : null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getUserByAuthCode($authCode): ?User
    {
        try {
            $query = 'SELECT * FROM user WHERE authCode = :authCode';
            $req = $this->DBuser->prepare($query);
            $req->execute(['authCode' => $authCode]);
            $user = $req->fetchObject(User::class);
            return $user !== false ? $user : null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getDeletedUserByEmail($email): ?User
    {
        try {
            $query = 'SELECT * FROM user WHERE email = :email AND isDeleted = 1';
            $req = $this->DBuser->prepare($query);
            $req->execute(['email' => $email]);
            $user = $req->fetchObject(User::class);
            return $user !== false ? $user : null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getUserAndHisAssociationsAndEventsAndHisEntreprisesBySlug($slug): array
    {
        try {
            $query = 'SELECT u.idUser, u.firstName, u.lastName, u.email, u.phone, u.avatarPath, u.bannerPath, u.bio, u.dateOfBirth, u.isActivated, u.isBanned, u.isDeleted, u.isOnline, u.lastSeen, u.createdAt, u.updatedAt, r.name AS roleName,
                      (SELECT COUNT(*) FROM association a WHERE a.idUser = u.idUser AND a.isActive = 1 AND a.isDeleted = 0) AS associationCount,
                      (SELECT COUNT(*) FROM evenement e WHERE e.idUser = u.idUser AND e.isPublic = 1 AND e.isDeleted = 0) AS eventCount,
                      (SELECT COUNT(*) FROM entreprise ent WHERE ent.idUser = u.idUser AND ent.isActive = 1 AND ent.isDeleted = 0) AS enterpriseCount,
                      (SELECT JSON_ARRAYAGG(
                          JSON_OBJECT(
                              "name", a.name,
                              "slug", a.slug,
                              "logoPath", a.logoPath,
                              "role", ua.role
                          )
                      ) FROM association a 
                      LEFT JOIN user_association ua ON a.idAssociation = ua.idAssociation
                      WHERE ua.idUser = u.idUser AND a.isActive = 1 AND a.isDeleted = 0 AND ua.isActive = 1
                      LIMIT 3) AS userAssociations,
                      (SELECT JSON_ARRAYAGG(
                          JSON_OBJECT(
                              "title", e.title,
                              "slug", e.slug,
                              "bannerPath", e.bannerPath,
                              "startDate", e.startDate
                          )
                      ) FROM evenement e 
                      WHERE e.idUser = u.idUser AND e.isPublic = 1 AND e.isDeleted = 0
                      ORDER BY e.startDate DESC
                      LIMIT 3) AS userEvents
                      FROM user u
                      JOIN role r ON u.idRole = r.idRole
                      WHERE u.slug = :slug AND u.isActivated = 1 AND u.isBanned = 0 AND u.isDeleted = 0';

            $req = $this->DBuser->prepare($query);
            $req->execute(['slug' => $slug]);
            $user = $req->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return [];
            }

            // Decode JSON arrays
            $user['userAssociations'] = $user['userAssociations'] ? json_decode($user['userAssociations'], true) : [];
            $user['userEvents'] = $user['userEvents'] ? json_decode($user['userEvents'], true) : [];

            return $user;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function isSlugExists($slug): bool
    {
        try {
            $query = 'SELECT COUNT(*) FROM user WHERE slug = :slug';
            $req = $this->DBuser->prepare($query);
            $req->execute(['slug' => $slug]);
            $count = $req->fetchColumn();
            return $count > 0;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function signUp(User $user): bool
    {
        try {
            $query = 'INSERT INTO user (uiid,slug, firstName, lastName, email, password, isActivated, isBanned, isDeleted, token, createdAt, idRole, rgpdAcceptedDate) 
            VALUES (:uiid, :slug, :firstName, :lastName, :email, :password, :isActivated, :isBanned, :isDeleted, :token, :createdAt, :idRole, :rgpdAcceptedDate)';

            $req = $this->DBuser->prepare($query);
            $req->execute([
                'uiid' => $user->getUiid(),
                'slug' => $user->getSlug(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'isActivated' => $user->getIsActivated(),
                'isBanned' => $user->getIsBanned(),
                'isDeleted' => $user->getIsDeleted(),
                'token' => $user->getToken(),
                'createdAt' => $user->getCreatedAt(),
                'rgpdAcceptedDate' => $user->getRgpdAcceptedDate(),
                'idRole' => $user->getIdRole()
            ]);
            $user->setIdUser($this->DBuser->lastInsertId());
            return true;
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred: ' . $e->getMessage());
        }
    }
    public function makeUserOffline($idUser): bool
    {
        try {
            $query = 'UPDATE user SET isOnline = 0 WHERE idUser = :idUser';
            $req = $this->DBuser->prepare($query);
            $req->execute(['idUser' => $idUser]);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function saveTokenAndUpdatePasswordResetAt($idUser, $token, $passwordResetAt): bool
    {
        try {
            $query = 'UPDATE user SET token = :token, passwordResetAt = :passwordResetAt WHERE idUser = :idUser';
            $req = $this->DBuser->prepare($query);
            $req->execute(['token' => $token, 'passwordResetAt' => $passwordResetAt, 'idUser' => $idUser]);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function saveAuthCodeAndUpdateEmailChangeAt($idUser, $authCode, $emailChangedAt): bool
    {
        try {
            $query = 'UPDATE user SET authCode = :authCode, emailChangedAt = :emailChangedAt WHERE idUser = :idUser';
            $req = $this->DBuser->prepare($query);
            $req->execute(['authCode' => $authCode, 'emailChangedAt' => $emailChangedAt, 'idUser' => $idUser]);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateLastSeenAndSetUserOnline($idUser, $lastSeen): bool
    {
        try {
            $query = 'UPDATE user SET lastSeen = :lastSeen, isOnline = 1 WHERE idUser = :idUser';
            $req = $this->DBuser->prepare($query);
            $req->execute(['idUser' => $idUser, 'lastSeen' => $lastSeen]);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function activateUser($idUser): bool
    {
        try {
            $query = 'UPDATE user SET isActivated = 1, token = NULL WHERE idUser = :idUser';
            $req = $this->DBuser->prepare($query);
            $req->execute(['idUser' => $idUser]);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function updateUser($user): bool
    {
        try {
            $query = 'UPDATE user SET firstName = :firstName, lastName = :lastName, phone = :phone, bio = :bio, dateOfBirth = :dateOfBirth, updatedAt = :updatedAt WHERE idUser = :idUser';
            $req = $this->DBuser->prepare($query);
            $req->execute([
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'phone' => $user->getPhone(),
                'bio' => $user->getBio(),
                'dateOfBirth' => $user->getDateOfBirth(),
                'updatedAt' => $user->getUpdatedAt(),
                'idUser' => $user->getIdUser()
            ]);
            return true;
        } catch (PDOException $e) {
            throw new Exception('An error occurred while updating user details.');
        }
    }
    public function updateUserEmail($idUser, $newEmail): bool
    {
        try {
            $query = 'UPDATE user SET email = :email WHERE idUser = :idUser';
            $req = $this->DBuser->prepare($query);
            $req->execute(['email' => $newEmail, 'idUser' => $idUser]);
            return true;
        } catch (PDOException $e) {
            throw new Exception('An error occurred while updating user email.');
        }
    }

    public function clearAuthCode($idUser): bool
    {
        try {
            $query = 'UPDATE user SET authCode = NULL WHERE idUser = :idUser';
            $req = $this->DBuser->prepare($query);
            $req->execute(['idUser' => $idUser]);
            return true;
        } catch (PDOException $e) {
            throw new Exception('An error occurred while clearing auth code.');
        }
    }
    public function updateUserPhone($user): bool
    {
        try {
            $query = 'UPDATE user SET phone = :phone WHERE idUser = :idUser';
            $req = $this->DBuser->prepare($query);
            $req->execute([
                'phone' => $user->getPhone(),
                'idUser' => $user->getIdUser()
            ]);
            return true;
        } catch (PDOException $e) {
            throw new Exception('An error occurred while updating user phone.');
        }
    }
    public function updateUserBio($user): bool
    {
        try {
            $query = $this->DBuser->prepare('UPDATE user SET bio = :bio WHERE idUser = :idUser');
            $query->execute([
                'bio' => $user->getBio(),
                'idUser' => $user->getIdUser()
            ]);
            return true;
        } catch (PDOException $e) {
            throw new Exception('An error occurred while updating bio.');
        }
    }
    public function updateUserDateOfBirth($user): bool
    {
        try {
            $query = $this->DBuser->prepare('UPDATE user SET dateOfBirth = :dateOfBirth WHERE idUser = :idUser');
            $query->execute([
                'dateOfBirth' => $user->getDateOfBirth(),
                'idUser' => $user->getIdUser()
            ]);
            return true;
        } catch (PDOException $e) {
            throw new Exception('An error occurred while updating date of birth.');
        }
    }
    public function updatePassword($idUser, $newPassword): bool
    {
        try {
            $query = $this->DBuser->prepare('UPDATE user SET password = :password WHERE idUser = :idUser');
            $query->execute([
                'password' => $newPassword,
                'idUser' => $idUser
            ]);
            return true;
        } catch (PDOException $e) {
            throw new Exception('An error occurred while updating password.');
        }
    }
    public function resetPassword($idUser, $passwordHash): bool
    {
        try {
            $query = $this->DBuser->prepare('UPDATE user SET password = :password , token = NULL WHERE idUser = :idUser');
            $query->execute([
                'password' => $passwordHash,
                'idUser' => $idUser
            ]);
            return true;
        } catch (PDOException $e) {
            throw new Exception('An error occurred while resetting password.');
        }
    }
    public function deleteUser($idUser, $deletedAt): bool
    {
        try {
            $query = $this->DBuser->prepare('UPDATE user SET isDeleted = 1, isOnline = 0, authCode = NULL ,token = NULL, deletedAt = :deletedAt WHERE idUser = :idUser');
            $query->execute(['idUser' => $idUser, 'deletedAt' => $deletedAt]);
            return true;
        } catch (PDOException $e) {
            throw new Exception('Un erreur est survenu lors de la suppression du compte utilisateur.');
        }
    }
    public function updateProfilePicture($idUser, $avatarPath)
    {
        $query = $this->DBuser->prepare('UPDATE user SET avatarPath = :avatarPath WHERE idUser = :idUser');
        $query->execute([
            'avatarPath' => $avatarPath,
            'idUser' => $idUser
        ]);
    }
    public function updateBanner($idUser, $bannerPath)
    {
        $query = $this->DBuser->prepare('UPDATE user SET bannerPath = :bannerPath WHERE idUser = :idUser');
        $query->execute([
            'bannerPath' => $bannerPath,
            'idUser' => $idUser
        ]);
    }
    public function countUserPreferences($idUser): int
    {
        try {
            $query = 'SELECT COUNT(*) FROM preference WHERE idUser = :idUser';
            $req = $this->DBuser->prepare($query);
            $req->execute(['idUser' => $idUser]);
            $count = $req->fetchColumn();
            return (int)$count;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getEventCategoryBySlug($categorySlug)
    {
        try {
            $query = 'SELECT * FROM event_category WHERE category_slug = :categorySlug';
            $req = $this->DBuser->prepare($query);
            $req->execute(['categorySlug' => $categorySlug]);
            $category = $req->fetch(PDO::FETCH_ASSOC);
            return $category !== false ? $category : null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function addUserPreferences($idUser, $idVille,$idEventCategory): bool
    {
        try {
            $query = 'INSERT INTO preference (idUser, idVille, idEventCategory, createdAt) VALUES (:idUser, :idVille, :idEventCategory, :createdAt)';
            $req = $this->DBuser->prepare($query);
            $req->execute([
                'idUser' => $idUser,
                'idVille' => $idVille,
                'idEventCategory' => $idEventCategory,
                'createdAt' => date('Y-m-d H:i:s')
            ]);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
