<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;
use src\Models\Entreprise;
use src\Repositories\EntrepriseRepository;
use Exception;
use DateTime;

class EntrepriseController extends AbstractController
{
    private $repo;

    public function __construct()
    {
        $this->repo = new EntrepriseRepository();
    }

    /**
     * Display list of user companies
     */
    public function mesEntreprises()
    {
        
        try {
            $idUser = $_SESSION['user_id'];
            $entreprises = $this->repo->getUserEntreprises($idUser);
            
            $this->render('entreprise/mes-entreprises', [
                'entreprises' => $entreprises,
                'title' => 'Mes entreprises'
            ]);
        } catch (Exception $e) {
            $this->render('error', [
                'message' => $e->getMessage(),
                'title' => 'Erreur'
            ]);
        }
    }

    /**
     * Show form to add a new company
     */
    public function showAddForm()
    {
        
        $this->render('entreprise/ajouter', [
            'title' => 'Ajouter une entreprise'
        ]);
    }

    /**
     * Process add company form
     */
    public function addEntreprise()
    {
        
        try {
            $idUser = $_SESSION['user_id'];
            
            // Validate form data
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
            $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_URL);
            $siret = filter_input(INPUT_POST, 'siret', FILTER_SANITIZE_SPECIAL_CHARS);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
            
            if (empty($name)) {
                throw new Exception("Le nom de l'entreprise est obligatoire");
            }
            
            // Default status if not provided
            if (empty($status)) {
                $status = 'brouillon';
            }
            
            // Create new company
            $entreprise = new Entreprise();
            $entreprise->setName($name)
                ->setDescription($description)
                ->setAddress($address)
                ->setPhone($phone)
                ->setEmail($email)
                ->setWebsite($website)
                ->setSiret($siret)
                ->setStatus($status)
                ->setIsActive(0) // Default to inactive
                ->setIdUser($idUser)
                ->setCreatedAt((new DateTime())->format('Y-m-d H:i:s'));
            
            // Handle logo upload if present
            if (!empty($_FILES['logo']['name'])) {
                $logoPath = $this->handleImageUpload('logo', 'entreprises/logos');
                $entreprise->setLogoPath($logoPath);
            }
            
            // Handle banner upload if present
            if (!empty($_FILES['banner']['name'])) {
                $bannerPath = $this->handleImageUpload('banner', 'entreprises/banners');
                $entreprise->setBannerPath($bannerPath);
            }
            
            $this->repo->createEntreprise($entreprise);
            
            $_SESSION['success'] = "L'entreprise a été créée avec succès";
            $this->redirect('/mes-entreprises');
        } catch (Exception $e) {
            $this->render('entreprise/ajouter', [
                'error' => $e->getMessage(),
                'title' => 'Ajouter une entreprise',
                'formData' => $_POST
            ]);
        }
    }

    /**
     * Show form to edit a company
     */
    public function showEditForm()
    {
        
        try {
            $idUser = $_SESSION['user_id'];
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);
            
            if (!$entreprise) {
                throw new Exception("L'entreprise demandée n'existe pas");
            }
            
            // Check if user is the owner of the company
            if ($entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette entreprise");
            }
            
            $this->render('entreprise/modifier', [
                'entreprise' => $entreprise,
                'title' => 'Modifier l\'entreprise'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/mes-entreprises');
        }
    }

    /**
     * Process edit company form
     */
    public function updateEntreprise()
    {
        
        try {
            $idUser = $_SESSION['user_id'];
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);
            
            if (!$entreprise) {
                throw new Exception("L'entreprise demandée n'existe pas");
            }
            
            // Check if user is the owner of the company
            if ($entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette entreprise");
            }
            
            // Validate form data
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
            $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_URL);
            $siret = filter_input(INPUT_POST, 'siret', FILTER_SANITIZE_SPECIAL_CHARS);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
            $isActive = isset($_POST['isActive']) ? 1 : 0;
            
            if (empty($name)) {
                throw new Exception("Le nom de l'entreprise est obligatoire");
            }
            
            // Update company
            $entreprise->setName($name)
                ->setDescription($description)
                ->setAddress($address)
                ->setPhone($phone)
                ->setEmail($email)
                ->setWebsite($website)
                ->setSiret($siret)
                ->setStatus($status)
                ->setIsActive($isActive)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));
            
            // Handle logo upload if present
            if (!empty($_FILES['logo']['name'])) {
                $logoPath = $this->handleImageUpload('logo', 'entreprises/logos');
                $entreprise->setLogoPath($logoPath);
                $this->repo->updateLogo($idEntreprise, $logoPath);
            }
            
            // Handle banner upload if present
            if (!empty($_FILES['banner']['name'])) {
                $bannerPath = $this->handleImageUpload('banner', 'entreprises/banners');
                $entreprise->setBannerPath($bannerPath);
                $this->repo->updateBanner($idEntreprise, $bannerPath);
            }
            
            $this->repo->updateEntreprise($entreprise);
            
            $_SESSION['success'] = "L'entreprise a été mise à jour avec succès";
            $this->redirect('/mes-entreprises');
        } catch (Exception $e) {
            $this->render('entreprise/modifier', [
                'entreprise' => $entreprise,
                'error' => $e->getMessage(),
                'title' => 'Modifier l\'entreprise'
            ]);
        }
    }

    /**
     * Delete a company
     */
    public function deleteEntreprise()
    {
        
        try {
            $idUser = $_SESSION['user_id'];
            
            // Check if user is the owner of the company
            if (!$this->repo->isEntrepriseOwner($idEntreprise, $idUser)) {
                throw new Exception("Vous n'avez pas l'autorisation de supprimer cette entreprise");
            }
            
            $this->repo->deleteEntreprise($idEntreprise);
            
            $_SESSION['success'] = "L'entreprise a été supprimée avec succès";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        $this->redirect('/mes-entreprises');
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($fileInputName, $directory)
    {
        $targetDir = __DIR__ . "/../../public/uploads/{$directory}/";
        
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . basename($_FILES[$fileInputName]['name']);
        $targetFile = $targetDir . $fileName;
        
        // Check file type
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($imageFileType, $allowedTypes)) {
            throw new Exception("Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés");
        }
        
        // Check file size (5MB max)
        if ($_FILES[$fileInputName]['size'] > 5000000) {
            throw new Exception("Le fichier est trop volumineux (maximum 5MB)");
        }
        
        if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $targetFile)) {
            return "/uploads/{$directory}/" . $fileName;
        } else {
            throw new Exception("Une erreur est survenue lors de l'upload de l'image");
        }
    }
}
