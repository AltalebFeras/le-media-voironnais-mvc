<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;
use src\Models\Entreprise;
use src\Repositories\AssociationRepository;
use src\Repositories\EntrepriseRepository;
use Exception;
use DateTime;
use src\Services\Helper;
use src\Services\Mail;

class EntrepriseController extends AbstractController
{
    private $repo;
    private $AssocRepo = null;

    public function __construct()
    {
        $this->repo = new EntrepriseRepository();
        $this->AssocRepo = new AssociationRepository();
    }
    private function getId(): mixed
    {
        $uiid =  isset($_GET['uiid']) ? htmlspecialchars(trim($_GET['uiid'])) : null;
        return $this->repo->getIdByUiid($uiid);
    }
    public function mesEntreprises(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $entreprisesPerPage = 6;
            $entreprises = $this->repo->getUserEntreprises($idUser, $currentPage, $entreprisesPerPage);
            $totalEntreprises = $this->repo->countUserEntreprises($idUser);
            $totalPages = (int)ceil($totalEntreprises / $entreprisesPerPage);

            $this->render('entreprise/mes_entreprises', [
                'entreprises' => $entreprises,
                'title' => 'Mes entreprises',
                'total' => $totalEntreprises,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages
            ]);
        } catch (Exception $e) {
            $this->redirect('mes_entreprises?error=true');
        }
    }
    public function displayEntrepriseDetails(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $idEntreprise = $this->getId();
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);
            $realisation = $this->repo->getAllRealisationByEntrepriseId($idEntreprise);
            $ville = $this->AssocRepo->getVilleById($entreprise->getIdVille());

            if (!$entreprise || !$idEntreprise) {
                throw new Exception("L'entreprise demandée n'existe pas");
            }

            // Check if user is the owner of the company
            if ($entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de voir cette entreprise");
            }

            // Calculate total realisations
            $totalRealisations = is_array($realisation) ? count($realisation) : 0;

            $this->render('entreprise/voir_entreprise', [
                'entreprise' => $entreprise,
                'ville' => $ville,
                'realisation' => $realisation,
                'totalRealisations' => $totalRealisations,
                'title' => 'Détails de l\'entreprise',
                'isOwner' => true
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_entreprises?error=true');
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
            $idVille = isset($_POST['idVille']) ? (int)$_POST['idVille'] : null;
            $errors = [];
            $_SESSION['form_data'] = $_POST;

            $existingEntreprise = $this->repo->getEntrepriseByName($name);
            if ($existingEntreprise) {
                $errors['name'] = "Une entreprise avec ce nom existe déjà.";
            }
            $nameVille = $this->AssocRepo->isVilleExists($idVille);
            if (!$nameVille) {
                $errors['idVille'] = "La ville sélectionnée est invalide";
            }
            // default paths for logo and banner
            $logoPath = 'assets/images/uploads/logos/default_logo.png';
            $bannerPath = 'assets/images/uploads/banners/default_banner.png';

            // Create new company
            $entreprise = new Entreprise();
            $entreprise->setName($name)
                ->setDescription($description)
                ->setAddress($address)
                ->setPhone($phone)
                ->setEmail($email)
                ->setWebsite($website)
                ->setSiret($siret)
                ->setIsActive(0) // Default to inactive
                ->setIdUser($idUser)
                ->setIdVille($idVille)
                ->setCreatedAt((new DateTime())->format('Y-m-d H:i:s'))
                ->setLogoPath($logoPath)
                ->setBannerPath($bannerPath);
            // Generate unique slug
            $helper = new Helper();
            $slug = $helper->generateSlug($nameVille, $name);
            $existSlug = $this->repo->isSlugExists($slug);
            $uiid = $helper->generateUiid();
            $entreprise->setUiid($uiid);

            if ($existSlug) {
                $suffix = 1;
                $finalSlug = "{$slug}-{$suffix}";
                while ($this->repo->isSlugExists($finalSlug)) {
                    $suffix++;
                    $finalSlug = "{$slug}-{$suffix}";
                }
                $slug = $finalSlug;
            }

            $entreprise->setSlug($slug);
            // Use model validation
            $modelErrors = $entreprise->validate();
            $errors = array_merge($errors, $modelErrors);
            $this->returnAllErrors($errors, 'entreprise/ajouter?error=true');

            $this->repo->createEntreprise($entreprise);

            $_SESSION['success'] = "L'entreprise a été créée avec succès";
            $this->redirect('mes_entreprises');
        } catch (Exception $e) {
            $this->render('entreprise/ajouter?error=true');
        }
    }

    /**
     * Show form to edit a company
     */
    public function showEditForm()
    {

        try {
            $idUser = $_SESSION['idUser'];
            $idEntreprise = $this->getId();
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);

            if (!$entreprise || !$idEntreprise) {
                throw new Exception("L'entreprise demandée n'existe pas");
            }

            // Check if user is the owner of the company
            if ($entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette entreprise");
            }
            $ville = $this->AssocRepo->getVilleById($entreprise->getIdVille());
            $this->render('entreprise/modifier_entreprise', [
                'entreprise' => $entreprise,
                'ville' => $ville,
                'title' => 'Modifier l\'entreprise'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_entreprises?error=true');
        }
    }

    /**
     * Process edit company form
     */
    public function updateEntreprise()
    {

        try {
            $idUser = $_SESSION['idUser'];
            $uiid = isset($_GET['uiid']) ? htmlspecialchars(trim($_GET['uiid'])) : null;
            $idEntreprise = $this->getId();
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
            $idVille = isset($_POST['idVille']) ? (int)$_POST['idVille'] : null;
            $siret = isset($_POST['siret']) ? htmlspecialchars(trim($_POST['siret'])) : null;

            $errors = [];
            $_SESSION['form_data'] = $_POST;
            // if the entreprise is active you can not edit the siret
            if ($entreprise->getIsActive() && $entreprise->getSiret() !== $siret) {
                $errors['siret'] = "Vous ne pouvez pas modifier le numéro SIRET d'une entreprise active! Veuillez contacter le support.";
            }
            $existingEntreprise = $this->repo->getEntrepriseByName($name);
            $originalName = $entreprise->getName();

            if ($existingEntreprise && $name === $existingEntreprise->getName() && $existingEntreprise->getIdEntreprise() != $idEntreprise) {
                $errors['name'] = "Une entreprise avec ce nom existe déjà.";
            }
            $nameVille = $this->AssocRepo->isVilleExists($idVille);
            if (!$nameVille) {
                $errors['idVille'] = "La ville sélectionnée est invalide";
            }

            // Update company
            $entreprise->setName($name)
                ->setDescription($description)
                ->setAddress($address)
                ->setPhone($phone)
                ->setEmail($email)
                ->setWebsite($website)
                ->setSiret($siret)
                ->setIdVille($idVille)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));


            // Regenerate slug if name changed - compare with original name
            if ($name !== $originalName) {
                $helper = new Helper();
                $slug = $helper->generateSlug($nameVille, $name);
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

                $entreprise->setSlug($slug);
            }

            // Use model validation
            $modelErrors = $entreprise->validate();
            $errors = array_merge($errors, $modelErrors);
            $this->returnAllErrors($errors, 'entreprise/modifier?uiid=' . $uiid . '&error=true');

            $this->repo->updateEntreprise($entreprise);

            $_SESSION['success'] = "L'entreprise a été mise à jour avec succès";
            $this->redirect('mes_entreprises?action=voir&uiid=' . $uiid);
        } catch (Exception $e) {
            $_SESSION['form_data'] = $_POST;
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('entreprise/modifier?uiid=' . $uiid . '&error=true');
        }
    }

    /**
     * Delete a company
     */
    public function deleteEntreprise()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $uiid = isset($_GET['uiid']) ? htmlspecialchars(trim($_GET['uiid'])) : null;
            $idEntreprise = $this->getId();
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);

            if (!$entreprise || !$idEntreprise) {
                throw new Exception("L'entreprise demandée n'existe pas");
            }

            // Check if user is the owner of the company
            if ($entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de supprimer cette entreprise");
            }
            // Check if the company has associated events
            $hasEvents = $this->repo->isEntrepriseHasEvents($idEntreprise);
            if ($hasEvents) {
                throw new Exception("L'entreprise ne peut pas être supprimée car elle contient des événements");
            }
            $this->repo->deleteEntreprise($idEntreprise);

            $_SESSION['success'] = "L'entreprise a été supprimée avec succès";
            $this->redirect('mes_entreprises');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_entreprises?action=voir&uiid=' . $uiid . '&error=true');
        }
    }

    public function updateBanner()
    {
        try {
            $idEntreprise = $this->getId();
            $uiid = isset($_GET['uiid']) ? htmlspecialchars(trim($_GET['uiid'])) : null;
            if (!$idEntreprise) {
                throw new Exception("ID d'entreprise invalide");
            }

            $idUser = $_SESSION['idUser'];
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);

            if (!$entreprise) {
                throw new Exception("L'entreprise demandée n'existe pas");
            }

            if ($entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette entreprise");
            }

            // Handle image upload
            $helper = new Helper();
            $bannerPath = $helper->handleImageUpload('banner', 'banners');

            // Update entreprise banner path
            $entreprise->setBannerPath($bannerPath)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $this->repo->updateEntrepriseBanner($entreprise);

            $_SESSION['success'] = "La bannière a été mise à jour avec succès";
            $this->redirect('mes_entreprises?action=voir&uiid=' . $uiid);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_entreprises?action=voir&uiid=' . $uiid . '&error=true');
        }
    }

    public function deleteBanner(): void
    {
        try {
            $idEntreprise = $this->getId();
            $uiid = isset($_GET['uiid']) ? htmlspecialchars(trim($_GET['uiid'])) : null;
            if (!$idEntreprise) {
                throw new Exception("ID d'entreprise invalide");
            }

            $idUser = $_SESSION['idUser'];
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);

            if (!$entreprise) {
                throw new Exception("L'entreprise demandée n'existe pas");
            }

            if ($entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette entreprise");
            }

            // Set banner path to default
            $defaultBannerPath = 'assets/images/uploads/banners/default_banner.png';
            if ($entreprise->getBannerPath() === $defaultBannerPath) {
                throw new Exception("La bannière est déjà la bannière par défaut");
            }
            // remove the file from server if not default
            $helper = new Helper();
            $helper->handleDeleteImage($entreprise->getBannerPath());

            $entreprise->setBannerPath($defaultBannerPath)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $this->repo->updateEntrepriseBanner($entreprise);

            $_SESSION['success'] = "La bannière a été réinitialisée avec succès";
            $this->redirect('mes_entreprises?action=voir&uiid=' . $uiid);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_entreprises?action=voir&uiid=' . $uiid . '&error=true');
        }
    }

    public function updateLogo()
    {
        try {
            $idEntreprise = $this->getId();
            $uiid = isset($_GET['uiid']) ? htmlspecialchars(trim($_GET['uiid'])) : null;
            if (!$idEntreprise) {
                throw new Exception("ID d'entreprise invalide");
            }

            $idUser = $_SESSION['idUser'];
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);

            if (!$entreprise) {
                throw new Exception("L'entreprise demandée n'existe pas");
            }

            if ($entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette entreprise");
            }

            // Handle image upload
            $helper = new Helper();
            $logoPath = $helper->handleImageUpload('logo', 'logos');

            // Update entreprise logo path
            $entreprise->setLogoPath($logoPath)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $this->repo->updateEntrepriseLogo($entreprise);

            $_SESSION['success'] = "Le logo a été mis à jour avec succès";
            $this->redirect('mes_entreprises?action=voir&uiid=' . $uiid);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_entreprises?action=voir&uiid=' . $uiid . '&error=true');
        }
    }

    public function deleteLogo(): void
    {
        try {
            $idEntreprise = $this->getId();
            $uiid = isset($_GET['uiid']) ? htmlspecialchars(trim($_GET['uiid'])) : null;
            if (!$idEntreprise) {
                throw new Exception("ID d'entreprise invalide");
            }

            $idUser = $_SESSION['idUser'];
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);

            if (!$entreprise) {
                throw new Exception("L'entreprise demandée n'existe pas");
            }

            if ($entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette entreprise");
            }

            // Set logo path to default
            $defaultLogoPath = 'assets/images/uploads/logos/default_logo.png';
            if ($entreprise->getLogoPath() === $defaultLogoPath) {
                throw new Exception("Le logo est déjà le logo par défaut");
            }
            // remove the file from server if not default
            $helper = new Helper();
            $helper->handleDeleteImage($entreprise->getLogoPath());

            $entreprise->setLogoPath($defaultLogoPath)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $this->repo->updateEntrepriseLogo($entreprise);

            $_SESSION['success'] = "Le logo a été réinitialisé avec succès";
            $this->redirect('mes_entreprises?action=voir&uiid=' . $uiid);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_entreprises?action=voir&uiid=' . $uiid . '&error=true');
        }
    }
    // send an email to admin to activate the entreprise and send the file in attachment 
    public function demanderActivation()
    {
        try {
            $idEntreprise = $this->getId();
            $uiid = isset($_GET['uiid']) ? htmlspecialchars(trim($_GET['uiid'])) : null;
            if (!$idEntreprise) {
                throw new Exception("ID d'entreprise invalide");
            }

            $idUser = $_SESSION['idUser'];
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);

            if (!$entreprise) {
                throw new Exception("L'entreprise demandée n'existe pas");
            }

            if ($entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette entreprise");
            }
            if ($entreprise->getIsActive()) {
                throw new Exception("L'entreprise est déjà active");
            }
           
            $lastRequestDate = $entreprise->getRequestDate();
            if ($lastRequestDate && $entreprise->getRequestDate() !== null && $entreprise->getHasRequestForActivation() == true) {
                $lastRequestDateTime = new DateTime($lastRequestDate);
                $currentDateTime = new DateTime();
                $interval = $currentDateTime->diff($lastRequestDateTime);
                if ($interval->days < 3) {
                    throw new Exception("Vous avez déjà fait une demande d'activation récemment. Veuillez attendre avant de faire une nouvelle demande.");
                }
            }
            // Handle Kbis file upload validation
            if (!isset($_FILES['kbis']) || $_FILES['kbis']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Veuillez fournir un fichier Kbis valide");
            }

            $file = $_FILES['kbis'];

            // Validate file type (PDF only)
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($fileExtension !== 'pdf') {
                throw new Exception("Seuls les fichiers PDF sont acceptés pour le Kbis");
            }

            // Validate file size (5MB max)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                throw new Exception("Le fichier est trop volumineux. Taille maximum autorisée : 5MB");
            }

            // Validate MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if ($mimeType !== 'application/pdf') {
                throw new Exception("Le fichier doit être un PDF valide");
            }

            // Get optional message
            $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

            // Get user information
            $userFirstName = $_SESSION['firstName'] ?? '';
            $userLastName = $_SESSION['lastName'] ?? '';
            $userEmail = $_SESSION['email'] ?? '';
            $requestDate = date('Y-m-d H:i:s');

            // Prepare email content
            $subject = "Demande d'activation d'entreprise - " . $entreprise->getName();

            $body = "
                <h3>Nouvelle demande d'activation d'entreprise</h3>
                
                <h4>Informations de l'entreprise :</h4>
                <ul>
                    <li><strong>Nom :</strong> " . $entreprise->getName() . "</li>
                    <li><strong>SIRET :</strong> " . ($entreprise->getSiret() ?: 'Non renseigné') . "</li>
                    <li><strong>Adresse :</strong> " . ($entreprise->getAddress() ?: 'Non renseignée') . "</li>
                    <li><strong>Email :</strong> " . ($entreprise->getEmail() ?: 'Non renseigné') . "</li>
                    <li><strong>Téléphone :</strong> " . ($entreprise->getPhone() ?: 'Non renseigné') . "</li>
                </ul>
                
                <h4>Informations du demandeur :</h4>
                <ul>
                    <li><strong>Nom :</strong> " . ($userFirstName ? $userFirstName . ' ' . $userLastName : 'Utilisateur inconnu') . "</li>
                    <li><strong>Email :</strong> " . ($userEmail ?: 'Email inconnu') . "</li>
                </ul>";

            if (!empty($message)) {
                $body .= "
                <h4>Message du demandeur :</h4>
                <p style='background-color: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 15px 0;'>
                    " . nl2br($message) . "
                </p>";
            }

            $body .= "
                <hr>
                <p><strong>Actions à effectuer :</strong></p>
                <ol>
                    <li>Vérifier le document Kbis ci-joint</li>
                    <li>Valider les informations de l'entreprise</li>
                    <li>Activer l'entreprise dans l'interface d'administration</li>
                </ol>
                <p>Vous trouvez en pièce jointe le document Kbis fourni par le demandeur.</p>
                <p>Merci de traiter cette demande dans les plus brefs délais.</p>
                
                <p><em>Cette demande a été générée automatiquement le " . $requestDate . "</em></p>";

            // Send email using your Mail service
            $mail = new Mail();

            // Add the temporary Kbis file as attachment directly from tmp location
            $originalFileName = 'Kbis_' . $entreprise->getName() . '_' . $requestDate . '.pdf';
            $mail->addAttachment($file['tmp_name'], $originalFileName);

            // Send the email
            $sendEmail =  $mail->sendEmail(
                NO_REPLY_EMAIL, // sender email
                'Le Média Voironnais - Système', // sender name
                ADMIN_EMAIL, // recipient email
                'Administrateur', // recipient name
                $subject,
                $body
            );
            if ($sendEmail) {
                // add has request with the date
                $this->repo->markActivationRequested($idEntreprise, $requestDate);
            }

            // No need to clean up - PHP automatically deletes temporary files

            $_SESSION['success'] = "Votre demande d'activation a été envoyée avec succès. Vous recevrez une réponse sous 2-3 jours ouvrés.";
            $this->redirect('mes_entreprises?action=voir&uiid=' . $uiid);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_entreprises?action=voir&uiid=' . $uiid . '&error=true');
        }
    }
}
