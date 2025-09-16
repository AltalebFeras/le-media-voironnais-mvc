<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;
use src\Models\Association;
use src\Repositories\AssociationRepository;
use Exception;
use DateTime;

class AssociationController extends AbstractController
{
    private $repo;

    public function __construct()
    {
        $this->repo = new AssociationRepository();
    }

    /**
     * Display list of user associations
     */
    public function mesAssociations()
    {

        try {
            $idUser = $_SESSION['idUser'];
            $associations = $this->repo->getUserAssociations($idUser);
            $this->render('association/mes_associations', [
                'associations' => $associations,
                'total' => count($associations)
            ]);
        } catch (Exception $e) {
            $this->render('error', [
                'message' => $e->getMessage(),
                'title' => 'Erreur'
            ]);
        }
    }

    public function displayAssociationDetails()
    {
        try {
        $idUser = $_SESSION['idUser'];
        $idAssociation = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $errors = [];
        $associationRepository = new AssociationRepository();
        $association = $associationRepository->findAssociationById($idAssociation);

        // Check if association exists and user has access to it
        if (!$association) {
            $errors['association'] = "L'association demandée n'existe pas";
        }

        // Check if user is owner or member of the association, or if association is public
        $isOwner = $association->getIdUser() == $idUser;
        $members = $this->repo->getAssociationMembers($idAssociation);

        if (!$isOwner) {
            $errors['access'] = "Vous n'avez pas accès à cette association";
        }
        $this->returnAllErrors($errors, 'association/mes-associations?error=true');

        $this->render('association/voir_association', [
            'association' => $association,
            'isOwner' => $isOwner,
            'isMember' => $isMember
        ]);
        } catch (Exception $e) {
            $this->render('error', [
                'message' => $e->getMessage(),
                'title' => 'Erreur'
            ]);
        }
    }
    public function getVilles()
    {
        try {
            header('Content-Type: application/json');
            
            $cp = isset($_POST['cp']) ? htmlspecialchars(trim($_POST['cp'])) : null;
            if (!$cp) {
                throw new Exception("Le code postal est requis");
            }
            
            $villes = $this->repo->getVillesByCp($cp);
            echo json_encode($villes);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);    
        }
    }
    public function showAddForm()
    {
        $this->render('association/ajouter');
    }

    public function addAssociation()
    {

        try {
            $idUser = $_SESSION['idUser'];
            $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : null;
            $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : null;
            $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : null;
            $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : null;
            $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : null;
            $website = isset($_POST['website']) ? htmlspecialchars(trim($_POST['website'])) : null;
            $idVille = isset($_POST['idVille']) ? (int)$_POST['idVille'] : null;

            if (empty($name)) {
                throw new Exception("Le nom de l'association est obligatoire");
            }

            // Create new association
            $association = new Association();
            $association->setName($name)
                ->setDescription($description)
                ->setAddress($address)
                ->setPhone($phone)
                ->setEmail($email)
                ->setWebsite($website)
                ->setIsActive(1)
                ->setIdUser($idUser)
                ->setIdVille($idVille)
                ->setCreatedAt((new DateTime())->format('Y-m-d H:i:s'));

            // Handle logo upload if present
            if (!empty($_FILES['logo']['name'])) {
                $logoPath = $this->handleImageUpload('logo', 'associations/logos');
                $association->setLogoPath($logoPath);
            }

            // Handle banner upload if present
            if (!empty($_FILES['banner']['name'])) {
                $bannerPath = $this->handleImageUpload('banner', 'associations/banners');
                $association->setBannerPath($bannerPath);
            }

            $this->repo->createAssociation($association);

            $_SESSION['success'] = "L'association a été créée avec succès";
            $this->redirect('/mes-associations');
        } catch (Exception $e) {
            $this->render('association/ajouter', [
                'error' => $e->getMessage(),
                'title' => 'Ajouter une association',
                'formData' => $_POST
            ]);
        }
    }

    /**
     * Show form to edit an association
     */
    public function showEditForm()
    {
        try {
            $idAssociation = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $idUser = $_SESSION['idUser'];
            $association = $this->repo->getAssociationById($idAssociation);

            if (!$association) {
                throw new Exception("L'association demandée n'existe pas");
            }

            // Check if user is the owner of the association
            if ($association->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette association");
            }

            $this->render('association/modifier', [
                'association' => $association,
                'title' => 'Modifier l\'association'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/mes-associations');
        }
    }

    /**
     * Process edit association form
     */
    public function updateAssociation()
    {

        try {
            $idUser = $_SESSION['idUser'];
            $association = $this->repo->getAssociationById($idAssociation);

            if (!$association) {
                throw new Exception("L'association demandée n'existe pas");
            }

            // Check if user is the owner of the association
            if ($association->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette association");
            }

            // Validate form data
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
            $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_URL);
            $isActive = isset($_POST['isActive']) ? 1 : 0;

            if (empty($name)) {
                throw new Exception("Le nom de l'association est obligatoire");
            }

            // Update association
            $association->setName($name)
                ->setDescription($description)
                ->setAddress($address)
                ->setPhone($phone)
                ->setEmail($email)
                ->setWebsite($website)
                ->setIsActive($isActive)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            // Handle logo upload if present
            if (!empty($_FILES['logo']['name'])) {
                $logoPath = $this->handleImageUpload('logo', 'associations/logos');
                $association->setLogoPath($logoPath);
                $this->repo->updateLogo($idAssociation, $logoPath);
            }

            // Handle banner upload if present
            if (!empty($_FILES['banner']['name'])) {
                $bannerPath = $this->handleImageUpload('banner', 'associations/banners');
                $association->setBannerPath($bannerPath);
                $this->repo->updateBanner($idAssociation, $bannerPath);
            }

            $this->repo->updateAssociation($association);

            $_SESSION['success'] = "L'association a été mise à jour avec succès";
            $this->redirect('/mes-associations');
        } catch (Exception $e) {
            $this->render('association/modifier', [
                'association' => $association,
                'error' => $e->getMessage(),
                'title' => 'Modifier l\'association'
            ]);
        }
    }

    /**
     * Delete an association
     */
    public function deleteAssociation()
    {

        try {
            $idUser = $_SESSION['idUser'];

            // Check if user is the owner of the association
            if (!$this->repo->isAssociationOwner($idAssociation, $idUser)) {
                throw new Exception("Vous n'avez pas l'autorisation de supprimer cette association");
            }

            $this->repo->deleteAssociation($idAssociation);

            $_SESSION['success'] = "L'association a été supprimée avec succès";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('/mes-associations');
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
