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

    public function mesEntreprises()
    {

        try {
            $idUser = $_SESSION['idUser'];
            $entreprises = $this->repo->getUserEntreprises($idUser);

            $this->render('entreprise/mes_entreprises', [
                'entreprises' => $entreprises,
                'title' => 'Mes entreprises'
            ]);
        } catch (Exception $e) {
            $this->render('dashboard', [
                'message' => $e->getMessage(),
                'title' => 'Erreur'
            ]);
        }
    }

    public function showAddForm()
    {
        $this->render('entreprise/ajouter_entreprise');
    }
    public function addEntreprise()
    {
        try {
            $idUser = $_SESSION['idUser'];

            // Validate form data
            $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : null;
            $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : null;
            $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : null;
            $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : null;
            $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : null;
            $website = isset($_POST['website']) ? htmlspecialchars(trim($_POST['website'])) : null;
            $siret = isset($_POST['siret']) ? htmlspecialchars(trim($_POST['siret'])) : null;
            $status = 'brouillon'; // Default status
            $errors = [];
            $_SESSION['form_data'] = $_POST;

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
            // Use model validation
            $modelErrors = $entreprise->validate();
            $errors = array_merge($errors, $modelErrors);
            $this->returnAllErrors($errors, 'entreprise/ajouter_entreprise?error=true');

            $this->repo->createEntreprise($entreprise);

            $_SESSION['success'] = "L'entreprise a été créée avec succès";
            $this->redirect('mes_entreprises');
        } catch (Exception $e) {
            $this->render('entreprise/ajouter_entreprise?error=true');
        }
    }

    /**
     * Show form to edit a company
     */
    public function showEditForm()
    {

        try {
            $idUser = $_SESSION['idUser'];
            $idEntreprise = isset($_GET['id']) ? htmlspecialchars(trim($_GET['id'])) : null;
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);

            if (!$entreprise || !$idEntreprise) {
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
            $idUser = $_SESSION['idUser'];
            $idEntreprise = isset($_GET['id']) ? htmlspecialchars(trim($_GET['id'])) : null;
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);

            if (!$entreprise || !$idEntreprise) {
                throw new Exception("L'entreprise demandée n'existe pas");
            }

            // Check if user is the owner of the company
            if ($entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette entreprise");
            }

            // Validate form data
            $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : null;
            $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : null;
            $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : null;
            $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : null;
            $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : null;
            $website = isset($_POST['website']) ? htmlspecialchars(trim($_POST['website'])) : null;
            $siret = isset($_POST['siret']) ? htmlspecialchars(trim($_POST['siret'])) : null;
            $status = 'brouillon'; // Default status

            $errors = [];
            $_SESSION['form_data'] = $_POST;
            // Update company
            $entreprise->setName($name)
                ->setDescription($description)
                ->setAddress($address)
                ->setPhone($phone)
                ->setEmail($email)
                ->setWebsite($website)
                ->setSiret($siret)
                ->setStatus($status)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            // Handle logo upload if present
            if (!empty($_FILES['logo']['name'])) {
                $logoPath = $this->handleImageUpload('logo', 'logos');
                $entreprise->setLogoPath($logoPath);
                $this->repo->updateLogo($idEntreprise, $logoPath);
            }

            // Handle banner upload if present
            if (!empty($_FILES['banner']['name'])) {
                $bannerPath = $this->handleImageUpload('banner', 'banners');
                $entreprise->setBannerPath($bannerPath);
                $this->repo->updateBanner($idEntreprise, $bannerPath);
            }

            $this->repo->updateEntreprise($entreprise);

            $_SESSION['success'] = "L'entreprise a été mise à jour avec succès";
            $this->redirect('mes_entreprises');
        } catch (Exception $e) {
            $this->redirect('entreprise/modifier?id=' . $idEntreprise);
        }
    }

    /**
     * Delete a company
     */
    public function deleteEntreprise()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $idEntreprise = isset($_GET['id']) ? htmlspecialchars(trim($_GET['id'])) : null;
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);

            if (!$entreprise || !$idEntreprise) {
                throw new Exception("L'entreprise demandée n'existe pas");
            }

            // Check if user is the owner of the company
            if ($entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de supprimer cette entreprise");
            }

            $this->repo->deleteEntreprise($idEntreprise);

            $_SESSION['success'] = "L'entreprise a été supprimée avec succès";
            $this->redirect('mes_entreprises');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_entreprises');
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
