<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;
use src\Models\Association;
use src\Repositories\AssociationRepository;
use Exception;
use DateTime;
use src\Repositories\VilleRepository;
use src\Services\Helper;

class AssociationController extends AbstractController
{
    private $repo;
    private $villeRepo;

    public function __construct()
    {
        $this->repo = new AssociationRepository();
        $this->villeRepo = new VilleRepository();
    }
    private function getId(): int|null
    {
        if (isset($_GET['uiid'])) {
            $uiid = htmlspecialchars(trim($_GET['uiid']));
        } elseif (isset($_POST['uiid'])) {
            $uiid = htmlspecialchars(trim($_POST['uiid']));
        } else {
            $uiid = null;
        }
        return $this->repo->getIdAssociationByUiid($uiid);
    }
    /**
     * Display list of user associations
     */
    public function mesAssociations()
    {

        try {
            $idUser = $_SESSION['idUser'];
            $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $associationsPerPage = 9;
            $associations = $this->repo->getUserAssociations($idUser, $currentPage, $associationsPerPage);
            $totalAssociations = $this->repo->countUserAssociations($idUser);
            $totalPages = (int)ceil($totalAssociations / $associationsPerPage);
            // Render the view with pagination data
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
            $idAssociation = $this->getId();
            $errors = [];
            $association = $this->repo->getAssociationById($idAssociation);

            if (!$association || !$idAssociation || $association->getIsDeleted() == true) {
                $errors['association'] = "L'association demandée n'existe pas";
            }

            $isOwner = $association ? $association->getIdUser() == $idUser : false;
            if (!$isOwner) {
                $errors['association'] = "L'association demandée n'existe pas";
            }
            $members = [];
            $ville = null;

            if ($association) {
                $members = $this->repo->getAssociationMembers($idAssociation);
                $ville = $this->villeRepo->getVilleById($association->getIdVille());

                // Check if user has access (owner or member or public association)
                $isMember = false;
                foreach ($members as $member) {
                    if ($member['idUser'] == $idUser) {
                        $isMember = true;
                        break;
                    }
                }

                if (!$isOwner && !$isMember) {
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


            $existingAssociation = $this->repo->getAssociationByNameForThisUser($name, $idUser);

            if ($existingAssociation && $existingAssociation->getIsDeleted() == false) {
                $errors['name'] = "Vous avez déjà une association avec ce nom";
            }
            $nameVille = $this->villeRepo->isVilleExists($idVille);
            if (!$nameVille) {
                $errors['idVille'] = "La ville sélectionnée est invalide";
            }

            $helper = new Helper();
            // default paths for logo and banner
            $logoPath = 'assets/images/uploads/logos/default_logo.png';
            $bannerPath = 'assets/images/uploads/banners/default_banner.png';

            // generate unique uiid 16 characters
            $uiid = $helper->generateUiid();
            // Create new association
            $association = new Association();

            $association->setName($name)
                ->setDescription($description)
                ->setAddress($address)
                ->setUiid($uiid)
                ->setPhone($phone)
                ->setEmail($email)
                ->setWebsite($website)
                ->setIsActive(1)
                ->setLogoPath($logoPath)
                ->setBannerPath($bannerPath)
                ->setIdUser($idUser)
                ->setIdVille($idVille)
                ->setCreatedAt((new DateTime())->format('Y-m-d H:i:s'));

            // Use model validation
            $modelErrors = $association->validate();
            $errors = array_merge($errors, $modelErrors);

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

            $association->setSlug($slug);

            $this->returnAllErrors($errors, 'association/ajouter?error=true');

            $create_association = $this->repo->createAssociation($association);
            if ($create_association) {
                $_SESSION['success'] = "L'association a été créée avec succès";
                $this->redirect('mes_associations');
            } else {
                throw new Exception("Une erreur est survenue lors de la création de l'association");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('association/ajouter?error=true');
        }
    }

    /**
     * Show form to edit an association
     */
    public function showEditForm()
    {
        try {
            // Handle URL segments for /association/modifier?uiid=
            $idAssociation = $this->getId();

            $idUser = $_SESSION['idUser'];
            $association = $this->repo->getAssociationById($idAssociation);

            if (!$association || !$idAssociation || $association->getIsDeleted() == true) {
                throw new Exception("L'association demandée n'existe pas");
            }

            $isOwner = $association ? $association->getIdUser() == $idUser : false;
            if (!$isOwner) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette association");
            }

            $ville = $this->villeRepo->getVilleById($association->getIdVille());

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

            $idAssociation = $this->getId();
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
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
            $_SESSION['form_data'] = $_POST;

            $errors = [];

            // Check if name exists for other associations of this user
            $existingAssociation = $this->repo->getAssociationByNameForThisUser($name, $idUser);
            if ($existingAssociation && $existingAssociation->getIdAssociation() != $idAssociation  && $existingAssociation->getIsDeleted() == false) {
                $errors['name'] = "Vous avez déjà une association avec ce nom";
            }

            $nameVille = $this->villeRepo->isVilleExists($idVille);
            if (!$nameVille) {
                $errors['idVille'] = "La ville sélectionnée est invalide";
            }

            // Update association data
            $association->setName($name)
                ->setDescription($description)
                ->setAddress($address)
                ->setPhone($phone)
                ->setEmail($email)
                ->setWebsite($website)
                ->setIdVille($idVille)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));
            // Regenerate slug if name changed
            if ($name !== $association->getName()) {
                $helper = new Helper();
                $slug = $helper->generateSlug($name, $nameVille);

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

                $association->setSlug($slug);
            }
            // Use model validation
            $modelErrors = $association->validate();
            $errors = array_merge($errors, $modelErrors);

            $this->returnAllErrors($errors, 'association/modifier?uiid=' . $uiid . '&error=true');

            $this->repo->updateAssociation($association);

            $_SESSION['success'] = "L'association a été mise à jour avec succès";
            $this->redirect('mes_associations?action=voir&uiid=' . $uiid);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('association/modifier?uiid=' . ($uiid ?? '') . '&error=true');
        }
    }

    /**
     * Delete an association
     */
    public function deleteAssociation(): void
    {
        try {
            $idAssociation = $this->getId();
            if (!$idAssociation) {
                throw new Exception("ID d'association invalide");
            }

            $idUser = $_SESSION['idUser'];

            if (!$this->repo->isAssociationOwner($idAssociation, $idUser)) {
                throw new Exception("Vous n'avez pas l'autorisation de supprimer cette association");
            }
            // verify before delete if association contains events
            $hasEvents = $this->repo->isAssociationHasEvents($idAssociation);
            if ($hasEvents) {
                throw new Exception("L'association ne peut pas être supprimée car elle contient des événements");
            }

            $this->repo->deleteAssociation($idAssociation, $idUser);
            $_SESSION['success'] = "L'association a été supprimée avec succès";
            $this->redirect('mes_associations');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_associations');
        }
    }

    public function updateBanner()
    {
        try {
            $idAssociation = $this->getId();
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
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

            // Handle image upload
            $helper = new Helper();
            $bannerPath = $helper->handleImageUpload('banner', 'banners');
         
            // Update association banner path
            $association->setBannerPath($bannerPath)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $this->repo->updateAssociationBanner($association);

            $_SESSION['success'] = "La bannière a été mise à jour avec succès";
            $this->redirect('mes_associations?action=voir&uiid=' . $uiid);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_associations?action=voir&uiid=' . $uiid . '&error=true');
        }
    }

    public function deleteBanner(): void
    {
        try {
            $idAssociation = $this->getId();
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
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

            // Set banner path to default
            $defaultBannerPath = 'assets/images/uploads/banners/default_banner.png';
            if ($association->getBannerPath() === $defaultBannerPath) {
                throw new Exception(" La bannière est déjà la bannière par défaut");
            }
            // remove the file from server if not default
            $helper = new Helper();
            $helper->handleDeleteImage($association->getBannerPath());

            $association->setBannerPath($defaultBannerPath)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $this->repo->updateAssociationBanner($association);

            $_SESSION['success'] = "La bannière a été réinitialisée avec succès";
            $this->redirect('mes_associations?action=voir&uiid=' . $uiid);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_associations?action=voir&uiid=' . $uiid . '&error=true');
        }
    }

    public function updateLogo()
    {
        try {
            $idAssociation = $this->getId();
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
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

            // Handle image upload
            $helper = new Helper();
            $logoPath = $helper->handleImageUpload('logo', 'logos');

            // Update association logo path
            $association->setLogoPath($logoPath)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $this->repo->updateAssociationLogo($association);

            $_SESSION['success'] = "Le logo a été mis à jour avec succès";
            $this->redirect('mes_associations?action=voir&uiid=' . $uiid);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_associations?action=voir&uiid=' . $uiid . '&error=true');
        }
    }
    public function deleteLogo(): void
    {
        try {
            $idAssociation = $this->getId();
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
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

            // Set logo path to default
            $defaultLogoPath = 'assets/images/uploads/logos/default_logo.png';
            if ($association->getLogoPath() === $defaultLogoPath) {
                throw new Exception(" Le logo est déjà le logo par défaut");
            }
            // remove the file from server if not default
            $helper = new Helper();
            $helper->handleDeleteImage($association->getLogoPath());

            $association->setLogoPath($defaultLogoPath)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $this->repo->updateAssociationLogo($association);

            $_SESSION['success'] = "Le logo a été réinitialisé avec succès";
            $this->redirect('mes_associations?action=voir&uiid=' . $uiid);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_associations?action=voir&uiid=' . $uiid . '&error=true');
        }
    }

    public function displayPublicAssociationDetails(string $associationSlug): void
    {
        try {
            $association = $this->repo->getAssociationBySlug($associationSlug);
            // var_dump($association);die;
            if (!$association || !$association['isActive'] || $association['isDeleted']) {
                $_SESSION['error'] = "L'association demandée n'existe pas.";
                $this->redirect('associations');
                return;
            }

            $this->render('association/assoc_recherche_detail', [
                'association' => $association,
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('associations');
        }
    }

    public function listPublicAssociations(): void
    {
        try {
            $associations = $this->repo->getAllActiveAssociations();
            $this->render('association/assoc_list', ['associations' => $associations]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('404');
        }
    }
}
