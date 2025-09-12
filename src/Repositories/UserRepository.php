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
    public function getUserById($user_id): ?User
    {
        try {
            $query = 'SELECT * FROM user WHERE user_id = :user_id';
            $req = $this->DBuser->prepare($query);
            $req->execute(['user_id' => $user_id]);
            $user = $req->fetchObject(User::class);
            return $user !== false ? $user : null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function signUp(User $user): User
    {
        try {
            $query = 'INSERT INTO user (firstName, lastName, email, password, isActivated, token, avatar_path, rgpd_accepted_date, createdAt, idRole) 
            VALUES (:firstName, :lastName, :email, :password, :isActivated, :activationToken, :profilePicturePath, :rgpdDate, :createdAt, :roleId)';

            $req = $this->DBuser->prepare($query);
            $req->execute([
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'isActivated' => $user->getIsActivated(),
                'activationToken' => $user->getToken(),
                'profilePicturePath' => $user->getProfilePicturePath(),
                'createdAt' => $user->getCreatedAt(),
                'rgpdDate' => $user->getRgpdDate(),
                'roleId' => $user->getRoleId()
            ]);
            $user->setUserId($this->DBuser->lastInsertId());
            return $user;
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred: ' . $e->getMessage());
        }
    }
    public function saveUserToken($user_id, $token)
    {
        try {
            $query = 'UPDATE user SET activationToken = :token WHERE user_id = :user_id';
            $req = $this->DBuser->prepare($query);
            $req->execute(['token' => $token, 'user_id' => $user_id]);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function saveResetPasswordToken($userId, $resetPasswordToken): bool
    {
        try {
            $query = 'UPDATE user SET reset_password_token = :reset_password_token WHERE user_id = :user_id';
            $req = $this->DBuser->prepare($query);
            $req->execute(['reset_password_token' => $resetPasswordToken, 'user_id' => $userId]);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getUser($email): bool|object|null
    {
        try {
            $query = 'SELECT u.*, r.name AS role_name FROM user u JOIN role r ON u.idRole = r.idRole WHERE u.email = :email';
            $req = $this->DBuser->prepare($query);
            $req->execute(['email' => $email]);
            $user = $req->fetchObject(User::class);
            // $user = $req->fetch(PDO::FETCH_ASSOC);

            return $user !== false ? $user : null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getUserByToken($token): ?User
    {
        try {
            $query  = 'SELECT user_id, token FROM user WHERE token = :token';
            $req = $this->DBuser->prepare($query);
            $req->execute(['token' => $token]);
            $user = $req->fetchObject(User::class);
            return $user !== false ? $user : null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getUserByResetPasswordToken($resetPasswordToken): ?User
    {
        try {
            $query  = 'SELECT user_id, reset_password_token FROM user WHERE reset_password_token = :resetPasswordToken';
            $req = $this->DBuser->prepare($query);
            $req->execute(['resetPasswordToken' => $resetPasswordToken]);
            $user = $req->fetchObject(User::class);
            return $user !== false ? $user : null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function updateResetPasswordRequestTime($userId, $resetPasswordRequestTime): bool
    {
        try {
            $query = 'UPDATE user SET reset_password_request_time = :reset_password_request_time WHERE user_id = :user_id';
            $req = $this->DBuser->prepare($query);
            $req->execute(['user_id' => $userId, 'reset_password_request_time' => $resetPasswordRequestTime]);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function activateUser($user_id): bool
    {
        try {
            $query = 'UPDATE user SET is_activated = 1, token = NULL WHERE user_id = :user_id';
            $req = $this->DBuser->prepare($query);
            $req->execute(['user_id' => $user_id]);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function updateUser($user): bool
    {
        try {
            $query = 'UPDATE user SET first_name = :first_name, last_name = :last_name, email = :email, updated_at = :updated_at WHERE user_id = :user_id';
            $req = $this->DBuser->prepare($query);
            $req->execute([
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'email' => $user->getEmail(),
                'updated_at' => $user->getUpdatedAt(),
                'user_id' => $user->getUserId()
            ]);
            return true;
        } catch (PDOException $e) {
            throw new Exception('An error occurred while updating user details.');

        }
    }
    public function updatePassword($user_id, $newPassword): bool
    {
        try {
            $query = $this->DBuser->prepare('UPDATE user SET password = :password WHERE user_id = :user_id');
            $query->execute([
                'password' => $newPassword,
                'user_id' => $user_id
            ]);
            return true;    
        } catch (PDOException $e) {
            throw new Exception('An error occurred while updating password.');
        }
    }
    public function resetPassword($user_id, $passwordHash): bool
    {
        try {
            $query = $this->DBuser->prepare('UPDATE user SET password = :password , reset_password_token = NULL WHERE user_id = :user_id');
            $query->execute([
                'password' => $passwordHash,
                'user_id' => $user_id
            ]);
            return true;
        } catch (PDOException $e) {
            throw new Exception('An error occurred while resetting password.');
        }
    }
    public function deleteUser($user_id)
    {
        try {

            $query = $this->DBuser->prepare('DELETE FROM user WHERE user_id = :user_id');
            $query->execute(['user_id' => $user_id]);
        } catch (PDOException $e) {
            throw new Exception('Un erreur est survenu lors de la suppression du compte utilisateur.');
        }
    }
    public function updateProfilePicture($user_id, $profilePicturePath)
    {
        $query = $this->DBuser->prepare('UPDATE user SET profile_picture_path = :profile_picture_path WHERE user_id = :user_id');
        $query->execute([
            'profile_picture_path' => $profilePicturePath,
            'user_id' => $user_id
        ]);
    }
  
}