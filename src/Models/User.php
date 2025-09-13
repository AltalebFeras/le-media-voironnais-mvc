<?php

namespace src\Models;

use DateTime;
use src\Services\Hydration;

class User
{
    private int $idUser;
    private int $idRole;
    private string $firstName;
    private string $lastName;
    private string $email;
    private ?string $phone;
    private string $password;
    private ?string $avatarPath;
    private ?string $bannerPath;
    private ?string $bio;
    private DateTime|string|null $dateOfBirth;
    private bool $isActivated;
    private bool $isOnline;
    private DateTime|string|null $lastSeen;
    private DateTime|string $rgpdAcceptedDate;
    private ?string $token;
    private DateTime|string $createdAt;
    private DateTime|string|null $updatedAt;
    private DateTime|string|null $resetPasswordRequestTime;
    private string $roleName;

    use Hydration;


    /**
     * Get the value of idUser
     */
    public function getIdUser(): int
    {
        return $this->idUser;
    }

    /**
     * Set the value of idUser
     *
     * @return  self
     */
    public function setIdUser($idUser): static
    {
        $this->idUser = $idUser;

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
     * Get the value of avatarPath
     */
    public function getAvatarPath(): string|null 
    {
        return $this->avatarPath;
    }

    /**
     * Set the value of avatarPath
     *
     * @return  self
     */
    public function setAvatarPath(string $avatarPath): self
    {
        $this->avatarPath = $avatarPath;
        return $this;
    }

    /**
     * Get the value of bannerPath
     */
    public function getBannerPath(): ?string
    {
        return $this->bannerPath;
    }

    /**
     * Set the value of bannerPath
     *
     * @return  self
     */
    public function setBannerPath(?string $bannerPath): self
    {
        $this->bannerPath = $bannerPath;

        return $this;
    }

    /**
     * Get the value of idRole
     */
    public function getIdRole(): int
    {
        return $this->idRole;
    }

    /**
     * Set the value of idRole
     *
     * @return  self
     */
    public function setIdRole(int $idRole): self
    {
        $this->idRole = $idRole;
        return $this;
    }

    /**
     * Get the value of rgpdAcceptedDate
     */
    public function getRgpdAcceptedDate(): DateTime|string
    {
        if (is_string($this->rgpdAcceptedDate)) {
            return $this->rgpdAcceptedDate;
        }
        return $this->rgpdAcceptedDate->format('Y-m-d H:i:s');
    }

    /**
     * Get the value of rgpdAcceptedDate formatted
     */
    public function getRgpdAcceptedDateFormatted(): DateTime|string
    {
        if (is_string($this->rgpdAcceptedDate)) {
            return $this->rgpdAcceptedDate;
        }
        return $this->rgpdAcceptedDate->format('d/m/Y à H:i');
    }

    /**
     * Set the value of rgpdAcceptedDate
     */
    public function setRgpdAcceptedDate(DateTime|string|null $rgpdAcceptedDate): self
    {
        if (is_string($rgpdAcceptedDate)) {
            $rgpdAcceptedDate = new DateTime($rgpdAcceptedDate);
        }
        $this->rgpdAcceptedDate = $rgpdAcceptedDate;
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
     * Get the value of createdAt formatted
     */
    public function getCreatedAtFormatted(): string|null
    {
        if ($this->createdAt === null) {
            return null;
        }
        if ($this->createdAt instanceof DateTime) {
            return $this->createdAt->format('d/m/Y à H:i');
        }
        // If it's a string, try to parse it as a DateTime
        try {
            $dt = new DateTime($this->createdAt);
            return $dt->format('d/m/Y à H:i');
        } catch (\Exception $e) {
            return $this->createdAt;
        }
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
    public function setResetPasswordRequestTime(mixed $resetPasswordRequestTime): void
    {
        if ($resetPasswordRequestTime === null) {
            $this->resetPasswordRequestTime = null;
        } elseif (is_string($resetPasswordRequestTime)) {
            $this->resetPasswordRequestTime = new DateTime($resetPasswordRequestTime);
        } elseif (is_a($resetPasswordRequestTime, DateTime::class)) {
            $this->resetPasswordRequestTime = $resetPasswordRequestTime;
        }
    }

    /**
     * Get the value of bio
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set the value of bio
     *
     * @return  self
     */
    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get the value of dateOfBirth
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set the value of dateOfBirth
     *
     * @return  self
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get the value of isOnline
     */
    public function getIsOnline(): bool
    {
        return $this->isOnline;
    }

    /**
     * Set the value of isOnline
     *
     * @return  self
     */
    public function setIsOnline($isOnline)
    {
        $this->isOnline = $isOnline;

        return $this;
    }

    /**
     * Get the value of lastSeen
     */
    public function getLastSeen(): DateTime|string|null
    {
        if ($this->lastSeen === null) {
            return null;
        }
        if (is_string($this->lastSeen)) {
            return $this->lastSeen;
        }
        // Format the DateTime object to a string
        return $this->lastSeen->format('Y-m-d H:i:s');
    }

    /**
     * Get the value of dateOfBirth formatted
     */
    public function getDateOfBirthFormatted(): string|null
    {
        if ($this->dateOfBirth === null) {
            return null;
        }
        if ($this->dateOfBirth instanceof DateTime) {
            return $this->dateOfBirth->format('d/m/Y');
        }
        try {
            $dt = new DateTime($this->dateOfBirth);
            return $dt->format('d/m/Y');
        } catch (\Exception $e) {
            return $this->dateOfBirth;
        }
    }

    /**
     * Get the value of lastSeen formatted
     */
    public function getLastSeenFormatted(): string|null
    {
        if ($this->lastSeen === null) {
            return null;
        }
        if ($this->lastSeen instanceof DateTime) {
            return $this->lastSeen->format('d/m/Y à H:i');
        }
        try {
            $dt = new DateTime($this->lastSeen);
            return $dt->format('d/m/Y à H:i');
        } catch (\Exception $e) {
            return $this->lastSeen;
        }
    }

    /**
     * Get the value of updatedAt formatted
     */
    public function getUpdatedAtFormatted(): string|null
    {
        if ($this->updatedAt === null) {
            return null;
        }
        if ($this->updatedAt instanceof DateTime) {
            return $this->updatedAt->format('d/m/Y à H:i');
        }
        try {
            $dt = new DateTime($this->updatedAt);
            return $dt->format('d/m/Y à H:i');
        } catch (\Exception $e) {
            return $this->updatedAt;
        }
    }

    /**
     * Set the value of lastSeen
     */
    public function setLastSeen(DateTime|string|null $lastSeen): self
    {
        if ($lastSeen === null) {
            $this->lastSeen = null;
        } elseif (is_string($lastSeen)) {
            $this->lastSeen = new DateTime($lastSeen);
        } elseif (is_a($lastSeen, DateTime::class)) {
            $this->lastSeen = $lastSeen;
        }
        return $this;
    }

    /**
     * Get the value of roleName
     */
    public function getRoleName()
    {
        return $this->roleName;
    }

    /**
     * Set the value of roleName
     *
     * @return  self
     */
    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;

        return $this;
    }

    /**
     * Get the value of phone
     */ 
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return  self
     */ 
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }
}
