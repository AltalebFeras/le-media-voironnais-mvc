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
            $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $associationsPerPage = 2;
            $associations = $this->repo->getUserAssociations($idUser, $currentPage, $associationsPerPage);
            $totalAssociations = $this->repo->countUserAssociations($idUser);
            $totalPages = (int)ceil($totalAssociations / $associationsPerPage);

            $this->render('association/mes_associations', [
                'associations' => $associations,
                'total' => $totalAssociations,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages
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
            $association = $this->repo->getAssociationById($idAssociation);

            if (!$association) {
                $errors['association'] = "L'association demandée n'existe pas";
            }

            $isOwner = $association ? $association->getIdUser() == $idUser : false;
            $members = [];
            $ville = null;

            if ($association) {
                $members = $this->repo->getAssociationMembers($idAssociation);
                $ville = $this->repo->getVilleById($association->getIdVille());

                // Check if user has access (owner or member or public association)
                $isMember = false;
                foreach ($members as $member) {
                    if ($member['idUser'] == $idUser) {
                        $isMember = true;
                        break;
                    }
                }

                if (!$isOwner && !$isMember && !$association->getIsPublic()) {
                    $errors['access'] = "Vous n'avez pas accès à cette association";
                }
            }

            $this->returnAllErrors($errors, 'mes_associations?error=true');

            $this->render('association/voir_association', [
                'association' => $association,
                'members' => $members,
                'ville' => $ville,
                'isOwner' => $isOwner,
                'isMember' => $isMember ?? false
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_associations');
        }
    }
    public function getVilles()
    {
        try {
            header('Content-Type: application/json');

            // Get the raw JSON input
            $input = json_decode(file_get_contents('php://input'), true);

            $codePostal = isset($input['codePostal']) ? htmlspecialchars(trim($input['codePostal'])) : null;
            if (!$codePostal) {
                throw new Exception("Le code postal est requis");
            }

            $villes = $this->repo->getVillesByCp($codePostal);
            echo json_encode(['succes' => true, 'data' => $villes]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    public function showAddForm()
    {
        $this->render('association/ajouter_association');
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

            $_SESSION['form_data'] = $_POST;
            $errors = [];

            // Custom validations not covered by the model

            $existingAssociation = $this->repo->getAssociationByNameForThisUser($name, $idUser);
            if ($existingAssociation) {
                $errors['name'] = "Vous avez déjà une association avec ce nom";
            }
            $existingVille = $this->repo->isVilleExists($idVille);
            if (!$existingVille) {
                $errors['idVille'] = "La ville sélectionnée est invalide";
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
                ->setLogoPath(null)
                ->setBannerPath(null)
                ->setIdUser($idUser)
                ->setIdVille($idVille)
                ->setCreatedAt((new DateTime())->format('Y-m-d H:i:s'));

            // Use model validation
            $modelErrors = $association->validate();
            $errors = array_merge($errors, $modelErrors);

            // Handle logo upload if present
            if (!empty($_FILES['logo']['name'])) {
                $logoPath = $this->handleImageUpload('logo', 'logos');
                $association->setLogoPath($logoPath);
            }

            // Handle banner upload if present
            if (!empty($_FILES['banner']['name'])) {
                $bannerPath = $this->handleImageUpload('banner', 'banners');
                $association->setBannerPath($bannerPath);
            }

            $this->returnAllErrors($errors, 'association/ajouter?error=true&');

            $create_association = $this->repo->createAssociation($association);
            if ($create_association) {
                $_SESSION['success'] = "L'association a été créée avec succès";
                $this->redirect('mes_associations');
            } else {
                throw new Exception("Une erreur est survenue lors de la création de l'association");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->render('association/ajouter', [
                'title' => 'Ajouter une association',
                'form_data' => $_POST
            ]);
        }
    }

    /**
     * Show form to edit an association
     */
    public function showEditForm()
    {
        try {
            // Handle URL segments for /association/modifier?id=
            $idAssociation = isset($_GET['id']) ? (int)$_GET['id'] : null;


            $idUser = $_SESSION['idUser'];
            $association = $this->repo->getAssociationById($idAssociation);

            if (!$association || !$idAssociation) {
                throw new Exception("L'association demandée n'existe pas");
            }

            if ($association->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette association");
            }

            $ville = $this->repo->getVilleById($association->getIdVille());

            $this->render('association/modifier_association', [
                'association' => $association,
                'ville' => $ville,
                'title' => 'Modifier l\'association'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_associations');
        }
    }

    /**
     * Process edit association form
     */
    public function updateAssociation()
    {
        try {
            $idAssociation = null;
            $idAssociation = isset($_GET['id']) ? (int)$_GET['id'] : null;

            if (!$idAssociation) {
                throw new Exception("ID d'association invalide");
            }

            $idUser = $_SESSION['idUser'];
            $association = $this->repo->getAssociationById($idAssociation);

            if (!$association) {
                throw new Exception("L'association demandée n'existe pas");
            }

            if ($association->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette association");
            }

            // Get form data with same validation as addAssociation
            $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : null;
            $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : null;
            $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : null;
            $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : null;
            $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : null;
            $website = isset($_POST['website']) ? htmlspecialchars(trim($_POST['website'])) : null;
            $idVille = isset($_POST['idVille']) ? (int)$_POST['idVille'] : null;
            $isActive = isset($_POST['isActive']) ? 1 : 0;

            $errors = [];


            // Check if name exists for other associations of this user
            $existingAssociation = $this->repo->getAssociationByNameForThisUser($name, $idUser);
            if ($existingAssociation && $existingAssociation->getIdAssociation() != $idAssociation) {
                $errors['name'] = "Vous avez déjà une association avec ce nom";
            }

            $existingVille = $this->repo->isVilleExists($idVille);
            if (!$existingVille) {
                $errors['idVille'] = "La ville sélectionnée est invalide";
            }

            // Update association data
            $association->setName($name)
                ->setDescription($description)
                ->setAddress($address)
                ->setPhone($phone)
                ->setEmail($email)
                ->setWebsite($website)
                ->setIsActive($isActive)
                ->setIdVille($idVille)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            // Use model validation
            $modelErrors = $association->validate();
            $errors = array_merge($errors, $modelErrors);

            // Handle logo upload if present
            if (!empty($_FILES['logo']['name'])) {
                $logoPath = $this->handleImageUpload('logo', 'logos');
                $association->setLogoPath($logoPath);
            }

            // Handle banner upload if present
            if (!empty($_FILES['banner']['name'])) {
                $bannerPath = $this->handleImageUpload('banner', 'banners');
                $association->setBannerPath($bannerPath);
            }

            $this->returnAllErrors($errors, 'association/modifier?id=' . $idAssociation . '?error=true');

            $this->repo->updateAssociation($association);

            $_SESSION['success'] = "L'association a été mise à jour avec succès";
            $this->redirect('mes_associations');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('association/modifier?id=' . ($idAssociation ?? ''));
        }
    }

    /**
     * Delete an association
     */
    public function deleteAssociation()
    {
        try {
            $idAssociation = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
            if (!$idAssociation) {
                throw new Exception("ID d'association invalide");
            }
            $idUser = $_SESSION['idUser'];

            if (!$this->repo->isAssociationOwner($idAssociation, $idUser)) {
                throw new Exception("Vous n'avez pas l'autorisation de supprimer cette association");
            }

            $this->repo->deleteAssociation($idAssociation, $idUser);
            $_SESSION['success'] = "L'association a été supprimée avec succès";
            $this->redirect('mes_associations');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_associations');
        }
    }



    /**
     * Handle image upload
     */
    public function handleImageUpload($fileInputName, $directory)
    {
        if (!isset($_FILES[$fileInputName]) || empty($_FILES[$fileInputName]['name'])) {
            throw new Exception("Aucun fichier n'a été sélectionné.");
        }

        // Handle upload errors
        if ($_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = 'Erreur lors du téléchargement de l\'image.';
            switch ($_FILES[$fileInputName]['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMsg = "Le fichier est trop volumineux. Limite serveur : " . ini_get('upload_max_filesize') . ".";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMsg = "Le fichier n'a été que partiellement téléchargé.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMsg = "Aucun fichier n'a été téléchargé.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errorMsg = "Dossier temporaire manquant.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errorMsg = "Échec de l'écriture du fichier sur le disque.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $errorMsg = "Une extension PHP a arrêté le téléchargement du fichier.";
                    break;
            }
            throw new Exception($errorMsg);
        }

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        $uploadDir = __DIR__ . "/../../public/uploads/{$directory}/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmpPath = $_FILES[$fileInputName]['tmp_name'];
        $fileNameOriginal = $_FILES[$fileInputName]['name'];
        $fileSize = $_FILES[$fileInputName]['size'];
        $fileType = mime_content_type($fileTmpPath);
        $fileExtension = strtolower(pathinfo($fileNameOriginal, PATHINFO_EXTENSION));

        // Check if file is actually uploaded
        if (!is_uploaded_file($fileTmpPath)) {
            throw new Exception('Le fichier n\'a pas été téléchargé correctement.');
        }

        // Validate file type and size
        if (!in_array($fileType, $allowedMimeTypes) || !in_array($fileExtension, $allowedExtensions)) {
            throw new Exception('Format de fichier non autorisé. Veuillez télécharger une image (jpg, jpeg, png, gif, webp).');
        }

        if ($fileSize > $maxFileSize) {
            throw new Exception('La taille de l\'image ne doit pas dépasser 5 Mo.');
        }

        $fileName = uniqid() . '_' . basename($fileNameOriginal);
        $uploadFile = "{$uploadDir}{$fileName}";

        // Handle EXIF orientation for JPEG images
        if ($fileExtension === 'jpg' || $fileExtension === 'jpeg') {
            $image = @imagecreatefromjpeg($fileTmpPath);
            if ($image && function_exists('exif_read_data')) {
                $exif = @exif_read_data($fileTmpPath);
                if (!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3:
                            $image = imagerotate($image, 180, 0);
                            break;
                        case 6:
                            $image = imagerotate($image, -90, 0);
                            break;
                        case 8:
                            $image = imagerotate($image, 90, 0);
                            break;
                    }
                }
            }
            if ($image) {
                imagejpeg($image, $uploadFile, 90);
                imagedestroy($image);
            } else {
                throw new Exception('Impossible de traiter l\'image JPEG.');
            }
        } else {
            // Move uploaded file for non-JPEG images
            if (!move_uploaded_file($fileTmpPath, $uploadFile)) {
                throw new Exception('Erreur lors du déplacement du fichier téléchargé.');
            }
        }

        return "/uploads/{$directory}/" . $fileName;
    }
}
