<?php

namespace src\Models;

use DateTime;
use src\Services\Hydration;

class Entreprise
{
    private int $idEntreprise;
    private string $uiid;
    private string $name;
    private string $slug;
    private ?string $description;
    private ?string $logoPath;
    private ?string $bannerPath;
    private ?string $address;
    private ?string $phone;
    private ?string $email;
    private ?string $website;
    private ?string $siret;
    private bool $isActive;
    private bool $hasRequestForActivation;
    private bool $isPublic;
    private bool $isPartner;
    private bool $isDeleted;
    private int $idUser;
    private int $idVille;
    private DateTime|string $createdAt;
    private DateTime|string|null $updatedAt;
    private DateTime|string|null $partnerStartDate;
    private DateTime|string|null $partnerEndDate;
    private DateTime|string|null $requestDate;


    use Hydration;

    // Getters
    public function getIdEntreprise(): int
    {
        return $this->idEntreprise;
    }
    public function getUiid()
    {
        return $this->uiid;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getSlug()
    {
        return $this->slug;
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

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }
    public function getHasRequestForActivation(): bool
    {
        return $this->hasRequestForActivation;
    }
    public function getIsPartner()
    {
        return $this->isPartner;
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
    public function getPartnerStartDate(): DateTime|string|null
    {
        if ($this->partnerStartDate === null) {
            return null;
        }
        if (is_string($this->partnerStartDate)) {
            return $this->partnerStartDate;
        }
        if (is_a($this->partnerStartDate, DateTime::class)) {
            return $this->partnerStartDate->format('Y-m-d H:i:s');
        }
        return null;
    }
    public function getPartnerStartDateFormatted(): string|null
    {
        if ($this->partnerStartDate === null) {
            return null;
        }
        if ($this->partnerStartDate instanceof DateTime) {
            return $this->partnerStartDate->format('d/m/Y à H:i');
        }
        try {
            $dt = new DateTime($this->partnerStartDate);
            return $dt->format('d/m/Y à H:i');
        } catch (\Exception $e) {
            return $this->partnerStartDate;
        }
    }
    public function getPartnerEndDate(): DateTime|string|null
    {
        if ($this->partnerEndDate === null) {
            return null;
        }
        if (is_string($this->partnerEndDate)) {
            return $this->partnerEndDate;
        }
        if (is_a($this->partnerEndDate, DateTime::class)) {
            return $this->partnerEndDate->format('Y-m-d H:i:s');
        }
        return null;
    }
    public function getPartnerEndDateFormatted(): string|null
    {
        if ($this->partnerEndDate === null) {
            return null;
        }
        if ($this->partnerEndDate instanceof DateTime) {
            return $this->partnerEndDate->format('d/m/Y à H:i');
        }
        try {
            $dt = new DateTime($this->partnerEndDate);
            return $dt->format('d/m/Y à H:i');
        } catch (\Exception $e) {
            return $this->partnerEndDate;
        }
    }
    public function getRequestDate(): DateTime|string|null
    {
        if ($this->requestDate === null) {
            return null;
        }
        if (is_string($this->requestDate)) {
            return $this->requestDate;
        }
        if (is_a($this->requestDate, DateTime::class)) {
            return $this->requestDate->format('Y-m-d H:i:s');
        }
        return null;
    }
    public function getRequestDateFormatted(): string|null
    {
        if ($this->requestDate === null) {
            return null;
        }
        if ($this->requestDate instanceof DateTime) {
            return $this->requestDate->format('d/m/Y à H:i');
        }
        try {
            $dt = new DateTime($this->requestDate);
            return $dt->format('d/m/Y à H:i');
        } catch (\Exception $e) {
            return $this->requestDate;
        }
    }


    // Setters
    public function setIdEntreprise(int $idEntreprise): static
    {
        $this->idEntreprise = $idEntreprise;
        return $this;
    }
    public function setUiid($uiid)
    {
        $this->uiid = $uiid;

        return $this;
    }
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }
    public function setSlug($slug)
    {
        $this->slug = $slug;

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

    public function setSiret(?string $siret): static
    {
        $this->siret = $siret;
        return $this;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }
    public function setHasRequestForActivation(bool $hasRequestForActivation): static
    {
        $this->hasRequestForActivation = $hasRequestForActivation;
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
    public function setPartnerStartDate(DateTime|string|null $partnerStartDate): self
    {
        if ($partnerStartDate === null) {
            $this->partnerStartDate = null;
        } elseif (is_string($partnerStartDate)) {
            $this->partnerStartDate = new DateTime($partnerStartDate);
        } elseif (is_a($partnerStartDate, DateTime::class)) {
            $this->partnerStartDate = $partnerStartDate;
        }
        return $this;
    }
    public function setPartnerEndDate(DateTime|string|null $partnerEndDate): self
    {
        if ($partnerEndDate === null) {
            $this->partnerEndDate = null;
        } elseif (is_string($partnerEndDate)) {
            $this->partnerEndDate = new DateTime($partnerEndDate);
        } elseif (is_a($partnerEndDate, DateTime::class)) {
            $this->partnerEndDate = $partnerEndDate;
        }
        return $this;
    }
    public function setRequestDate(DateTime|string|null $requestDate): self
    {
        if ($requestDate === null) {
            $this->requestDate = null;
        } elseif (is_string($requestDate)) {
            $this->requestDate = new DateTime($requestDate);
        } elseif (is_a($requestDate, DateTime::class)) {
            $this->requestDate = $requestDate;
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
    public function setIsPublic(bool $isPublic): static
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
    public function setIsPartner($isPartner)
    {
        $this->isPartner = $isPartner;

        return $this;
    }

    public function validate(): array
    {
        $errors = [];

        // Validation du nom (obligatoire)
        if (empty(trim($this->name))) {
            $errors['name'] = 'Le nom de l\'entreprise est obligatoire.';
        } elseif (strlen($this->name) > 180) {
            $errors['name'] = 'Le nom ne peut pas dépasser 180 caractères.';
        }
        // Validation de l'description
        if (!empty($this->description) && (strlen($this->description) > 1000 || strlen($this->description) < 10)) {
            $errors['description'] = 'La description ne peut pas dépasser 1000 caractères et doit contenir au moins 10 caractères.';
        }
        // Validation de l'adresse
        if (!empty($this->address) && (strlen($this->address) > 255 || strlen($this->address) < 5)) {
            $errors['address'] = 'L\'adresse ne peut pas dépasser 255 caractères et doit contenir au moins 5 caractères.';
        }

        // Validation de l'email (si fourni)
        if (!empty($this->email) && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'adresse email n\'est pas valide.';
        }

        // Validation du téléphone (si fourni)
        if (!empty($this->phone) && !preg_match('/^[0-9\s\-\+\(\)\.]{10,20}$/', $this->phone)) {
            $errors['phone'] = 'Le numéro de téléphone n\'est pas valide.';
        }

        // Validation du site web (si fourni)
        if (!empty($this->website) && !filter_var($this->website, FILTER_VALIDATE_URL)) {
            $errors['website'] = 'L\'URL du site web n\'est pas valide.';
        }

        // Validation du SIRET (si fourni)
        if (!empty($this->siret) && !preg_match('/^[0-9]{14}$/', $this->siret)) {
            $errors['siret'] = 'Le numéro SIRET doit contenir exactement 14 chiffres.';
        }

        // Validation de l'ID utilisateur (obligatoire)
        if (empty($this->idUser)) {
            $errors['idUser'] = 'L\'utilisateur est obligatoire.';
        }

        // Validation de l'ID ville (obligatoire)
        if (empty($this->idVille)) {
            $errors['idVille'] = 'La ville est obligatoire.';
        }

        return $errors;
    }

    public function isValid(): bool
    {
        return empty($this->validate());
    }

    public function toArray(): array
    {
        return [
            'idEntreprise' => $this->idEntreprise,
            'name' => $this->name,
            'description' => $this->description,
            'logoPath' => $this->logoPath,
            'bannerPath' => $this->bannerPath,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'siret' => $this->siret,
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
