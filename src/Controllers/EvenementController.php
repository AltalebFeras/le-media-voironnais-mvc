<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;
use src\Models\Evenement;
use src\Repositories\EvenementRepository;
use Exception;
use DateTime;
use src\Repositories\NotificationRepository;
use src\Services\ConfigRouter;
use src\Services\Helper;

use src\Services\Mail;
use function PHPSTORM_META\type;

class EvenementController extends AbstractController
{
    private $repo;

    public function __construct()
    {
        $this->repo = new EvenementRepository();
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
        return $this->repo->getIdByUiid($uiid);
    }

    /**
     * Display list of user events
     */
    public function mesEvenements()
    {
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
    }


    /**
     * Display event details
     */
    public function displayEventDetails()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $idEvenement = $this->getId();
            $evenement = $this->repo->getEventCompleteById($idEvenement);


            if (!$evenement || !$idEvenement) {
                throw new Exception("L'événement demandé n'existe pas");
            }

            // Check if user is the owner of the event
            if ($evenement['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de voir cet événement");
            }

            $ville = $this->repo->getVilleById($evenement['idVille']);
            $images = $this->repo->getEventImages($idEvenement);
            $participants = $this->repo->getEventParticipantsUponStatus($idEvenement, $idUser, $status = 'inscrit');
            if ($evenement['requiresApproval'] == true) {
                $waitingList = $this->repo->getEventParticipantsUponStatus($idEvenement, $idUser, $status = 'liste_attente');
            }

            // var_dump($waitingList , $participants); die;
            $this->render('evenement/voir_evenement', [
                'evenement' => $evenement,
                'ville' => $ville,
                'title' => 'Détails de l\'événement',
                'eventImages' => $images,
                'participants' => $participants,
                'waitingList' => $waitingList,
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
            // verify if the entreprise is active
            $isActiveEntreprise = $this->repo->isEntrepriseActiveAndPublic($idEntreprise);
            if ($idEntreprise && !$isActiveEntreprise) {
                $errors['idEntreprise'] = "L'entreprise sélectionnée n'est pas active ni publique";
            }
            // Ville and category existence
            $nameVille = $this->repo->isVilleExists($idVille);
            if (!$nameVille) {
                $errors['idVille'] = "La ville sélectionnée est invalide";
            }
            $nameCategory = $this->repo->isEventCategoryExists($idEventCategory);
            if (!$idEventCategory || !$nameCategory) {
                $errors['idEventCategory'] = "La catégorie sélectionnée est invalide";
            }
            // verify if the title is not empty and is unique for the user only
            if (empty($title)) {
                $errors['title'] = "Le titre est requis";
            } elseif ($this->repo->isTitleExistsForUser($title, $idUser)) {
                $errors['title'] = "Vous avez déjà un événement avec ce titre";
            }
            // verify maxParticipants is positive integer and less than or equal to 10000
            if ($maxParticipants !== null && (!is_int($maxParticipants) || $maxParticipants <= 0 || $maxParticipants > 10000)) {
                $errors['maxParticipants'] = "Le nombre maximum de participants doit être un entier positif et inférieur ou égal à 10 000";
            }

            $this->returnAllErrors($errors, 'evenement/ajouter', ['error' => 'true']);

            // default path for banner
            $bannerPath =  'assets/images/uploads/banners/default_banner.png';
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
                ->setIsPublic($isPublic)
                ->setIsDeleted(false)
                ->setIdUser($idUser)
                ->setIdAssociation($idAssociation)
                ->setIdVille($idVille)
                ->setIdEventCategory($idEventCategory)
                ->setCreatedAt((new DateTime())->format('Y-m-d H:i:s'))
                ->setBannerPath($bannerPath);

            // Use model validation
            $modelErrors = $evenement->validate();
            $errors = array_merge($errors, $modelErrors);
            // Ensure unique slug
            $helper = new Helper();
            $slug = $helper->generateSlug($nameVille, $nameCategory, $title);
            $existSlug = $this->repo->isSlugExists($slug);
            $uiid = $helper->generateUiid();
            $evenement->setUiid($uiid);

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
            $this->returnAllErrors($errors, 'evenement/ajouter', ['error' => 'true']);
            $this->repo->createEvent($evenement);
            $_SESSION['success'] = "L'événement a été créé avec succès";
            $this->redirect('mes_evenements');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('evenement/ajouter', [
                'error' => 'true'
            ]);
        }
    }

    /**
     * Show form to edit an event
     */
    public function showEditEventForm()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $idEvenement = $this->getId();
            $uiid = isset($_GET['uiid']) ? htmlspecialchars(trim($_GET['uiid'])) : null;
            $evenement = $this->repo->getEventCompleteById($idEvenement);

            if (!$evenement || !$idEvenement) {
                throw new Exception("L'événement demandé n'existe pas");
            }

            if ($evenement['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cet événement");
            }

            $categories = $this->repo->getEventCategories();
            $associations = $this->repo->getUserAssociations($idUser);
            $entreprises = $this->repo->getUserEntreprise($idUser);
            $ville = $this->repo->getVilleById($evenement['idVille']);

            $this->render('evenement/modifier_evenement', [
                'evenement' => $evenement,
                'categories' => $categories,
                'associations' => $associations,
                'entreprises' => $entreprises,
                'ville' => $ville,
                'title' => 'Modifier l\'événement'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid, 'error' => 'true']);
        }
    }

    /**
     * Process edit event form
     */
    public function updateEvent()
    {
        try {

            $idUser = $_SESSION['idUser'];
            $uiid =  isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
            $idEvenement =  $this->getId();
            if (!$idEvenement) {
                throw new Exception("ID d'événement invalide");
            }

            $evenement = $this->repo->getEventCompleteById($idEvenement);

            if (!$evenement) {
                throw new Exception("L'événement demandé n'existe pas");
            }

            if ($evenement['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cet événement");
            }
            $originalTitle = $evenement['title'];
            $originalSlug = $evenement['slug'];

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
            $idEntreprise = isset($_POST['idEntreprise']) && !empty($_POST['idEntreprise']) ? (int)$_POST['idEntreprise'] : null;
            $idEventCategory = isset($_POST['idEventCategory']) ? (int)$_POST['idEventCategory'] : null;
            $isPublic = isset($_POST['isPublic']) ? 1 : 0;
            $requiresApproval = isset($_POST['requiresApproval']) ? 1 : 0;

            $errors = [];

            // Store form data in session for error cases
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
            // verify if the entreprise is active
            $isActiveEntreprise = $this->repo->isEntrepriseActiveAndPublic($idEntreprise);
            if ($idEntreprise && !$isActiveEntreprise) {
                $errors['idEntreprise'] = "L'entreprise sélectionnée n'est pas active ni publique";
            }
            $nameVille = $this->repo->isVilleExists($idVille);
            if (!$nameVille) {
                $errors['idVille'] = "La ville sélectionnée est invalide";
            }
            $nameCategory = $this->repo->isEventCategoryExists($idEventCategory);
            if (!$nameCategory) {
                $errors['idEventCategory'] = "La catégorie sélectionnée est invalide";
            }
            // verify if the title is not empty and is unique for the user only (exclude current event)
            if (empty($title)) {
                $errors['title'] = "Le titre est requis";
            } elseif ($this->repo->isTitleExistsForUser($title, $idUser, $idEvenement)) {
                $errors['title'] = "Vous avez déjà un événement avec ce titre";
            }
            // verify maxParticipants is positive integer and less than or equal to 10000
            if ($maxParticipants !== null && (!is_int($maxParticipants) || $maxParticipants <= 0 || $maxParticipants > 10000)) {
                $errors['maxParticipants'] = "Le nombre maximum de participants doit être un entier positif et inférieur ou égal à 10 000";
            }
            $this->returnAllErrors($errors, 'evenement/modifier', ['uiid' => $uiid, 'error' => 'true']);

            // Update event data
            $evenementModel = new Evenement();
            $evenementModel->setIdEvenement($idEvenement)
                ->setIdUser($idUser)
                ->setTitle($title)
                ->setDescription($description)
                ->setShortDescription($shortDescription)
                ->setStartDate($startDate)
                ->setEndDate($endDate)
                ->setRegistrationDeadline($registrationDeadline)
                ->setMaxParticipants($maxParticipants)
                ->setAddress($address)
                ->setPrice($price)
                ->setCurrency($currency)
                ->setIsPublic($isPublic)
                ->setRequiresApproval($requiresApproval)
                ->setIdVille($idVille)
                ->setIdEventCategory($idEventCategory)
                ->setIdAssociation($idAssociation)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $helper = new Helper();
            // Use model validation
            $modelErrors = $evenementModel->validate();
            $errors = array_merge($errors, $modelErrors);

            // Handle slug generation
            if ($title !== $originalTitle) {
                // Regenerate slug if title changed
                $slug = $helper->generateSlug($nameVille, $nameCategory, $title);
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
                $evenementModel->setSlug($slug);
            } else {
                // Keep existing slug if title unchanged
                $evenementModel->setSlug($originalSlug);
            }

            $this->returnAllErrors($errors, 'evenement/modifier', ['uiid' => $uiid, 'error' => 'true']);

            $this->repo->updateEvent($evenementModel);

            $_SESSION['success'] = "L'événement a été mis à jour avec succès";
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid]);
        } catch (Exception $e) {
            // Preserve form data on exception
            $_SESSION['form_data'] = $_POST;
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('evenement/modifier', ['uiid' => $uiid, 'error' => 'true']);
        }
    }

    /**
     * Delete an event
     */
    public function deleteEvent()
    {
        try {
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
            $idEvenement = $this->getId();
            if (!$idEvenement) {
                throw new Exception("l'événement invalide");
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
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid, 'error' => 'true']);
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
     * Update event banner
     */
    public function updateBanner()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
            $idEvenement = $this->getId();

            if (!$idEvenement) {
                throw new Exception("ID d'événement invalide");
            }

            $evenement = $this->repo->getEventCompleteById($idEvenement);
            if (!$evenement || $evenement['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cet événement");
            }

            $helper = new Helper();
            $bannerPath = $helper->handleImageUpload('banner', 'banners');

            $this->repo->updateEventBanner($idEvenement, $bannerPath);

            $_SESSION['success'] = "La bannière a été mise à jour avec succès";
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid, 'error' => 'true']);
        }
    }

    /**
     * Delete event banner
     */
    public function deleteBanner(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
            $idEvenement = $this->getId();

            if (!$idEvenement) {
                throw new Exception("ID d'événement invalide");
            }

            $evenement = $this->repo->getEventCompleteById($idEvenement);
            if (!$evenement || $evenement['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cet événement");
            }

            $defaultBannerPath = 'assets/images/uploads/banners/default_banner.png';

            if ($evenement['bannerPath'] !== $defaultBannerPath) {
                $helper = new Helper();
                $helper->handleDeleteImage($evenement['bannerPath']);
            }

            $this->repo->updateEventBanner($idEvenement, $defaultBannerPath);

            $_SESSION['success'] = "La bannière a été réinitialisée avec succès";
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid, 'error' => 'true']);
        }
    }

    /**
     * Add event image
     */
    public function addEventImage()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
            $idEvenement = $this->getId();

            if (!$idEvenement) {
                throw new Exception("ID d'événement invalide");
            }

            $evenement = $this->repo->getEventCompleteById($idEvenement);
            if (!$evenement || $evenement['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cet événement");
            }

            $helper = new Helper();
            $imagePath = $helper->handleImageUpload('eventImage', 'events');
            $altText = isset($_POST['altText']) ? htmlspecialchars(trim($_POST['altText'])) : '';
            // $idEventImage = $helper->generateUiid();

            // Get current max sort order
            $maxSortOrder = $this->repo->getMaxImageSortOrder($idEvenement);
            $sortOrder = $maxSortOrder + 1;

            $this->repo->addEventImage($idEvenement, $imagePath, $altText, $sortOrder);

            $_SESSION['success'] = "L'image a été ajoutée avec succès";
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid, 'error' => 'true']);
        }
    }

    /**
     * Delete event image
     */
    public function deleteEventImage()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
            $idEventImage = isset($_POST['imageId']) ? (int)$_POST['imageId'] : null;
            $idEvenement = $this->getId();

            if (!$idEvenement || !$idEventImage) {
                throw new Exception("Paramètres invalides");
            }

            $evenement = $this->repo->getEventCompleteById($idEvenement);
            if (!$evenement || $evenement['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cet événement");
            }

            $image = $this->repo->getEventImageById($idEventImage);
            if (!$image || $image['idEvenement'] != $idEvenement) {
                throw new Exception("Image introuvable");
            }

            // Delete physical file
            $helper = new Helper();
            $helper->handleDeleteImage($image['imagePath']);

            // Delete from database
            $this->repo->deleteEventImage($idEventImage);

            $_SESSION['success'] = "L'image a été supprimée avec succès";
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid, 'error' => 'true']);
        }
    }

    public function listEvents()
    {
        try {
            // Get upcoming events (next 3 events)
            $upcomingEvents = $this->repo->getUpcomingEvents(3);

            // Get recent events (last 4 events that already happened)
            $recentEvents = $this->repo->getRecentEvents(4);

            // Get all events with pagination
            $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $evenementsPerPage = 12;
            $allEvents = $this->repo->getEvents($currentPage, $evenementsPerPage);
            $totalEvenements = $this->repo->countEvents();
            $totalPages = (int)ceil($totalEvenements / $evenementsPerPage);
            $this->render('evenement/publique_evenements_listes', [
                'upcomingEvents' => $upcomingEvents,
                'recentEvents' => $recentEvents,
                'allEvents' => $allEvents,
                'title' => 'Événements',
                'total' => $totalEvenements,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages
            ]);
        } catch (Exception $e) {
            $this->redirect('/', ['error' => 'true']);
        }
    }
    public function viewEventBySlug($composedRoute)
    {
        try {
            $ville_slug = $composedRoute[1] ?? null;
            $category_slug = $composedRoute[2] ?? null;
            $slug = $composedRoute[3] ?? null;

            if (!$slug || !$ville_slug) {
                throw new Exception("Événement invalide");
            }

            $evenement = $this->repo->getEventBySlug($slug);

            if (!$evenement) {
                throw new Exception("L'événement demandé n'existe pas");
            }
            $idEvenement = $evenement['idEvenement'];
            // verify if connected user is the owner or is subscribed to the event
            if (isset($_SESSION['idUser'])) {
                $idUser = $_SESSION['idUser'];
                $isOwner = $this->repo->isEventOwner($idEvenement, $idUser);
                $subscription = $this->repo->getUserSubscription($idUser, $idEvenement);
                if ($subscription) {
                    $status = $subscription['status'];
                    $isSubscribed = $status === 'inscrit' ? true : false;
                    $isSubscribeOnWaitingList = $status === 'liste_attente' ? true : false;
                    $isRefused = $status === 'refuse' ? true : false;
                    $isCancelled = $status === 'annule' ? true : false;
                }
            }
            $ville = $this->repo->getVilleById($evenement['idVille']);

            // Verify that the ville_slug matches
            if ($ville['ville_slug'] !== $ville_slug) {
                throw new Exception("L'événement demandé n'existe pas");
            }

            $images = $this->repo->getEventImages($evenement['idEvenement']);
            // Fetch both comments and replies
            $commentsData = $this->repo->getEventComments($evenement['idEvenement']);
            $comments = $commentsData['comments'];
            $replies = $commentsData['replies'];

            // Count likes and comments
            $likesCount = $this->repo->countEventLikes($evenement['idEvenement']);
            $commentsCount = $this->repo->countEventComments($evenement['idEvenement']);

            // Check if connected user has liked or favourited
            $userHasLiked = false;
            $userHasFavourited = false;
            if (isset($_SESSION['idUser'])) {
                $userHasLiked = $this->repo->hasUserLikedEvent($_SESSION['idUser'], $evenement['idEvenement']);
                $userHasFavourited = $this->repo->hasUserFavouritedEvent($_SESSION['idUser'], $evenement['idEvenement']);
            }

            // Generate share URLs for social media
            $shareUrl = DOMAIN . HOME_URL . "evenements/{$ville_slug}/{$slug}";
            $shareTitle = urlencode($evenement['title']);
            $shareDesc = urlencode(substr($evenement['shortDescription'] ?? $evenement['description'], 0, 100) . '...');
            $shareDate = urlencode(date('d/m/Y H:i', strtotime($evenement['startDate'])));
            $shareLieu = urlencode($evenement['ville_nom_reel'] ?? 'Voiron');

            $shareTable = [
                'whatsapp' => "https://api.whatsapp.com/send?text=%F0%9F%8E%89%20{$shareTitle}%0A%F0%9F%93%85%20Date%20:%20{$shareDate}%0A%F0%9F%93%8D%20Lieu%20:%20{$shareLieu}%0A%E2%9C%A8%20{$shareDesc}%0A👉%20{$shareUrl}",
                'facebook' => "https://www.facebook.com/sharer.php?u=" . urlencode($shareUrl),
                'linkedin' => "https://www.linkedin.com/shareArticle?mini=true&url={$shareUrl}&title={$shareTitle}",
                'twitter' => "https://twitter.com/intent/tweet?url={$shareUrl}&text=%F0%9F%8E%89%20{$shareTitle}%20-%20{$shareDesc}",
                'pinterest' => "https://www.pinterest.com/pin/create/button/?url={$shareUrl}&media={$images[0]}&description={$shareDesc}",
                'instagram' => "https://www.instagram.com/sharer.php?u={$shareUrl}",
                'telegram' => "https://t.me/share/url?url={$shareUrl}&text=%F0%9F%8E%89%20{$shareTitle}%0A%F0%9F%93%85%20Date%20:%20{$shareDate}%0A%F0%9F%93%8D%20Lieu%20:%20{$shareLieu}%0A%E2%9C%A8%20{$shareDesc}",
                'email' => "mailto:?subject={$shareTitle}&body=%F0%9F%8E%89%20Bonjour%2C%0A%0A{$shareDesc}%0A%F0%9F%93%85%20Date%20:%20{$shareDate}%0A%F0%9F%93%8D%20Lieu%20:%20{$shareLieu}%0A%0A👉%20{$shareUrl}"
            ];
            $this->render('evenement/publique_evenement_detail', [
                'evenement' => $evenement,
                'ville' => $ville,
                'title' => $evenement['title'],
                'eventImages' => $images,
                'shareTable' => $shareTable,
                'isOwner' => $isOwner ?? false,
                'isSubscribed' => $isSubscribed ?? false,
                'isSubscribeOnWaitingList' => $isSubscribeOnWaitingList ?? false,
                'isRefused' => $isRefused ?? false,
                'isCancelled' => $isCancelled ?? false,
                'comments' => $comments ?? [],
                'replies' => $replies ?? [],
                'likesCount' => $likesCount,
                'commentsCount' => $commentsCount,
                'userHasLiked' => $userHasLiked,
                'userHasFavourited' => $userHasFavourited
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('evenements', ['error' => 'true']);
        }
    }
    /**
     * Get event interaction data (comments, likes, favorites) as JSON
     */
    public function getEventInteractions()
    {
        try {
            header('Content-Type: application/json');

            $eventUiid = isset($_GET['uiid']) ? htmlspecialchars(trim($_GET['uiid'])) : null;

            if (!$eventUiid) {
                throw new Exception("UIID d'événement invalide");
            }

            $idEvenement = $this->repo->getIdByUiid($eventUiid);
            if (!$idEvenement) {
                throw new Exception("Événement introuvable");
            }

            // Fetch comments and replies
            $commentsData = $this->repo->getEventComments($idEvenement);
            $comments = $commentsData['comments'];
            $replies = $commentsData['replies'];

            // Count likes and comments
            $likesCount = $this->repo->countEventLikes($idEvenement);
            $commentsCount = $this->repo->countEventComments($idEvenement);

            // Check if connected user has liked or favourited
            $userHasLiked = false;
            $userHasFavourited = false;
            $currentUserUiid = null; // Changed from currentUserId

            if (isset($_SESSION['idUser'])) {
                $currentUserId = $_SESSION['idUser'];
                $currentUserUiid = $_SESSION['userUiid']; // Use userUiid
                $userHasLiked = $this->repo->hasUserLikedEvent($currentUserId, $idEvenement);
                $userHasFavourited = $this->repo->hasUserFavouritedEvent($currentUserId, $idEvenement);
            }

            echo json_encode([
                'success' => true,
                'comments' => $comments,
                'replies' => $replies,
                'likesCount' => $likesCount,
                'commentsCount' => $commentsCount,
                'userHasLiked' => $userHasLiked,
                'userHasFavourited' => $userHasFavourited,
                'currentUserUiid' => $currentUserUiid // Changed from currentUserId
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function inscriptionEvent($composedRoute)
    {
        try {
            if (!isset($_SESSION['idUser'])) {
                $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
                $_SESSION['warning'] = "Vous devez être connecté pour vous inscrire à un événement";
                // var_dump($_SERVER, $_SESSION['redirect_after_login']);die;
                $this->redirect('connexion', ['need_login' => 'true']);
                return;
            }
            $idUser = $_SESSION['idUser'];

            $ville_slug = $composedRoute[1] ?? null;
            $category_slug = $composedRoute[2] ?? null;
            $slug = $composedRoute[3] ?? null;

            if (!$slug) {
                throw new Exception("Événement invalide");
            }

            $evenement = $this->repo->getEventBySlug($slug);
            if (!$evenement) {
                throw new Exception("L'événement demandé n'existe pas");
            }
            $idEvenement = $evenement['idEvenement'];
            // Verify that the ville_slug matches
            $ville = $this->repo->getVilleById($evenement['idVille']);
            if ($ville['ville_slug'] !== $ville_slug) {
                throw new Exception("L'événement demandé n'existe pas");
            }
            // verify if connected user is the owner or is subscribed to the event
            $isOwner = $this->repo->isEventOwner($idEvenement, $idUser);
            if ($isOwner) {
                throw new Exception("Vous ne pouvez pas vous inscrire à votre propre événement");
            }
            $subscription = $this->repo->getUserSubscription($idUser, $idEvenement);
            if ($subscription) {
                $status = $subscription['status'] ?? null;
            }


            // Check if status is already 'inscrit' or 'refuse' or 'annule' or 'liste_attente'
            if ($status === 'inscrit') {
                throw new Exception("Vous êtes déjà inscrit à cet événement");
            }
            if ($status === 'refuse') {
                throw new Exception("Votre inscription à cet événement a été refusée");
            }
            if ($status === 'liste_attente') {
                throw new Exception("Vous êtes déjà sur la liste d'attente pour cet événement");
            }
            if ($status === 'annule') {
                throw new Exception("Votre inscription à cet événement a été annulée, veuillez contacter l'organisateur.");
            }

            // Check if event is public
            if ($evenement['isPublic'] == 0) {
                throw new Exception("Vous ne pouvez pas vous inscrire à un événement privé");
            }
            // Check if event is deleted
            if ($evenement['isDeleted']) {
                throw new Exception("Vous ne pouvez pas vous inscrire à un événement supprimé");
            }

            // Check if event is full
            if ($evenement['maxParticipants'] > 0 && $evenement['currentParticipants'] >= $evenement['maxParticipants']) {
                throw new Exception("Cet événement est complet");
            }

            // Check if registration deadline has passed
            if ($evenement['registrationDeadline'] && strtotime($evenement['registrationDeadline']) < time()) {
                throw new Exception("La date limite d'inscription est dépassée");
            }

            // Register the user
            // if requiresApproval is true, set status to 'pending', else 'approved'
            if ($evenement['requiresApproval'] == 1) {
                $status = 'liste_attente';
            } else {
                $status = 'inscrit';
            }
            $idCreator = $evenement['idUser'];
            $idEvenement = $evenement['idEvenement'];
            if ($idCreator == $idUser) {
                throw new Exception("Vous ne pouvez pas vous inscrire à votre propre événement");
            }
            $subscription = $this->repo->registerUserForEventAndIncrementEventParticipants($idUser, $idEvenement, $status);
            if ($subscription) {
                //    push Notification
                // notify creator

                $notification = new NotificationRepository();
                $type =  ($status === 'liste_attente') ? 'preinscription' : 'inscription';
                $title = ($status === 'liste_attente') ? 'Nouvelle pré-inscription' : 'Nouvelle inscription';
                $message = "Un utilisateur vient de s'inscrire à votre événement : " . $evenement['title'];
                $urlCreator = "mes_evenements?action=voir&uiid=" . $evenement['uiid'];
                $dataCreator = [
                    'idUser' => $idCreator,
                    'idEvenement' => $idEvenement,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'url' => $urlCreator,
                    'createdAt' => (new DateTime())->format('Y-m-d H:i:s')
                ];

                $notifyCreator = $notification->pushNotification($dataCreator);
                $urlUser = "evenements/{$ville_slug}/{$category_slug}/{$slug}";
                // notify user
                $dataUser = [
                    'idUser' => $idUser,
                    'idEvenement' => $idEvenement,
                    'type' => $type,
                    'title' => ($status === 'liste_attente') ? 'Pré-inscription enregistrée' : 'Inscription confirmée',
                    'message' => "Vous êtes " . ($status === 'liste_attente' ? 'pré-inscrit' : 'inscrit') . " à l'événement : " . $evenement['title'],
                    'url' => $urlUser,
                    'createdAt' => (new DateTime())->format('Y-m-d H:i:s')
                ];
                $notifyUser = $notification->pushNotification($dataUser);
                // send email to user if max participants of the event less than 50 persons (to avoid spam)
                if ($evenement['maxParticipants'] < 50) {
                    $mail = new Mail();
                    $subject = ($status === 'liste_attente') ? "Pré-inscription enregistrée" : "Inscription confirmée";
                    $body = "Vous êtes " . ($status === 'liste_attente' ? 'pré-inscrit' : 'inscrit') . " à l'événement : <strong>" . $evenement['title'] . "</strong>.<br>";
                    $body .= "Détails de l'événement :<br>";
                    $body .= "Date et heure de début : " . date('d/m/Y H:i', strtotime($evenement['startDate'])) . "<br>";
                    $body .= "Lieu : " . ($evenement['address'] ?? 'Non spécifié') . "<br>";
                    $body .= "Ville : " . ($evenement['ville_nom_reel'] ?? 'Non spécifiée') . "<br>";
                    $body .= "Description : " . ($evenement['shortDescription'] ?? substr($evenement['description'], 0, 100) . '...') . "<br><br>";
                    $mail->sendEmail(ADMIN_EMAIL, ADMIN_SENDER_NAME, $_SESSION['email'], $_SESSION['firstName'], $subject, $body);
                }
                $_SESSION['success'] = "Votre inscription a été " . ($status === 'liste_attente' ? "enregistrée et est en attente d'approbation" : "confirmée") . " avec succès!";
                $this->redirect('evenements/' . $ville_slug . '/' . $category_slug . '/' . $slug);
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('evenements/' . $ville_slug  . '/' . $category_slug . '/' . $slug, ['error' => 'true']);
        }
    }
    public function acceptParticipant()
    {

        try {
            $idUser = $_SESSION['idUser'];
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
            $idEvenement = $this->getId();
            $idEventParticipant = isset($_POST['idEventParticipant']) ? (int)$_POST['idEventParticipant'] : null;

            if (!$idEvenement || !$idEventParticipant) {
                throw new Exception("Paramètres invalides");
            }

            $evenement = $this->repo->getEventCompleteById($idEvenement);
            if (!$evenement || $evenement['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cet événement");
            }

            // Check if event is full
            if ($evenement['maxParticipants'] > 0 && $evenement['currentParticipants'] >= $evenement['maxParticipants']) {
                throw new Exception("Cet événement est complet, vous ne pouvez pas accepter plus de participants");
            }

            $subscription = $this->repo->getSubscriptionById($idEventParticipant);
            if (!$subscription || $subscription['idEvenement'] != $idEvenement || $subscription['status'] !== 'liste_attente') {
                throw new Exception("Inscription introuvable ou déjà traitée");
            }

            // Accept the participant
            $acceptRequest = $this->repo->updateSubscriptionStatus($idEventParticipant, $idEvenement, $newStatus = 'inscrit');

            // Notify participant
            $notification = new NotificationRepository();
            $title = 'Inscription confirmée';
            $message = "Votre inscription à l'événement : " . $evenement['title'] . " a été confirmée.";
            $url = "evenements/" . $evenement['ville_slug'] . "/" . $evenement['category_slug'] . "/" . $evenement['slug'];
            $dataParticipant = [
                'idUser' => $subscription['idUser'],
                'idEvenement' => $idEvenement,
                'type' => 'inscription',
                'title' => $title,
                'message' => $message,
                'url' => $url,
                'createdAt' => (new DateTime())->format('Y-m-d H:i:s')
            ];
            $notifyParticipant = $notification->pushNotification($dataParticipant);

            // send email to participant if max participants of the event less than 50 persons (to avoid spam)
            if ($evenement['maxParticipants'] < 50) {
                $mail = new Mail();
                $subject = "Inscription confirmée";
                $body = "Votre inscription à l'événement : <strong>" . $evenement['title'] . "</strong> a été confirmée.<br>";
                $body .= "Détails de l'événement :<br>";
                $body .= "Date et heure de début : " . date('d/m/Y H:i', strtotime($evenement['startDate'])) . "<br>";
                $body .= "Lieu : " . ($evenement['address'] ?? 'Non spécifié') . "<br>";
                $body .= "Ville : " . ($evenement['ville_nom_reel'] ?? 'Non spécifiée') . "<br>";
                $body .= "Description : " . ($evenement['shortDescription'] ?? substr($evenement['description'], 0, 100) . '...') . "<br><br>";
                $mail->sendEmail(ADMIN_EMAIL, ADMIN_SENDER_NAME, $subscription['email'], $subscription['firstName'], $subject, $body);
            }
            $_SESSION['success'] = "Le participant a été accepté avec succès";
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid, 'error' => 'true']);
        }
    }
    public function refuseParticipant()
    {

        try {
            $idUser = $_SESSION['idUser'];
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
            $idEvenement = $this->getId();
            $idEventParticipant = isset($_POST['idEventParticipant']) ? (int)$_POST['idEventParticipant'] : null;

            if (!$idEvenement || !$idEventParticipant) {
                throw new Exception("Paramètres invalides");
            }

            $evenement = $this->repo->getEventCompleteById($idEvenement);
            if (!$evenement || $evenement['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cet événement");
            }

            $subscription = $this->repo->getSubscriptionById($idEventParticipant);
            if (!$subscription || $subscription['idEvenement'] != $idEvenement || !in_array($subscription['status'], ['liste_attente', 'inscrit'])) {
                throw new Exception("Inscription introuvable ou déjà traitée");
            }

            // Refuse the participant
            $refuseRequest = $this->repo->updateSubscriptionStatus($idEventParticipant, $idEvenement, $newStatus = 'refuse');

            // Notify participant
            $notification = new NotificationRepository();
            $title = 'Inscription refusée';
            $message = "Votre inscription à l'événement : " . $evenement['title'] . " a été refusée.";
            $url = "evenements/" . $evenement['ville_slug'] . "/" . $evenement['category_slug'] . "/" . $evenement['slug'];
            $dataParticipant = [
                'idUser' => $subscription['idUser'],
                'idEvenement' => $idEvenement,
                'type' => 'inscription',
                'title' => $title,
                'message' => $message,
                'url' => $url,
                'createdAt' => (new DateTime())->format('Y-m-d H:i:s')
            ];
            $notifyParticipant = $notification->pushNotification($dataParticipant);

            // send email to participant if max participants of the event less than 50 persons (to avoid spam)
            if ($evenement['maxParticipants'] < 50) {
                $mail = new Mail();
                $subject = "Inscription refusée";
                $body = "Votre inscription à l'événement : <strong>" . $evenement['title'] . "</strong> a été refusée.<br>";
                $body .= "Détails de l'événement :<br>";
                $body .= "Date et heure de début : " . date('d/m/Y H:i', strtotime($evenement['startDate'])) . "<br>";
                $body .= "Lieu : " . ($evenement['address'] ?? 'Non spécifié') . "<br>";
                $body .= "Ville : " . ($evenement['ville_nom_reel'] ?? 'Non spécifiée') . "<br>";
                $body .= "Description : " . ($evenement['shortDescription'] ?? substr($evenement['description'], 0, 100) . '...') . "<br><br>";
                $mail->sendEmail(ADMIN_EMAIL, ADMIN_SENDER_NAME, $subscription['email'], $subscription['firstName'], $subject, $body);
            }
            $_SESSION['success'] = "Le participant a été refusé avec succès";
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_evenements', ['action' => 'voir', 'uiid' => $uiid, 'error' => 'true']);
        }
    }
    public function likeEvent()
    {
        $idUser = $_SESSION['idUser'];
        $eventUiid = htmlspecialchars(trim($_POST['eventUiid'] ?? ''));
        $liked = $this->repo->toggleEventLikeByUiid($idUser, $eventUiid);
        echo json_encode(['success' => true, 'liked' => $liked]);
    }

    public function favouriteEvent()
    {
        $idUser = $_SESSION['idUser'];
        $eventUiid = htmlspecialchars(trim($_POST['eventUiid'] ?? ''));
        $favourited = $this->repo->toggleEventFavouriteByUiid($idUser, $eventUiid);
        echo json_encode(['success' => true, 'favourited' => $favourited]);
    }

    public function addEventComment()
    {
        $idUser = $_SESSION['idUser'];
        $eventUiid = htmlspecialchars(trim($_POST['eventUiid'] ?? ''));
        $content = trim($_POST['content'] ?? '');
        $parentUiid = isset($_POST['parentUiid']) ? htmlspecialchars(trim($_POST['parentUiid'])) : null;

        if (!$content) {
            echo json_encode(['success' => false, 'error' => 'Le commentaire ne peut pas être vide.']);
            return;
        }

        try {
            $commentUiid = $this->repo->addEventCommentByUiid($idUser, $eventUiid, $content, $parentUiid);
            echo json_encode(['success' => true, 'commentUiid' => $commentUiid]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function likeEventComment()
    {
        $idUser = $_SESSION['idUser'];
        $commentUiid = htmlspecialchars(trim($_POST['commentUiid'] ?? ''));
        $liked = $this->repo->toggleEventCommentLikeByUiid($idUser, $commentUiid);
        echo json_encode(['success' => true, 'liked' => $liked]);
    }

    public function reportEventComment()
    {
        $idUser = $_SESSION['idUser'];
        $commentUiid = htmlspecialchars(trim($_POST['commentUiid'] ?? ''));
        $reason = trim($_POST['reason'] ?? '');
        $this->repo->reportEventCommentByUiid($idUser, $commentUiid, $reason);
        echo json_encode(['success' => true]);
    }

    public function replyEventComment()
    {
        $idUser = $_SESSION['idUser'];
        $eventUiid = htmlspecialchars(trim($_POST['eventUiid'] ?? ''));
        $parentUiid = htmlspecialchars(trim($_POST['parentUiid'] ?? ''));
        $content = trim($_POST['content'] ?? '');

        if (!$content) {
            echo json_encode(['success' => false, 'error' => 'Le commentaire ne peut pas être vide.']);
            return;
        }

        try {
            // Get parent comment to find the user to mention
            $parentComment = $this->repo->getEventCommentByUiid($parentUiid);

            if (!$parentComment) {
                echo json_encode(['success' => false, 'error' => 'Commentaire parent introuvable.']);
                return;
            }

            // Get parent user details - we mention the DIRECT parent, not the root
            $parentUserRepo = new \src\Repositories\UserRepository();
            $parentUser = $parentUserRepo->getUserById($parentComment['idUser']);

            // Only add @mention if replying to someone else
            if ($parentComment['idUser'] != $idUser && $parentUser) {
                $mentionName = "@" . $parentUser->getFirstName() . " " . $parentUser->getLastName();
                // Add mention at the beginning of the content if not already present
                if (strpos($content, $mentionName) !== 0) {
                    $content = $mentionName . " " . $content;
                }
            }

            // For nested replies, we need to store them under the ROOT parent comment
            // Find the root parent comment UIID
            $rootParentUiid = $parentUiid;
            if ($parentComment['parentId']) {
                // This is a reply to a reply, so we need to find the root parent
                $tempParent = $this->repo->getEventCommentById($parentComment['parentId']);
                if ($tempParent) {
                    $rootParentUiid = $tempParent['uiid'];
                }
            }

            // Add comment using the ROOT parent UIID so all replies stay grouped
            $commentUiid = $this->repo->addEventCommentByUiid($idUser, $eventUiid, $content, $rootParentUiid);

            // Send notification to the mentioned user (the DIRECT parent author)
            if ($parentComment['idUser'] != $idUser && $parentUser) {
                $idEvenement = $this->repo->getIdByUiid($eventUiid);
                $event = $this->repo->getEventCompleteById($idEvenement);

                $notificationRepo = new NotificationRepository();
                $currentUser = $parentUserRepo->getUserById($idUser);

                $notificationData = [
                    'idUser' => $parentComment['idUser'],
                    'idEvenement' => $idEvenement,
                    'type' => 'mention',
                    'title' => 'Vous avez été mentionné',
                    'message' => $currentUser->getFirstName() . " " . $currentUser->getLastName() . " vous a mentionné dans un commentaire sur l'événement : " . $event['title'],
                    'url' => "evenements/" . $event['ville_slug'] . "/" . $event['category_slug'] . "/" . $event['slug'],
                    'createdAt' => (new DateTime())->format('Y-m-d H:i:s')
                ];

                $notificationRepo->pushNotification($notificationData);
            }

            echo json_encode(['success' => true, 'commentUiid' => $commentUiid]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function deleteEventComment()
    {
        $idUser = $_SESSION['idUser'];
        $commentUiid = htmlspecialchars(trim($_POST['commentUiid'] ?? ''));
        $comment = $this->repo->getEventCommentByUiid($commentUiid);

        if (!$comment) {
            echo json_encode(['success' => false, 'error' => 'Commentaire introuvable.']);
            return;
        }

        if ($comment['idUser'] != $idUser) {
            echo json_encode(['success' => false, 'error' => 'Vous ne pouvez supprimer que vos propres commentaires.']);
            return;
        }

        $this->repo->deleteEventCommentWithRepliesByUiid($commentUiid);
        echo json_encode(['success' => true]);
    }
    public function getAllMyFavouriteEvents()
    {
        try {
            $idUser = $_SESSION['idUser'];

            // Get all favourite events with pagination
            $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $evenementsPerPage = 12;
            $favouriteEvents = $this->repo->getUserFavouriteEvents($idUser, $currentPage, $evenementsPerPage);
            $totalEvenements = $this->repo->countUserFavouriteEvents($idUser);
            $totalPages = (int)ceil($totalEvenements / $evenementsPerPage);
            $this->render('user/mes_favoris', [
                'favouriteEvents' => $favouriteEvents,
                'title' => 'Mes événements favoris',
                'total' => $totalEvenements,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/', ['error' => 'true']);
        }
    }
    public function removeFavoriteEvent()
    {
        try {
            $idUser = $_SESSION['idUser'];
            $eventUiid = isset($_POST['eventUiid']) ? htmlspecialchars(trim($_POST['eventUiid'])) : null;
            
            if (!$eventUiid) {
                throw new Exception("Événement invalide");
            }
            
            $idEvenement = $this->repo->getIdByUiid($eventUiid);
            if (!$idEvenement) {
                throw new Exception("Événement introuvable");
            }
            
            // Remove from favorites
            $this->repo->toggleEventFavourite($idUser, $idEvenement);
            
            $_SESSION['success'] = "L'événement a été retiré de vos favoris";
            $this->redirect('mes_favoris');
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_favoris', ['error' => 'true']);
        }
    }
}
