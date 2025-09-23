<?php

namespace src\Models;

use DateTime;
use src\Services\Hydration;

class Realisation
{
    use Hydration;

    private int $idRealisation ;
    private string $uiid ;
    private string $title ;
    private string $slug ;
    private ?string $description ;
    private string $dateRealized ;
    private bool $isPublic;
    private bool $isFeatured ;
    private bool $isDeleted ;
    private int $idEntreprise ;
    private DateTime $createdAt ;
    private ?DateTime $updatedAt ;

    // Getters
    public function getIdRealisation(): ?int
    {
        return $this->idRealisation;
    }
    public function getUiid(): ?string
    {
        return $this->uiid;
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function getSlug(): ?string
    {
        return $this->slug;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getDateRealized(): ?string
    {
        return $this->dateRealized;
    }
    public function getIsPublic(): bool
    {
        return $this->isPublic;
    }
    public function getIsFeatured(): bool
    {
        return $this->isFeatured;
    }
    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }
    public function getIdEntreprise(): ?int
    {
        return $this->idEntreprise;
    }
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    // Setters
    public function setIdRealisation(?int $idRealisation): self
    {
        $this->idRealisation = $idRealisation;
        return $this;
    }

    public function setUiid(?string $uiid): self
    {
        $this->uiid = $uiid;
        return $this;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setDateRealized(?string $dateRealized): self
    {
        $this->dateRealized = $dateRealized;
        return $this;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function setIsFeatured(bool $isFeatured): self
    {
        $this->isFeatured = $isFeatured;
        return $this;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    public function setIdEntreprise(?int $idEntreprise): self
    {
        $this->idEntreprise = $idEntreprise;
        return $this;
    }

    public function setCreatedAt($createdAt): self
    {
        if ($createdAt instanceof DateTime) {
            $this->createdAt = $createdAt;
        } elseif (is_string($createdAt)) {
            $this->createdAt = new DateTime($createdAt);
        }
        return $this;
    }

    public function setUpdatedAt($updatedAt): self
    {
        if ($updatedAt instanceof DateTime) {
            $this->updatedAt = $updatedAt;
        } elseif (is_string($updatedAt)) {
            $this->updatedAt = new DateTime($updatedAt);
        }
        return $this;
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->title)) {
            $errors['title'] = "Le titre est requis";
        } elseif (strlen($this->title) < 3) {
            $errors['title'] = "Le titre doit contenir au moins 3 caractères";
        } elseif (strlen($this->title) > 180) {
            $errors['title'] = "Le titre ne peut pas dépasser 180 caractères";
        }

        if (!empty($this->description) && strlen($this->description) > 5000) {
            $errors['description'] = "La description ne peut pas dépasser 5000 caractères";
        }

        if (!empty($this->dateRealized)) {
            $date = DateTime::createFromFormat('Y-m-d', $this->dateRealized);
            if (!$date || $date->format('Y-m-d') !== $this->dateRealized) {
                $errors['dateRealized'] = "Format de date invalide";
            }
        }

        return $errors;
    }

    /**
     * Format createdAt for display
     */
    public function getFormattedCreatedAt(): string
    {
        if ($this->createdAt) {
            return $this->createdAt->format('d/m/Y à H:i');
        }
        return '';
    }

    /**
     * Format updatedAt for display
     */
    public function getFormattedUpdatedAt(): string
    {
        if ($this->updatedAt) {
            return $this->updatedAt->format('d/m/Y à H:i');
        }
        return '';
    }

    /**
     * Format dateRealized for display
     */
    public function getFormattedDateRealized(): string
    {
        if ($this->dateRealized) {
            $date = DateTime::createFromFormat('Y-m-d', $this->dateRealized);
            if ($date) {
                return $date->format('d/m/Y');
            }
        }
        return '';
    }

    /**
     * Get truncated description for display
     */
    public function getDisplayDescription(int $maxLength = 150): string
    {
        if (!empty($this->description)) {
            return strlen($this->description) > $maxLength
                ? substr($this->description, 0, $maxLength) . '...'
                : $this->description;
        }

        return '';
    }
}
