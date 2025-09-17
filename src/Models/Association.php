<?php

namespace src\Models;

use DateTime;
use src\Services\Hydration;

class Association
{
    private int $idAssociation;
    private string $name;
    private ?string $description;
    private ?string $logoPath;
    private ?string $bannerPath;
    private ?string $address;
    private ?string $phone;
    private ?string $email;
    private ?string $website;
    private bool $isActive;
    private bool $isPublic;
    private bool $isDeleted;
    private int $idUser;
    private int $idVille;
    private DateTime|string $createdAt;
    private DateTime|string|null $updatedAt;

    use Hydration;

    // Getters
    public function getIdAssociation(): int
    {
        return $this->idAssociation;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getLogoPath(): ?string
    {
        return $this->logoPath;
    }

    public function getBannerPath(): ?string
    {
        return $this->bannerPath;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getIdUser(): int
    {
        return $this->idUser;
    }

    public function getCreatedAt(): DateTime|string
    {
        if (is_string($this->createdAt)) {
            return $this->createdAt;
        }
        // Format the DateTime object to a string
        return $this->createdAt->format('Y-m-d H:i:s');
    }

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

    // Setters
    public function setIdAssociation(int $idAssociation): static
    {
        $this->idAssociation = $idAssociation;
        return $this;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function setLogoPath(?string $logoPath): static
    {
        $this->logoPath = $logoPath;
        return $this;
    }

    public function setBannerPath(?string $bannerPath): static
    {
        $this->bannerPath = $bannerPath;
        return $this;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;
        return $this;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function setIdUser(int $idUser): static
    {
        $this->idUser = $idUser;
        return $this;
    }

    public function setCreatedAt(DateTime|string $createdAt): self
    {
        if (is_string($createdAt)) {
            $createdAt = new DateTime($createdAt);
        }
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(DateTime|string|null $updatedAt): self
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
     * Get the value of idVille
     */
    public function getIdVille(): int
    {
        return $this->idVille;
    }

    /**
     * Set the value of idVille
     *
     * @return  self
     */
    public function setIdVille(int $idVille): self
    {
        $this->idVille = $idVille;
        return $this;
    }

    /**
     * Get the value of isPublic
     */
    public function getIsPublic(): bool
    {
        return $this->isPublic;
    }

    /**
     * Set the value of isPublic
     *
     * @return  self
     */
    public function setIsPublic($isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get the value of isDeleted
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set the value of isDeleted
     *
     * @return  self
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function validate()
    {
        $errors = [];

        // Validation du nom
        if (empty(trim($this->name))) {
            $errors['name'] = 'Le nom de l\'association est obligatoire.';
        } elseif (strlen($this->name) > 255) {
            $errors['name'] = 'Le nom ne peut pas dépasser 255 caractères.';
        }
        // Validation de l'description
        if (!empty($this->description) && strlen($this->description) > 1000 || strlen($this->description) < 10) {
            $errors['description'] = 'La description ne peut pas dépasser 1000 caractères et doit contenir au moins 10 caractères.';
        }

        // Validation de l'adresse
        if (!empty($this->address) && strlen($this->address) > 255 || strlen($this->address) < 5) {
            $errors['address'] = 'L\'adresse ne peut pas dépasser 255 caractères et doit contenir au moins 5 caractères.';
        }

        // Validation de l'email
        if (!empty($this->email) && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'adresse email n\'est pas valide.';
        }

        // Validation du téléphone
        if (!empty($this->phone) && !preg_match('/^[0-9\s\-\+\(\)\.]{10,20}$/', $this->phone)) {
            $errors['phone'] = 'Le numéro de téléphone n\'est pas valide.';
        }

        // Validation du site web
        if (!empty($this->website) && !filter_var($this->website, FILTER_VALIDATE_URL)) {
            $errors['website'] = 'L\'URL du site web n\'est pas valide.';
        }
        if (empty($this->idVille)) {
            $errors['ville'] = "La ville est obligatoire";
        }
        // Validation de l'utilisateur
        if (empty($this->idUser)) {
            $errors['idUser'] = 'L\'utilisateur est obligatoire.';
        }

        return $errors;
    }

    public function isValid(): bool
    {
        return empty($this->validate());
    }

    public function toArray()
    {
        return [
            'idAssociation' => $this->idAssociation,
            'name' => $this->name,
            'description' => $this->description,
            'logoPath' => $this->logoPath,
            'bannerPath' => $this->bannerPath,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'isActive' => $this->isActive,
            'isPublic' => $this->isPublic,
            'isDeleted' => $this->isDeleted,
            'idUser' => $this->idUser,
            'idVille' => $this->idVille,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
