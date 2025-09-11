<?php

namespace src\Models;

use DateTime;
use src\Services\Hydration;

class User
{
    private int $userId;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $password;
    private bool $isActivated;
    private ?string $token;
    private ?string $resetPasswordToken;
    private string $profilePicturePath;
    private DateTime $rgpdDate;
    private DateTime $createdAt;
    private ?DateTime $updatedAt;
    private ?DateTime $resetPasswordRequestTime;
    private int $roleId;
    private ?string $roleName = null;
    use Hydration;


    /**
     * Get the value of userId
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @return  self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get the value of firstName
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @return  self
     */
    public function setFirstName($firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of lastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @return  self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of isActivated
     */
    public function getIsActivated()
    {
        return $this->isActivated;
    }

    /**
     * Set the value of isActivated
     *
     * @return  self
     */
    public function setIsActivated($isActivated)
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    /**
     * Get the value of token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the value of token
     *
     * @return  self
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }


    /**
     * Get the value of profilePicture
     */
    public function getProfilePicturePath(): string
    {
        return $this->profilePicturePath;
    }

    /**
     * Set the value of profilePicture
     */
    public function setProfilePicturePath(string $profilePicturePath): self
    {
        $this->profilePicturePath = $profilePicturePath;

        return $this;
    }

    /**
     * Get the value of resetPasswordToken
     */
    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    /**
     * Set the value of resetPasswordToken
     */
    public function setResetPasswordToken(?string $resetPasswordToken): self
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    /**
     * Get the value of roleId
     */
    public function getRoleId(): int
    {
        return $this->roleId;
    }

    /**
     * Set the value of roleId
     */
    public function setRoleId(int $roleId): self
    {
        $this->roleId = $roleId;

        return $this;
    }

    /**
     * Get the value of rgpdDate
     */
    public function getRgpdDate(): DateTime|string
    {
        if (is_string($this->rgpdDate)) {
            return $this->rgpdDate;
        }
        // Format the DateTime object to a string
        return $this->rgpdDate->format('Y-m-d H:i:s');
    }

    /**
     * Get the value of rgpdDate formatted
     */
    public function getRgpdDateFormatted(): DateTime|string
    {
        if (is_string($this->rgpdDate)) {
            return $this->rgpdDate;
        }
        // Format the DateTime object to a string
        return $this->rgpdDate->format('d/m/Y à H:i');
    }

    /**
     * Set the value of rgpdDate
     */
    public function setRgpdDate(DateTime|string $rgpdDate): self
    {

        if (is_string($rgpdDate)) {
            $rgpdDate = new DateTime($rgpdDate);
        }
        $this->rgpdDate = $rgpdDate;

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): DateTime|string
    {
        if (is_string($this->createdAt)) {
            return $this->createdAt;
        }
        // Format the DateTime object to a string
        return $this->createdAt->format('Y-m-d H:i:s');
    }

    /**
     * Get the value of createdAt formatted
     */
    public function getCreatedAtFormatted(): DateTime|string
    {
        if (is_string($this->createdAt)) {
            return $this->createdAt;
        }
        // Format the DateTime object to a string
        return $this->createdAt->format('d/m/Y à H:i');
    }

    /**
     * Set the value of createdAt
     */
    public function setCreatedAt(DateTime|string $createdAt): self
    {
        if (is_string($createdAt)) {
            $createdAt = new DateTime($createdAt);
        }
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt(): DateTime|string|null
    {
        if ($this->updatedAt === null) {
            return null;
        }
        if (is_string($this->updatedAt)) {
            return $this->updatedAt;
        }
        if (is_a($this->updatedAt, DateTime::class)) {
            return $this->updatedAt->format('Y-m-d H:i:s');
        }
        return null;
    }

    /**
     * Get the value of updatedAt formatted
     */
    public function getUpdatedAtFormatted(): string|null
    {
        if ($this->updatedAt === null) {
            return null;
        }
        if (is_string($this->updatedAt)) {
            return $this->updatedAt;
        }
        if (is_a($this->updatedAt, DateTime::class)) {
            return $this->updatedAt->format('d/m/Y à H:i');
        }
        return null;
    }

    /**
     * Set the value of updatedAt
     */
    public function setUpdatedAt(mixed $updatedAt): self
    {
        if ($updatedAt === null) {
            $this->updatedAt = null;
        } elseif (is_string($updatedAt)) {
            $this->updatedAt = new DateTime($updatedAt);
        } elseif (is_a($updatedAt, DateTime::class)) {
            $this->updatedAt = $updatedAt;
        }
        return $this;
    }
    /**
     * Get the value of lastResetPasswordTime
     */
    public function getResetPasswordRequestTime(): DateTime|string|null
    {
        if ($this->resetPasswordRequestTime === null) {
            return null;
        }
        if (is_string($this->resetPasswordRequestTime)) {
            return $this->resetPasswordRequestTime;
        }
        if (is_a($this->resetPasswordRequestTime, DateTime::class)) {
            return $this->resetPasswordRequestTime->format('Y-m-d H:i:s');
        }
        return null;
    }

    /**
     * Get the value of lastResetPasswordTime formatted
     */
    public function getResetPasswordRequestTimeFormatted(): string|null
    {
        if ($this->resetPasswordRequestTime === null) {
            return null;
        }
        if (is_string($this->resetPasswordRequestTime)) {
            return $this->resetPasswordRequestTime;
        }
        if (is_a($this->resetPasswordRequestTime, DateTime::class)) {
            return $this->resetPasswordRequestTime->format('d/m/Y à H:i');
        }
        return null;
    }

    /**
     * Set the value of lastResetPasswordTime
     */
    public function setResetPasswordRequestTime(mixed $resetPasswordRequestTime): self
    {
        if ($resetPasswordRequestTime === null) {
            $this->resetPasswordRequestTime = null;
        } elseif (is_string($resetPasswordRequestTime)) {
            $this->resetPasswordRequestTime = new DateTime($resetPasswordRequestTime);
        } elseif (is_a($resetPasswordRequestTime, DateTime::class)) {
            $this->resetPasswordRequestTime = $resetPasswordRequestTime;
        }
        return $this;
    }
    /**
     * Get the value of roleName
     */
    public function getRoleName(): ?string
    {
        return $this->roleName;
    }
    /**
     * Set the value of roleName
     *
     * @return  self
     */

    public function setRoleName(?string $roleName): self
    {
        $this->roleName = $roleName;
        return $this;
    }
}
