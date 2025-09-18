<?php

namespace src\Models;

use DateTime;

class Evenement
{
    private int $idEvenement;
    private string $title;
    private string $slug;
    private ?string $description = null;
    private ?string $shortDescription = null;
    private string $startDate;
    private string $endDate;
    private string $registrationDeadline;
    private int $maxParticipants;
    private ?int $currentParticipants = null;
    private ?string $address = null;
    private ?string $imagePath = null;
    private ?string $bannerPath = null;
    private ?string $status = null;
    private ?bool $isPublic = null;
    private ?bool $isDeleted = null;
    private ?bool $requiresApproval = null;
    private ?float $price = null;
    private ?string $currency = null;
    private string $createdAt;
    private ?string $updatedAt = null;
    private int $idUser;
    private ?int $idAssociation = null;
    private int $idVille;
    private int $idEventCategory;

    // Getters
    public function getIdEvenement(): ?int
    {
        return $this->idEvenement;
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function getSlug()
    {
        return $this->slug;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }
    public function getStartDate(): ?string
    {
        return $this->startDate;
    }
    public function getEndDate(): ?string
    {
        return $this->endDate;
    }
    public function getRegistrationDeadline(): ?string
    {
        return $this->registrationDeadline;
    }
    public function getMaxParticipants(): ?int
    {
        return $this->maxParticipants;
    }
    public function getCurrentParticipants(): ?int
    {
        return $this->currentParticipants;
    }
    public function getAddress(): ?string
    {
        return $this->address;
    }
    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }
    public function getBannerPath(): ?string
    {
        return $this->bannerPath;
    }
    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }
    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }
    public function getRequiresApproval(): ?bool
    {
        return $this->requiresApproval;
    }
    public function getPrice(): ?float
    {
        return $this->price;
    }
    public function getCurrency(): ?string
    {
        return $this->currency;
    }
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
    public function getIdUser(): ?int
    {
        return $this->idUser;
    }
    public function getIdAssociation(): ?int
    {
        return $this->idAssociation;
    }
    public function getIdVille(): ?int
    {
        return $this->idVille;
    }
    public function getIdEventCategory(): ?int
    {
        return $this->idEventCategory;
    }

    // Setters with method chaining
    public function setIdEvenement(?int $idEvenement): self
    {
        $this->idEvenement = $idEvenement;
        return $this;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    public function setStartDate(?string $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function setEndDate(?string $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function setRegistrationDeadline(?string $registrationDeadline): self
    {
        $this->registrationDeadline = $registrationDeadline;
        return $this;
    }

    public function setMaxParticipants(?int $maxParticipants): self
    {
        $this->maxParticipants = $maxParticipants;
        return $this;
    }

    public function setCurrentParticipants(?int $currentParticipants): self
    {
        $this->currentParticipants = $currentParticipants;
        return $this;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function setBannerPath(?string $bannerPath): self
    {
        $this->bannerPath = $bannerPath;
        return $this;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setIsPublic(?bool $isPublic): self
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    public function setRequiresApproval(?bool $requiresApproval): self
    {
        $this->requiresApproval = $requiresApproval;
        return $this;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function setCreatedAt(?string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(?string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function setIdUser(?int $idUser): self
    {
        $this->idUser = $idUser;
        return $this;
    }

    public function setIdAssociation(?int $idAssociation): self
    {
        $this->idAssociation = $idAssociation;
        return $this;
    }

    public function setIdVille(?int $idVille): self
    {
        $this->idVille = $idVille;
        return $this;
    }

    public function setIdEventCategory(?int $idEventCategory): self
    {
        $this->idEventCategory = $idEventCategory;
        return $this;
    }

    // Formatted date getters
    public function getStartDateFormatted(): ?string
    {
        if (!$this->startDate) return null;
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $this->startDate);
        return $date ? $date->format('d/m/Y H:i') : $this->startDate;
    }

    public function getCreatedAtFormatted(): ?string
    {
        if (!$this->createdAt) return null;
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $this->createdAt);
        return $date ? $date->format('d/m/Y H:i') : $this->createdAt;
    }

    /**
     * Validate event data
     */
    public function validate(): array
    {
        $errors = [];

        // Required fields validation
        if (empty($this->title)) {
            $errors['title'] = 'Le titre est obligatoire';
        } elseif (strlen($this->title) < 3 || strlen($this->title) > 180) {
            $errors['title'] = 'Le titre doit contenir entre 3 et 180 caractères';
        }

        if (empty($this->description)) {
            $errors['description'] = 'La description est obligatoire';
        } elseif (strlen($this->description) > 5000) {
            $errors['description'] = 'La description ne peut pas dépasser 5000 caractères';
        }

        if (empty($this->startDate)) {
            $errors['startDate'] = 'La date de début est obligatoire';
        } else {
            // Try datetime-local format first, then full datetime format
            $startDate = DateTime::createFromFormat('Y-m-d\TH:i', $this->startDate);
            if (!$startDate) {
                $startDate = DateTime::createFromFormat('Y-m-d H:i:s', $this->startDate);
            }

            if (!$startDate) {
                $errors['startDate'] = 'Format de date invalide';
            } else {
                $today = new DateTime();
                $today->setTime(0, 0, 0); // Set to start of day for comparison
                if ($startDate < $today) {
                    $errors['startDate'] = 'La date de début ne peut pas être dans le passé';
                }
            }
        }

        if (empty($this->address)) {
            $errors['address'] = 'L\'adresse est obligatoire';
        } elseif (strlen($this->address) > 500) {
            $errors['address'] = 'L\'adresse ne peut pas dépasser 500 caractères';
        }

        if (!$this->idVille) {
            $errors['idVille'] = 'La ville est obligatoire';
        }

        if (!$this->idEventCategory) {
            $errors['idEventCategory'] = 'La catégorie est obligatoire';
        }

        // Optional fields validation
        if ($this->maxParticipants && ($this->maxParticipants < 1 || $this->maxParticipants > 10000)) {
            $errors['maxParticipants'] = 'Le nombre maximum de participants doit être entre 1 et 10000';
        }

        if ($this->price && $this->price < 0) {
            $errors['price'] = 'Le prix ne peut pas être négatif';
        }

        if ($this->registrationDeadline) {
            $deadlineDate = DateTime::createFromFormat('Y-m-d\TH:i', $this->registrationDeadline);
            if (!$deadlineDate) {
                $deadlineDate = DateTime::createFromFormat('Y-m-d H:i:s', $this->registrationDeadline);
            }

            $startDate = DateTime::createFromFormat('Y-m-d\TH:i', $this->startDate);
            if (!$startDate) {
                $startDate = DateTime::createFromFormat('Y-m-d H:i:s', $this->startDate);
            }

            if ($deadlineDate && $startDate && $deadlineDate > $startDate) {
                $errors['registrationDeadline'] = 'La date limite d\'inscription doit être antérieure à la date de début';
            }
        }

        if ($this->endDate && $this->startDate) {
            $endDate = DateTime::createFromFormat('Y-m-d\TH:i', $this->endDate);
            if (!$endDate) {
                $endDate = DateTime::createFromFormat('Y-m-d H:i:s', $this->endDate);
            }

            $startDate = DateTime::createFromFormat('Y-m-d\TH:i', $this->startDate);
            if (!$startDate) {
                $startDate = DateTime::createFromFormat('Y-m-d H:i:s', $this->startDate);
            }

            if ($endDate && $startDate && $endDate < $startDate) {
                $errors['endDate'] = 'La date de fin doit être postérieure à la date de début';
            }
        }

        return $errors;
    }
}
