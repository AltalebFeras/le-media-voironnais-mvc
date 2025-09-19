<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;
use src\Models\Entreprise;
use src\Repositories\AssociationRepository;
use src\Repositories\EntrepriseRepository;
use Exception;
use DateTime;
use src\Services\Helper;

class EntrepriseController extends AbstractController
{
    private $repo;
    private $AssocRepo = null;

    public function __construct()
    {
        $this->repo = new EntrepriseRepository();
        $this->AssocRepo = new AssociationRepository();
    }
    private function getId()
    {
        $uiid =  isset($_GET['uiid']) ? htmlspecialchars(trim($_GET['uiid'])) : null;
        return $this->repo->getIdByUiid($uiid);
    }
    public function mesEntreprises()
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
    public function displayEntrepriseDetails()
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
                throw new Exception("Vous n'avez pas l'autorisation de voir cette entreprise");
            }

            $this->render('entreprise/voir_entreprise', [
                'entreprise' => $entreprise,
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
            $existingVille = $this->AssocRepo->isVilleExists($idVille);
            if (!$existingVille) {
                $errors['idVille'] = "La ville sélectionnée est invalide";
            }
            // default paths for logo and banner
            $logoPath = DOMAIN . HOME_URL . 'assets/images/uploads/logos/default_logo.png';
            $bannerPath = DOMAIN . HOME_URL . 'assets/images/uploads/banners/default_banner.png';

            // Create new company
            $entreprise = new Entreprise();
            $entreprise->setName($name)
                ->setDescription($description)
                ->setAddress($address)
                ->setPhone($phone)
                ->setEmail($email)
                ->setWebsite($website)
                ->setSiret($siret)
                ->setStatus('brouillon') // Default status
                ->setIsActive(0) // Default to inactive
                ->setIdUser($idUser)
                ->setIdVille($idVille)
                ->setCreatedAt((new DateTime())->format('Y-m-d H:i:s'))
                ->setLogoPath($logoPath)
                ->setBannerPath($bannerPath);
            // Generate unique slug
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
            $idEntreprise = isset($_GET['id']) ? htmlspecialchars(trim($_GET['id'])) : null;
            $entreprise = $this->repo->getEntrepriseById($idEntreprise);

            if (!$entreprise || !$idEntreprise) {
                throw new Exception("L'entreprise demandée n'existe pas");
            }

            // Check if user is the owner of the company
            if ($entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette entreprise");
            }

            $this->render('entreprise/modifier_entreprise', [
                'entreprise' => $entreprise,
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
            $idVille = isset($_POST['idVille']) ? (int)$_POST['idVille'] : null;
            $siret = isset($_POST['siret']) ? htmlspecialchars(trim($_POST['siret'])) : null;
            $status = 'brouillon'; // Default status

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
            $existingVille = $this->AssocRepo->isVilleExists($idVille);
            if (!$existingVille) {
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
                ->setStatus($status)
                ->setIdVille($idVille)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));


            // Regenerate slug if name changed - compare with original name
            if ($name !== $originalName) {
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

                $entreprise->setSlug($slug);
            }

            // Use model validation
            $modelErrors = $entreprise->validate();
            $errors = array_merge($errors, $modelErrors);
            $this->returnAllErrors($errors, 'entreprise/modifier?id=' . $idEntreprise . '&error=true');

            $this->repo->updateEntreprise($entreprise);

            $_SESSION['success'] = "L'entreprise a été mise à jour avec succès";
            $this->redirect('mes_entreprises');
        } catch (Exception $e) {
            $_SESSION['form_data'] = $_POST;
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('entreprise/modifier?id=' . $idEntreprise . '&error=true');
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
            $this->redirect('mes_entreprises');
        }
    }
}
