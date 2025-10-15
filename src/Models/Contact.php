<?php

namespace src\Models;

use DateTime;

class Contact
{
    private ?int $idContact = null;
    private ?string $firstName = null;
    private ?string $lastName = null;
    private ?string $email = null;
    private ?string $phone = null;
    private ?string $subject = null;
    private ?string $message = null;
    private ?string $status = 'nouveau';
    private ?string $response = null;
    private ?string $createdAt = null;
    private ?string $repliedAt = null;
    private ?string $uiid = null;

    // Getters
    public function getIdContact(): ?int
    {
        return $this->idContact;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getRepliedAt(): ?string
    {
        return $this->repliedAt;
    }

    public function getUiid(): ?string
    {
        return $this->uiid;
    }

    // Setters
    public function setIdContact(?int $idContact): self
    {
        $this->idContact = $idContact;
        return $this;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setResponse(?string $response): self
    {
        $this->response = $response;
        return $this;
    }

    public function setCreatedAt(?string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setRepliedAt(?string $repliedAt): self
    {
        $this->repliedAt = $repliedAt;
        return $this;
    }

    public function setUiid(?string $uiid): self
    {
        $this->uiid = $uiid;
        return $this;
    }

    // Validation method
    public function validate(): array
    {
        $errors = [];

        // First name validation
        if (empty($this->firstName)) {
            $errors['firstName'] = "Le prénom est requis";
        } elseif (strlen($this->firstName) < 2 || strlen($this->firstName) > 50) {
            $errors['firstName'] = "Le prénom doit contenir entre 2 et 50 caractères";
        }

        // Last name validation
        if (empty($this->lastName)) {
            $errors['lastName'] = "Le nom est requis";
        } elseif (strlen($this->lastName) < 2 || strlen($this->lastName) > 50) {
            $errors['lastName'] = "Le nom doit contenir entre 2 et 50 caractères";
        }

        // Email validation
        if (empty($this->email)) {
            $errors['email'] = "L'email est requis";
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "L'email n'est pas valide";
        } elseif (strlen($this->email) > 255) {
            $errors['email'] = "L'email ne peut pas dépasser 255 caractères";
        }

        // Phone validation (optional)
        if (!empty($this->phone)) {
            if (!preg_match('/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/', $this->phone)) {
                $errors['phone'] = "Le numéro de téléphone n'est pas au format français valide";
            }
        }

        // Subject validation
        if (empty($this->subject)) {
            $errors['subject'] = "Le sujet est requis";
        } elseif (strlen($this->subject) < 5 || strlen($this->subject) > 200) {
            $errors['subject'] = "Le sujet doit contenir entre 5 et 200 caractères";
        }

        // Message validation
        if (empty($this->message)) {
            $errors['message'] = "Le message est requis";
        } elseif (strlen($this->message) < 10 || strlen($this->message) > 2000) {
            $errors['message'] = "Le message doit contenir entre 10 et 2000 caractères";
        }

        // Status validation
        $validStatuses = ['nouveau', 'lu', 'traite', 'archive'];
        if (!in_array($this->status, $validStatuses)) {
            $errors['status'] = "Le statut doit être l'un des suivants : " . implode(', ', $validStatuses);
        }

        return $errors;
    }
}
