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
            $query = 'SELECT u.*, r.name AS roleName FROM user u JOIN role r ON u.idRole = r.idRole WHERE u.email = :email';
            $req = $this->DBuser->prepare($query);
            $req->execute(['email' => $email]);
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

    public function signUp(User $user): User
    {
        try {
            $query = 'INSERT INTO user (firstName, lastName, email, password, isActivated, isBanned, isDeleted, token, rgpdAcceptedDate, createdAt, idRole) 
            VALUES (:firstName, :lastName, :email, :password, :isActivated, :isBanned, :isDeleted, :token, :rgpdAcceptedDate, :createdAt, :idRole)';

            $req = $this->DBuser->prepare($query);
            $req->execute([
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
            return $user;
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
}