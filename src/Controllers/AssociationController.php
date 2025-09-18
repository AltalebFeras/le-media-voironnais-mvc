<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;
use src\Models\Association;
use src\Repositories\AssociationRepository;
use Exception;
use DateTime;
use src\Services\Helper;

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
            $associationsPerPage = 9;
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


            $existingAssociation = $this->repo->getAssociationByNameForThisUser($name, $idUser);
            if ($existingAssociation) {
                $errors['name'] = "Vous avez déjà une association avec ce nom";
            }
            $existingVille = $this->repo->isVilleExists($idVille);
            if (!$existingVille) {
                $errors['idVille'] = "La ville sélectionnée est invalide";
            }
            
            $helper = new Helper();
            // default paths for logo and banner
            $logoPath = DOMAIN. HOME_URL . 'assets/images/uploads/logos/default_logo.png';
            $bannerPath = DOMAIN. HOME_URL . 'assets/images/uploads/banners/default_banner.png';
            // Create new association
            $association = new Association();

            $association->setName($name)
                ->setDescription($description)
                ->setAddress($address)
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

            $slug = $helper->generateSlug($name);
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
            // Regenerate slug if name changed
            if ($name !== $association->getName()) {
                $helper = new Helper();
                $slug = $helper->generateSlug($name);

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
 
}
