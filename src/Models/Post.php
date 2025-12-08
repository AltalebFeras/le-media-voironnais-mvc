<?php

namespace src\Models;

use src\Services\Hydration;

class Post
{
    use Hydration;

    private ?int $idPost = null;
    private ?string $uiid = null;
    private ?string $title = null;
    private ?string $content = null;
    private ?string $imagePath = null;
    private ?int $idUser = null;
    private ?int $idAssociation = null;
    private ?int $idEntreprise = null;
    private ?string $authorType = null; // 'user', 'association', 'entreprise'
    private ?bool $isPublished = false;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    // Getters
    public function getIdPost(): ?int { return $this->idPost; }
    public function getUiid(): ?string { return $this->uiid; }
    public function getTitle(): ?string { return $this->title; }
    public function getContent(): ?string { return $this->content; }
    public function getImagePath(): ?string { return $this->imagePath; }
    public function getIdUser(): ?int { return $this->idUser; }
    public function getIdAssociation(): ?int { return $this->idAssociation; }
    public function getIdEntreprise(): ?int { return $this->idEntreprise; }
    public function getAuthorType(): ?string { return $this->authorType; }
    public function getIsPublished(): ?bool { return $this->isPublished; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    // Setters
    public function setIdPost(?int $idPost): self { $this->idPost = $idPost; return $this; }
    public function setUiid(?string $uiid): self { $this->uiid = $uiid; return $this; }
    public function setTitle(?string $title): self { $this->title = $title; return $this; }
    public function setContent(?string $content): self { $this->content = $content; return $this; }
    public function setImagePath(?string $imagePath): self { $this->imagePath = $imagePath; return $this; }
    public function setIdUser(?int $idUser): self { $this->idUser = $idUser; return $this; }
    public function setIdAssociation(?int $idAssociation): self { $this->idAssociation = $idAssociation; return $this; }
    public function setIdEntreprise(?int $idEntreprise): self { $this->idEntreprise = $idEntreprise; return $this; }
    public function setAuthorType(?string $authorType): self { $this->authorType = $authorType; return $this; }
    public function setIsPublished(?bool $isPublished): self { $this->isPublished = $isPublished; return $this; }
    public function setCreatedAt(?string $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function setUpdatedAt(?string $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->title) || strlen($this->title) < 3) {
            $errors['title'] = "Le titre doit contenir au moins 3 caractères";
        }

        if (empty($this->content) || strlen($this->content) < 10) {
            $errors['content'] = "Le contenu doit contenir au moins 10 caractères";
        }

        if (empty($this->authorType) || !in_array($this->authorType, ['user', 'association', 'entreprise'])) {
            $errors['authorType'] = "Type d'auteur invalide";
        }

        return $errors;
    }
}
