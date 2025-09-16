<?php

namespace src\Models;

class Association
{
    private $idAssociation;
    private $name;
    private $description;
    private $logoPath;
    private $bannerPath;
    private $address;
    private $phone;
    private $email;
    private $website;
    private $isActive;
    private $idUser;
    private $createdAt;
    private $updatedAt;

    // Getters
    public function getIdAssociation()
    {
        return $this->idAssociation;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getLogoPath()
    {
        return $this->logoPath;
    }
    public function getBannerPath()
    {
        return $this->bannerPath;
    }
    public function getAddress()
    {
        return $this->address;
    }
    public function getPhone()
    {
        return $this->phone;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getWebsite()
    {
        return $this->website;
    }
    public function getIsActive()
    {
        return $this->isActive;
    }
    public function getIdUser()
    {
        return $this->idUser;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    // Setters
    public function setIdAssociation($idAssociation)
    {
        $this->idAssociation = $idAssociation;
        return $this;
    }
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    public function setLogoPath($logoPath)
    {
        $this->logoPath = $logoPath;
        return $this;
    }
    public function setBannerPath($bannerPath)
    {
        $this->bannerPath = $bannerPath;
        return $this;
    }
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
        return $this;
    }
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
