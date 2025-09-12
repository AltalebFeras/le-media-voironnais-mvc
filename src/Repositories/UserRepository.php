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
        $database = new Database();
        $this->DBuser = $database->getDB();

        require_once __DIR__ . '/../../config.php';
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
    public function signUp(User $user): User
    {
        try {
            $query = 'INSERT INTO user (firstName, lastName, email, password, isActivated, token, avatarPath, rgpdAcceptedDate, createdAt, idRole) 
            VALUES (:firstName, :lastName, :email, :password, :isActivated, :token, :avatarPath, :rgpdAcceptedDate, :createdAt, :idRole)';

            $req = $this->DBuser->prepare($query);
            $req->execute([
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'isActivated' => $user->getIsActivated(),
                'token' => $user->getToken(),
                'avatarPath' => $user->getAvatarPath(),
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
    public function saveToken($idUser, $token): bool
    {
        try {
            $query = 'UPDATE user SET token = :token WHERE idUser = :idUser';
            $req = $this->DBuser->prepare($query);
            $req->execute(['token' => $token, 'idUser' => $idUser]);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
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
    public function updateLastSeenAndSetUserOnline($idUser,$lastSeen): bool
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
            $query = 'UPDATE user SET firstName = :firstName, lastName = :lastName, email = :email, updatedAt = :updatedAt WHERE idUser = :idUser';
            $req = $this->DBuser->prepare($query);
            $req->execute([
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'updatedAt' => $user->getUpdatedAt(),
                'idUser' => $user->getIdUser()
            ]);
            return true;
        } catch (PDOException $e) {
            throw new Exception('An error occurred while updating user details.');
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
    public function deleteUser($idUser)
    {
        try {
            $query = $this->DBuser->prepare('DELETE FROM user WHERE idUser = :idUser');
            $query->execute(['idUser' => $idUser]);
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
}
