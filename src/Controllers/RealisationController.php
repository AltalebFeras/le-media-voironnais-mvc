<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;
use src\Models\Realisation;
use src\Repositories\EntrepriseRepository;
use src\Repositories\RealisationRepository;
use Exception;
use DateTime;
use src\Services\Helper;

class RealisationController extends AbstractController
{
    private $repo;
    private $entrepriseRepo = null;

    public function __construct()
    {
        $this->repo = new RealisationRepository();
        $this->entrepriseRepo = new EntrepriseRepository();
    }
    private function getRealisationId(): int|null
    {
        if (isset($_GET['realisation_uiid'])) {
            $uiid = htmlspecialchars(trim($_GET['realisation_uiid']));
        } elseif (isset($_POST['realisation_uiid'])) {
            $uiid = htmlspecialchars(trim($_POST['realisation_uiid']));
        } else {
            $uiid = null;
        }
        return $this->repo->getIdRealisationByUiid($uiid);
    }

    private function getEntrepriseId()
    {
        $uiid = isset($_GET['entreprise_uiid']) ? htmlspecialchars(trim($_GET['entreprise_uiid'])) : null;
        return $this->entrepriseRepo->getIdEntrepriseByUiid($uiid);
    }

    /**
     * Display list of entreprise realisations
     */
    public function mesRealisations(): void
    {
        try {
            $idEntreprise = $this->getEntrepriseId();
            $entreprise = $this->entrepriseRepo->getEntrepriseById($idEntreprise);
            $idUser = $_SESSION['idUser'];

            if (!$entreprise || $entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation d'accéder à cette entreprise");
            }

            $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $realisationsPerPage = 9;
            $realisations = $this->repo->getEntrepriseRealisations($idEntreprise, $currentPage, $realisationsPerPage);
            $totalRealisations = $this->repo->countEntrepriseRealisations($idEntreprise);
            $totalPages = (int)ceil($totalRealisations / $realisationsPerPage);

            $this->render('realisation/mes_realisations', [
                'realisations' => $realisations,
                'entreprise' => $entreprise,
                'title' => 'Mes réalisations - ' . $entreprise->getName(),
                'total' => $totalRealisations,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_entreprises?action=voir&uiid=' . ($entreprise->getUiid() ?? '') . '&error=true');
        }
    }

    /**
     * Display realisation details
     */
    public function displayRealisationDetails()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $idRealisation = $this->getRealisationId();
            $realisation = $this->repo->getRealisationById($idRealisation);

            if (!$realisation || !$idRealisation) {
                throw new Exception("La réalisation demandée n'existe pas");
            }

            $entreprise = $this->entrepriseRepo->getEntrepriseById($realisation->getIdEntreprise());
            if (!$entreprise || $entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de voir cette réalisation");
            }

            $images = $this->repo->getRealisationImages($idRealisation);

            $this->render('realisation/voir_realisation', [
                'realisation' => $realisation,
                'entreprise' => $entreprise,
                'title' => 'Détails de la réalisation',
                'realisationImages' => $images,
                'isOwner' => 'true'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_entreprises?action=voir&uiid=' . ($entreprise->getUiid() ?? '') . '&error=true');
        }
    }

    /**
     * Show form to add realisation
     */
    public function showAddRealisationForm()
    {
        try {
            $idEntreprise = $this->getEntrepriseId();
            $entreprise = $this->entrepriseRepo->getEntrepriseById($idEntreprise);
            $idUser = $_SESSION['idUser'];

            if (!$entreprise || $entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation d'accéder à cette entreprise");
            }

            $this->render('realisation/ajouter_realisation', [
                'entreprise' => $entreprise,
                'title' => 'Ajouter une réalisation'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_entreprises?action=voir&uiid=' . ($entreprise->getUiid() ?? '') . '&error=true');
        }
    }

    /**
     * Process add realisation form
     */
    public function addRealisation()
    {
        try {
            $idEntreprise = $this->getEntrepriseId();
            $entreprise = $this->entrepriseRepo->getEntrepriseById($idEntreprise);
            $idUser = $_SESSION['idUser'];

            if (!$entreprise || $entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation d'accéder à cette entreprise");
            }

            // Get form data
            $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : null;
            $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : null;
            $dateRealized = isset($_POST['dateRealized']) ? htmlspecialchars(trim($_POST['dateRealized'])) : null;
            $isPublic = isset($_POST['isPublic']) ? 1 : 0;
            $isFeatured = isset($_POST['isFeatured']) ? 1 : 0;

            $errors = [];
            $_SESSION['form_data'] = $_POST;

            // Check if title exists for this entreprise
            if ($this->repo->isTitleExistsForEntreprise($title, $idEntreprise)) {
                $errors['title'] = "Vous avez déjà une réalisation avec ce titre";
            }

            // Create new realisation
            $realisation = new Realisation();
            $realisation->setTitle($title)
                ->setDescription($description)
                ->setDateRealized($dateRealized)
                ->setIsPublic($isPublic)
                ->setIsFeatured($isFeatured)
                ->setIdEntreprise($idEntreprise)
                ->setCreatedAt(new DateTime());

            // Generate unique slug and uiid
            $helper = new Helper();
            $uiid = $helper->generateUiid();
            $realisation->setUiid($uiid);

            $slug = $helper->generateSlug($entreprise->getName(), $title);
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

            $realisation->setSlug($slug);

            // Use model validation
            $modelErrors = $realisation->validate();
            $errors = array_merge($errors, $modelErrors);

            $this->returnAllErrors($errors, 'entreprise/mes_realisations/ajouter?entreprise_uiid=' . $entreprise->getUiid() . '&error=true');

            $this->repo->createRealisation($realisation);

            $_SESSION['success'] = "La réalisation a été créée avec succès";
            $this->redirect('entreprise/mes_realisations?entreprise_uiid=' . $entreprise->getUiid());
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('entreprise/mes_realisations/ajouter?entreprise_uiid=' . ($entreprise->getUiid() ?? '') . '&error=true');
        }
    }

    /**
     * Show form to edit realisation
     */
    public function showEditRealisationForm()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $idRealisation = $this->getRealisationId();
            $realisation = $this->repo->getRealisationById($idRealisation);

            if (!$realisation || !$idRealisation) {
                throw new Exception("La réalisation demandée n'existe pas");
            }

            $entreprise = $this->entrepriseRepo->getEntrepriseById($realisation->getIdEntreprise());
            if (!$entreprise || $entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette réalisation");
            }

            $this->render('realisation/modifier_realisation', [
                'realisation' => $realisation,
                'entreprise' => $entreprise,
                'title' => 'Modifier la réalisation'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('entreprise/mes_realisations', ['action' => 'voir', 'entreprise_uiid' => $entreprise->getUiid(), 'realisation_uiid' => $idRealisation, 'error' => true]);
        }
    }

    /**
     * Process edit realisation form
     */
    public function updateRealisation()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $realisationUiid = isset($_POST['realisation_uiid']) ? htmlspecialchars(trim($_POST['realisation_uiid'])) : null;
            $idRealisation = $this->repo->getIdRealisationByUiid($realisationUiid);
            $realisation = $this->repo->getRealisationById($idRealisation);

            if (!$realisation || !$idRealisation) {
                throw new Exception("La réalisation demandée n'existe pas");
            }

            $entreprise = $this->entrepriseRepo->getEntrepriseById($realisation->getIdEntreprise());
            if (!$entreprise || $entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette réalisation");
            }

            // Get form data
            $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : null;
            $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : null;
            $dateRealized = isset($_POST['dateRealized']) ? htmlspecialchars(trim($_POST['dateRealized'])) : null;
            $isPublic = isset($_POST['isPublic']) ? 1 : 0;
            $isFeatured = isset($_POST['isFeatured']) ? 1 : 0;

            $errors = [];
            $_SESSION['form_data'] = $_POST;

            // Check if title exists for this entreprise (exclude current realisation)
            if ($this->repo->isTitleExistsForEntreprise($title, $entreprise->getIdEntreprise(), $idRealisation)) {
                $errors['title'] = "Vous avez déjà une réalisation avec ce titre";
            }

            // Update realisation data
            $originalTitle = $realisation->getTitle();
            $realisation->setTitle($title)
                ->setDescription($description)
                ->setDateRealized($dateRealized)
                ->setIsPublic($isPublic)
                ->setIsFeatured($isFeatured)
                ->setUpdatedAt(new DateTime());

            // Regenerate slug if title changed
            if ($title !== $originalTitle) {
                $helper = new Helper();
                $slug = $helper->generateSlug($entreprise->getName(), $title);
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
                $realisation->setSlug($slug);
            }

            // Use model validation
            $modelErrors = $realisation->validate();
            $errors = array_merge($errors, $modelErrors);

            $this->returnAllErrors($errors, 'entreprise/mes_realisations/modifier?realisation_uiid=' . $realisationUiid . '&error=true');

            $this->repo->updateRealisation($realisation);

            $_SESSION['success'] = "La réalisation a été mise à jour avec succès";
            $this->redirect('entreprise/mes_realisations', ['action' => 'voir', 'entreprise_uiid' => $entreprise->getUiid(), 'realisation_uiid' => $realisationUiid]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('entreprise/mes_realisations/modifier', ['realisation_uiid' => $realisationUiid, 'error' => true]);
        }
    }

    /**
     * Delete realisation
     */
    public function deleteRealisation()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $realisationUiid = isset($_POST['realisation_uiid']) ? htmlspecialchars(trim($_POST['realisation_uiid'])) : null;
            $idRealisation = $this->repo->getIdRealisationByUiid($realisationUiid);
            $realisation = $this->repo->getRealisationById($idRealisation);

            if (!$realisation || !$idRealisation) {
                throw new Exception("La réalisation demandée n'existe pas");
            }

            $entreprise = $this->entrepriseRepo->getEntrepriseById($realisation->getIdEntreprise());
            if (!$entreprise || $entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de supprimer cette réalisation");
            }

            $this->repo->deleteRealisation($idRealisation);

            $_SESSION['success'] = "La réalisation a été supprimée avec succès";
            $this->redirect('entreprise/mes_realisations?entreprise_uiid=' . $entreprise->getUiid());
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('entreprise/mes_realisations', ['action' => 'voir', 'entreprise_uiid' => $entreprise->getUiid(), 'realisation_uiid' => $realisationUiid, 'error' => true]);
        }
    }

    /**
     * Add realisation image
     */
    public function addRealisationImage()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $realisationUiid = isset($_POST['realisation_uiid']) ? htmlspecialchars(trim($_POST['realisation_uiid'])) : null;
            $idRealisation = $this->repo->getIdRealisationByUiid($realisationUiid);
            $realisation = $this->repo->getRealisationById($idRealisation);

            if (!$realisation || !$idRealisation) {
                throw new Exception("La réalisation demandée n'existe pas");
            }

            $entreprise = $this->entrepriseRepo->getEntrepriseById($realisation->getIdEntreprise());
            if (!$entreprise || $entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette réalisation");
            }

            $helper = new Helper();
            $imagePath = $helper->handleImageUpload('realisationImage', 'realisations');
            $altText = isset($_POST['altText']) ? htmlspecialchars(trim($_POST['altText'])) : '';
            $uiid = $helper->generateUiid();

            // Get current max sort order
            $maxSortOrder = $this->repo->getMaxImageSortOrder($idRealisation);
            $sortOrder = $maxSortOrder + 1;

            $this->repo->addRealisationImage($uiid, $idRealisation, $imagePath, $altText, $sortOrder);

            $_SESSION['success'] = "L'image a été ajoutée avec succès";
            $this->redirect('entreprise/mes_realisations', [
                'action' => 'voir',
                'entreprise_uiid' => $entreprise->getUiid(),
                'realisation_uiid' => $realisationUiid
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('entreprise/mes_realisations', [
                'action' => 'voir',
                'entreprise_uiid' => $entreprise->getUiid(),
                'realisation_uiid' => $realisationUiid,
                'error' => true
            ]);
        }
    }
    /**
     * Delete realisation image
     */
    public function deleteRealisationImage()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $realisationUiid = isset($_POST['realisation_uiid']) ? htmlspecialchars(trim($_POST['realisation_uiid'])) : null;
            $realisationImageUiid = isset($_POST['realisation_image_uiid']) ? htmlspecialchars(trim($_POST['realisation_image_uiid'])) : null;
            $idRealisation = $this->repo->getIdRealisationByUiid($realisationUiid);
            $realisation = $this->repo->getRealisationById($idRealisation);

            if (!$realisation || !$idRealisation || !$realisationImageUiid) {
                throw new Exception("Paramètres invalides");
            }

            $entreprise = $this->entrepriseRepo->getEntrepriseById($realisation->getIdEntreprise());
            if (!$entreprise || $entreprise->getIdUser() != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette réalisation");
            }

            $image = $this->repo->getRealisationImageByUiid($realisationImageUiid);
            if (!$image || $image['idRealisation'] != $idRealisation) {
                throw new Exception("Image introuvable");
            }

            // Delete physical file
            $helper = new Helper();
            $helper->handleDeleteImage($image['imagePath']);

            // Delete from database
            $this->repo->deleteRealisationImage($realisationImageUiid);

            $_SESSION['success'] = "L'image a été supprimée avec succès";
            $this->redirect('entreprise/mes_realisations', [
                'action' => 'voir',
                'entreprise_uiid' => $entreprise->getUiid(),
                'realisation_uiid' => $realisationUiid
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('entreprise/mes_realisations', [
                'action' => 'voir',
                'entreprise_uiid' => $entreprise->getUiid(),
                'realisation_uiid' => $realisationUiid,
                'error' => true
            ]);
        }
    }
}
