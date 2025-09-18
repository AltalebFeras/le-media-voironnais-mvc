<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;
use src\Models\Evenement;
use src\Repositories\EvenementRepository;
use Exception;
use DateTime;
use src\Services\Helper;

class EvenementController extends AbstractController
{
    private $repo;

    public function __construct()
    {
        $this->repo = new EvenementRepository();
    }

    /**
     * Display list of user events
     */
    public function mesEvenements()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $evenementsPerPage = 9;
            $evenements = $this->repo->getUserEvents($idUser, $currentPage, $evenementsPerPage);
            $totalEvenements = $this->repo->countUserEvents($idUser);
            $totalPages = (int)ceil($totalEvenements / $evenementsPerPage);

            $this->render('evenement/mes_evenements', [
                'evenements' => $evenements,
                'title' => 'Mes événements',
                'total' => $totalEvenements,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages
            ]);
        } catch (Exception $e) {
            $this->redirect('mes_evenements?error=true');
        }
    }

    /**
     * Display event details
     */
    public function displayEventDetails()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $idEvenement = isset($_GET['id']) ? htmlspecialchars(trim($_GET['id'])) : null;
            $evenement = $this->repo->getEventById($idEvenement);

            if (!$evenement || !$idEvenement) {
                throw new Exception("L'événement demandé n'existe pas");
            }

            // Check if user is the owner of the event
            if ($evenement->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de voir cet événement");
            }

            $ville = $this->repo->getVilleById($evenement->getIdVille());

            $this->render('evenement/voir_evenement', [
                'evenement' => $evenement,
                'ville' => $ville,
                'title' => 'Détails de l\'événement',
                'isOwner' => true
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_evenements?error=true');
        }
    }

    /**
     * Show form to add event
     */
    public function showAddEventForm()
    {
        $idUser = $_SESSION['idUser'];
        $categories = $this->repo->getEventCategories();
        $associations = $this->repo->getUserAssociations($idUser);
        $entreprises = $this->repo->getUserEntreprise($idUser);

        $this->render('evenement/ajouter_evenement', [
            'categories' => $categories,
            'associations' => $associations,
            'entreprises' => $entreprises
        ]);
    }

    /**
     * Process add event form
     */
    public function addEvent()
    {
        try {
            $idUser = $_SESSION['idUser'];
            // Validate form data
            $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : null;
            $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : null;
            $shortDescription = isset($_POST['shortDescription']) ? htmlspecialchars(trim($_POST['shortDescription'])) : null;
            $startDate = isset($_POST['startDate']) ? htmlspecialchars(trim($_POST['startDate'])) : null;
            $endDate = isset($_POST['endDate']) ? htmlspecialchars(trim($_POST['endDate'])) : null;
            $registrationDeadline = isset($_POST['registrationDeadline']) ? htmlspecialchars(trim($_POST['registrationDeadline'])) : null;
            $maxParticipants = isset($_POST['maxParticipants']) ? (int)$_POST['maxParticipants'] : null;
            $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : null;
            $price = isset($_POST['price']) ? (float)$_POST['price'] : null;
            $currency = isset($_POST['currency']) ? htmlspecialchars(trim($_POST['currency'])) : 'EUR';
            $idVille = isset($_POST['idVille']) ? (int)$_POST['idVille'] : null;
            $idEventCategory = isset($_POST['idEventCategory']) ? (int)$_POST['idEventCategory'] : null;
            $idAssociation = isset($_POST['idAssociation']) && !empty($_POST['idAssociation']) ? (int)$_POST['idAssociation'] : null;
            $idEntreprise = isset($_POST['idEntreprise']) && !empty($_POST['idEntreprise']) ? (int)$_POST['idEntreprise'] : null;
            $isPublic = isset($_POST['isPublic']) ? 1 : 0;

            $errors = [];
            $_SESSION['form_data'] = $_POST;
            // Validate association ownership
            $userAssociations = $this->repo->getUserAssociations($idUser);
            $associationIds = array_map(function ($assoc) {
                return is_object($assoc) ? $assoc->getIdAssociation() : $assoc['idAssociation'];
            }, $userAssociations);
            if ($idAssociation && !in_array($idAssociation, $associationIds)) {
                $errors['idAssociation'] = "L'association sélectionnée est invalide";
            }
            // validation entreprise ownership
            $userEntreprises = $this->repo->getUserEntreprise($idUser);
            $entrepriseIds = array_map(function ($ent) {
                return is_object($ent) ? $ent->getIdEntreprise() : $ent['idEntreprise'];
            }, $userEntreprises);
            if ($idEntreprise && !in_array($idEntreprise, $entrepriseIds)) {
                $errors['idEntreprise'] = "L'entreprise sélectionnée est invalide";
            }
            // Ville and category existence
            $existingVille = $this->repo->isVilleExists($idVille);
            if (!$existingVille) {
                $errors['idVille'] = "La ville sélectionnée est invalide";
            }
            $existingCategory = $this->repo->isEventCategoryExists($idEventCategory);
            if (!$existingCategory) {
                $errors['idEventCategory'] = "La catégorie sélectionnée est invalide";
            }
            // verify if the title is not empty and is unique for the user only
            if (empty($title)) {
                $errors['title'] = "Le titre est requis";
            } elseif ($this->repo->isTitleExistsForUser($title, $idUser)) {
                $errors['title'] = "Vous avez déjà un événement avec ce titre";
            }

            $this->returnAllErrors($errors, 'evenement/ajouter?error=true');
            $helper = new Helper();

            // Create new event
            $evenement = new Evenement();
            $evenement->setTitle($title)
                ->setDescription($description)
                ->setShortDescription($shortDescription)
                ->setStartDate($startDate)
                ->setEndDate($endDate)
                ->setRegistrationDeadline($registrationDeadline)
                ->setMaxParticipants($maxParticipants)
                ->setCurrentParticipants(0)
                ->setAddress($address)
                ->setPrice($price)
                ->setCurrency($currency)
                ->setStatus('brouillon')
                ->setIsPublic($isPublic)
                ->setIsDeleted(false)
                ->setIdUser($idUser)
                ->setIdAssociation($idAssociation)
                ->setIdVille($idVille)
                ->setIdEventCategory($idEventCategory)
                ->setCreatedAt((new DateTime())->format('Y-m-d H:i:s'))
                ->setImagePath(null)
                ->setBannerPath(null);
            // Set default image and banner paths
            $bannerPath = DOMAIN . HOME_URL . 'assets/images/uploads/banners/default_banner.png';
            $evenement->setBannerPath($bannerPath);

            // Use model validation
            $modelErrors = $evenement->validate();
            $errors = array_merge($errors, $modelErrors);
            // Ensure unique slug
            $slug = $helper->generateSlug($title);
            $existSlug = $this->repo->isSlugExists($slug);

            if ($existSlug) {
                $suffix = 1;
                $finalSlug = "{$slug}-{$suffix}";
                while ($this->repo->isSlugExists($finalSlug)) {
                    $suffix++;
                    $finalSlug = "{$slug}-{$suffix}";
                }
                $slug = $finalSlug;
            }

            $evenement->setSlug($slug);

            $this->returnAllErrors($errors, 'evenement/ajouter?error=true');

            $this->repo->createEvent($evenement);

            $_SESSION['success'] = "L'événement a été créé avec succès";
            $this->redirect('mes_evenements');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->render('evenement/ajouter_evenement', [
                'title' => 'Ajouter un événement',
                'form_data' => $_POST,
                'associations' => $this->repo->getUserAssociations($idUser),
                'categories' => $this->repo->getEventCategories()
            ]);
        }
    }

    /**
     * Show form to edit an event
     */
    public function showEditEventForm()
    {
        try {
            $idEvenement = isset($_GET['id']) ? (int)$_GET['id'] : null;
            $idUser = $_SESSION['idUser'];
            $evenement = $this->repo->getEventById($idEvenement);

            if (!$evenement || !$idEvenement) {
                throw new Exception("L'événement demandé n'existe pas");
            }

            if ($evenement->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cet événement");
            }

            $categories = $this->repo->getEventCategories();
            $associations = $this->repo->getUserAssociations($idUser);
            $ville = $this->repo->getVilleById($evenement->getIdVille());

            $this->render('evenement/modifier_evenement', [
                'evenement' => $evenement,
                'categories' => $categories,
                'associations' => $associations,
                'ville' => $ville,
                'title' => 'Modifier l\'événement'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_evenements');
        }
    }

    /**
     * Process edit event form
     */
    public function updateEvent()
    {
        try {
            $idEvenement = isset($_GET['id']) ? (int)$_GET['id'] : null;

            if (!$idEvenement) {
                throw new Exception("ID d'événement invalide");
            }

            $idUser = $_SESSION['idUser'];
            $evenement = $this->repo->getEventById($idEvenement);

            if (!$evenement) {
                throw new Exception("L'événement demandé n'existe pas");
            }

            if ($evenement->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cet événement");
            }
            $originalTitle = $evenement->getTitle();

            // Get form data
            $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : null;
            $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : null;
            $shortDescription = isset($_POST['shortDescription']) ? htmlspecialchars(trim($_POST['shortDescription'])) : null;
            $startDate = isset($_POST['startDate']) ? htmlspecialchars(trim($_POST['startDate'])) : null;
            $endDate = isset($_POST['endDate']) ? htmlspecialchars(trim($_POST['endDate'])) : null;
            $registrationDeadline = isset($_POST['registrationDeadline']) ? htmlspecialchars(trim($_POST['registrationDeadline'])) : null;
            $maxParticipants = isset($_POST['maxParticipants']) ? (int)$_POST['maxParticipants'] : null;
            $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : null;
            $price = isset($_POST['price']) ? (float)$_POST['price'] : null;
            $currency = isset($_POST['currency']) ? htmlspecialchars(trim($_POST['currency'])) : 'EUR';
            $idVille = isset($_POST['idVille']) ? (int)$_POST['idVille'] : null;
            $idAssociation = isset($_POST['idAssociation']) && !empty($_POST['idAssociation']) ? (int)$_POST['idAssociation'] : null;
            $idEventCategory = isset($_POST['idEventCategory']) ? (int)$_POST['idEventCategory'] : null;
            $isPublic = isset($_POST['isPublic']) ? 1 : 0;
            $requiresApproval = isset($_POST['requiresApproval']) ? 1 : 0;

            $errors = [];

            $_SESSION['form_data'] = $_POST;

            // Validate association ownership
            $userAssociations = $this->repo->getUserAssociations($idUser);
            $associationIds = array_map(function ($assoc) {
                return is_object($assoc) ? $assoc->getIdAssociation() : $assoc['idAssociation'];
            }, $userAssociations);
            if ($idAssociation && !in_array($idAssociation, $associationIds)) {
                $errors['idAssociation'] = "L'association sélectionnée est invalide";
            }
            $existingVille = $this->repo->isVilleExists($idVille);
            if (!$existingVille) {
                $errors['idVille'] = "La ville sélectionnée est invalide";
            }
            $existingCategory = $this->repo->isEventCategoryExists($idEventCategory);
            if (!$existingCategory) {
                $errors['idEventCategory'] = "La catégorie sélectionnée est invalide";
            }
            // verify if the title is not empty and is unique for the user only (exclude current event)
            if (empty($title)) {
                $errors['title'] = "Le titre est requis";
            } elseif ($this->repo->isTitleExistsForUser($title, $idUser, $idEvenement)) {
                $errors['title'] = "Vous avez déjà un événement avec ce titre";
            }
            $this->returnAllErrors($errors, 'evenement/modifier?id=' . $idEvenement . '&error=true');

            // Update event data
            $evenement->setTitle($title)
                ->setDescription($description)
                ->setShortDescription($shortDescription)
                ->setStartDate($startDate)
                ->setEndDate($endDate)
                ->setRegistrationDeadline($registrationDeadline)
                ->setMaxParticipants($maxParticipants)
                ->setAddress($address)
                ->setPrice($price)
                ->setCurrency($currency)
                ->setStatus('brouillon')
                ->setIsPublic($isPublic)
                ->setRequiresApproval($requiresApproval)
                ->setIdVille($idVille)
                ->setIdEventCategory($idEventCategory)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $helper = new Helper();
            // Use model validation
            $modelErrors = $evenement->validate();
            $errors = array_merge($errors, $modelErrors);
            // Ensure unique slug
            // Regenerate slug if title changed
            if ($title !== $originalTitle) {
                // $evenement->setSlug(null); // Reset slug to force regeneration
                $slug = $helper->generateSlug($title);
                $existSlug = $this->repo->isSlugExists($slug);

                if ($existSlug) {
                    $suffix = 1;
                    $finalSlug = "{$slug}-{$suffix}";
                    while ($this->repo->isSlugExists($finalSlug)) {
                        $suffix++;
                        $finalSlug = "{$slug}-{$suffix}";
                    }
                    $slug = $finalSlug;
                }
                $evenement->setSlug($slug);
            }

            $this->returnAllErrors($errors, 'evenement/modifier?id=' . $idEvenement . '&error=true');

            $this->repo->updateEvent($evenement);

            $_SESSION['success'] = "L'événement a été mis à jour avec succès";
            $this->redirect('mes_evenements');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('evenement/modifier?id=' . ($idEvenement ?? ''));
        }
    }

    /**
     * Delete an event
     */
    public function deleteEvent()
    {
        try {
            $idEvenement = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
            if (!$idEvenement) {
                throw new Exception("ID d'événement invalide");
            }
            $idUser = $_SESSION['idUser'];

            if (!$this->repo->isEventOwner($idEvenement, $idUser)) {
                throw new Exception("Vous n'avez pas l'autorisation de supprimer cet événement");
            }

            $this->repo->deleteEvent($idEvenement);
            $_SESSION['success'] = "L'événement a été supprimé avec succès";
            $this->redirect('mes_evenements');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_evenements');
        }
    }

    /**
     * Get cities by postal code (AJAX)
     */
    public function getVilles()
    {
        try {
            header('Content-Type: application/json');

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
