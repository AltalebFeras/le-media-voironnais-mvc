<?php

use GuzzleHttp\Psr7\Message;
use src\Controllers\AdminController;
use src\Controllers\ContactController;
use src\Controllers\EvenementController;
use src\Controllers\HomeController;
use src\Controllers\NotificationController;
use src\Controllers\RealisationController;
use src\Controllers\UserController;
use src\Controllers\AssociationController;
use src\Controllers\EntrepriseController;
use src\Services\ConfigRouter;

$homeController = new HomeController();
$userController = new UserController();
$adminController = new AdminController();
$associationController = new AssociationController();
$entrepriseController = new EntrepriseController();
$evenementController = new EvenementController();
$realisationController = new RealisationController();
$notificationController = new NotificationController();
$contactController = new ContactController();

$route = $_SERVER['REDIRECT_URL'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'];

$connectionSecured = isset($_SESSION['connected']) && $_SESSION['role'] === 'user' && ConfigRouter::checkConnection();
$connectionSecuredAdmin = isset($_SESSION['connectedAdmin']) && $_SESSION['role'] === 'admin' && ConfigRouter::checkConnection();
$connectionSecuredSuperAdmin = isset($_SESSION['connectedSuperAdmin']) && $_SESSION['role'] === 'super_admin' && ConfigRouter::checkConnection();

$composedRoute = ConfigRouter::getComposedRoute($route);
// url = DOMAIN . HOME_URL part0/part1/part2/part3/part4/part5
//['part0' => 'evenements', 'part1' => 'ville-slug', 'part2' => 'cate-slug', 'part3' => 'inscription']
$part0 = $composedRoute[0] ?? null;
$part1 = $composedRoute[1] ?? null;
$part2 = $composedRoute[2] ?? null;
$part3 = $composedRoute[3] ?? null;
$part4 = $composedRoute[4] ?? null;
$part5 = $composedRoute[5] ?? null;

switch ($route) {
    // Public routes

    case HOME_URL:
        $homeController->displayHomepage();
        break;
    // Authentication routes
    case HOME_URL . 'connexion':
        if ($method === 'POST') {
            $userController->treatmentConnexion();
        } else {
            if ($connectionSecured) {
                $userController->displayDashboard();
            } elseif ($connectionSecuredAdmin) {
                $adminController->displayAdminDashboard();
            } elseif ($connectionSecuredSuperAdmin) {
                $adminController->displayAdminDashboard();
            } else {
                $homeController->displayAuth();
            }
        }
        break;

    case HOME_URL . 'inscription':
        if ($method === 'POST') {
            $userController->treatmentInscription();
        } else {
            if ($connectionSecured) {
                $userController->displayDashboard();
            } elseif ($connectionSecuredAdmin) {
                $adminController->displayAdminDashboard();
            } elseif ($connectionSecuredSuperAdmin) {
                $adminController->displayAdminDashboard();
            } else {
                $homeController->displayFormInscription();
            }
        }
        break;

    case HOME_URL . 'activer_mon_compte':
        if ($method === 'GET' && $_GET['token']) {
            $userController->activateAccount();
        } else {
            $homeController->page404();
        }
        break;
    case HOME_URL . 'mdp_oublie':
        if ($method === 'POST') {
            $userController->treatmentForgotMyPassword();
        } else {
            if ($connectionSecured) {
                $userController->displayDashboard();
            } else {
                $homeController->displayFormForgetPassword();
            }
        }
        break;
    case HOME_URL . 'reinit_mon_mot_de_passe':
        if ($method === 'POST') {
            $userController->treatmentResetPassword();
        } else {
            if ($connectionSecured) {
                $userController->displayDashboard();
            } else {
                if (isset($_GET['token'])) {
                    $homeController->displayFormResetPassword();
                } else {
                    $homeController->page404();
                }
            }
        }
        break;
    // events public routes
    // Example: /evenements/ville-slug or /evenements/ville-slug/category_slug/event-slug /evenements/ville-slug/category_slug
    case $part0 === 'evenements':
        if ($part1 && !$part2) {
            // $evenementController->displayEventsForThisVille($composedRoute);
        } elseif ($part2 && !$part3) {
            // $evenementController->viewEventByVilleSlugAndCategorySlug($composedRoute);
        } elseif ($part3 && !$part4) {
            if ($method === 'POST') {
                $evenementController->inscriptionEvent($composedRoute);
            } else {
                $evenementController->viewEventBySlug($composedRoute);
            }
        } elseif ($part4) {
            $homeController->page404();
        } else {
            $evenementController->listEvents();
        }
        break;

    // Event like/favourite/comment endpoints (AJAX)
    case HOME_URL . 'evenement/interactions':
        if ($method === 'GET') {
            $evenementController->getEventInteractions();
        } else {
            $homeController->page404();
        }
        break;

    case HOME_URL . 'evenement/like':
    case HOME_URL . 'evenement/favourite':
    case HOME_URL . 'evenement/comment':
    case HOME_URL . 'evenement/comment/like':
    case HOME_URL . 'evenement/comment/report':
    case HOME_URL . 'evenement/comment/reply':
    case HOME_URL . 'evenement/comment/delete':
        if ($method === 'POST' && $connectionSecured) {
            switch ($part1) {
                case 'like':
                    $evenementController->likeEvent();
                    break;
                case 'favourite':
                    $evenementController->favouriteEvent();
                    break;
                case 'comment':
                    // make switch case for  like, report, reply, delete
                    switch ($part2) {
                        case 'like':
                            $evenementController->likeEventComment();
                            break;
                        case 'report':
                            $evenementController->reportEventComment();
                            break;
                        case 'reply':
                            $evenementController->replyEventComment();
                            break;
                        case 'delete':
                            $evenementController->deleteEventComment();
                            break;
                        default:
                            $evenementController->addEventComment();
                    }
                    break;
                default:
                    $homeController->page404();
                    break;
            }
        } else {
            $homeController->page404();
        }
        break;


    case HOME_URL . 'dashboard':
        if ($connectionSecured) {
            $userController->displayDashboard();
        } else {
            $homeController->displayAuth();
        }
        break;

    case HOME_URL . 'villes':
        if ($method === 'POST' && $connectionSecured) {
            $associationController->getVilles();
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette pagess.'];
            $homeController->displayAuth();
        }
        break;
    // Association routes
    case HOME_URL . 'mes_associations':
        if ($connectionSecured) {
            if ($method === 'GET' && !isset($_GET['action']) && !isset($_GET['uiid'])) {
                $associationController->mesAssociations();
            } else if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'voir' && isset($_GET['uiid'])) {
                $associationController->displayAssociationDetails();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'association/ajouter':
        if ($connectionSecured) {
            if ($method === 'POST') {
                $associationController->addAssociation();
            } elseif ($method === 'GET') {
                $associationController->showAddForm();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'association/modifier':
        if ($connectionSecured) {
            if ($method === 'POST') {
                if (isset($_POST['action']) && $_POST['action'] === 'modifier_banner') {
                    $associationController->updateBanner();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'supprimer_banner') {
                    $associationController->deleteBanner();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'modifier_logo') {
                    $associationController->updateLogo();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'supprimer_logo') {
                    $associationController->deleteLogo();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'modifier_association') {
                    $associationController->updateAssociation();
                } else {
                    $homeController->page404();
                }
            } elseif ($method === 'GET' && isset($_GET['uiid'])) {
                $associationController->showEditForm();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'association/supprimer':
        if ($method === 'POST') {
            $associationController->deleteAssociation();
        } else {
            $homeController->page404();
        }
        break;

    // Entreprise routes
    case HOME_URL . 'mes_entreprises':
        if ($connectionSecured) {
            if ($method === 'GET' && !isset($_GET['action']) && !isset($_GET['uiid'])) {
                $entrepriseController->mesEntreprises();
            } elseif ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'voir' && isset($_GET['uiid'])) {
                $entrepriseController->displayEntrepriseDetails();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'entreprise/ajouter':
        if ($connectionSecured) {
            if ($method === 'POST') {
                $entrepriseController->addEntreprise();
            } elseif ($method === 'GET') {
                $entrepriseController->showAddForm();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;

    case HOME_URL . 'entreprise/modifier':
        if ($connectionSecured) {
            if ($method === 'POST') {
                if (isset($_POST['action']) && $_POST['action'] === 'modifier_banner') {
                    $entrepriseController->updateBanner();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'supprimer_banner') {
                    $entrepriseController->deleteBanner();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'modifier_logo') {
                    $entrepriseController->updateLogo();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'supprimer_logo') {
                    $entrepriseController->deleteLogo();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'modifier_entreprise') {
                    $entrepriseController->updateEntreprise();
                } else {
                    $homeController->page404();
                }
            } elseif ($method === 'GET' && isset($_GET['uiid'])) {
                $entrepriseController->showEditForm();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;

    case HOME_URL . 'entreprise/supprimer':
        if ($method === 'POST') {
            $entrepriseController->deleteEntreprise();
        } else {
            $homeController->page404();
        }
        break;

    case HOME_URL . 'entreprise/demander_activation_mon_entreprise':
        if ($method === 'POST') {
            $entrepriseController->demanderActivation();
        } else {
            $homeController->page404();
        }
        break;
    // Realisation routes
    case HOME_URL . 'entreprise/mes_realisations':
        if ($connectionSecured) {
            if ($method === 'GET' && isset($_GET['entreprise_uiid']) && !isset($_GET['action'])) {
                $realisationController->mesRealisations();
            } elseif ($method === 'GET' && isset($_GET['entreprise_uiid']) && isset($_GET['action']) && $_GET['action'] === 'voir' && isset($_GET['realisation_uiid'])) {
                $realisationController->displayRealisationDetails();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'entreprise/mes_realisations/ajouter':
        if ($connectionSecured) {
            if ($method === 'POST') {
                $realisationController->addRealisation();
            } elseif ($method === 'GET' && isset($_GET['entreprise_uiid'])) {
                $realisationController->showAddRealisationForm();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;

    case HOME_URL . 'entreprise/mes_realisations/modifier':
        if ($connectionSecured) {
            if ($method === 'POST') {
                $realisationController->updateRealisation();
            } elseif ($method === 'GET' && isset($_GET['realisation_uiid'])) {
                $realisationController->showEditRealisationForm();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;

    case HOME_URL . 'entreprise/mes_realisations/supprimer':
        if ($method === 'POST') {
            $realisationController->deleteRealisation();
        } else {
            $homeController->page404();
        }
        break;

    // Evenement routes
    case HOME_URL . 'mes_evenements':
        if ($connectionSecured) {
            if ($method === 'GET' && !isset($_GET['uiid'])) {
                $evenementController->mesEvenements();
            } elseif ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'voir' && isset($_GET['uiid'])) {
                $evenementController->displayEventDetails();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'evenement/ajouter':
        if ($method === 'POST' && $connectionSecured) {
            $evenementController->addEvent();
        } elseif ($connectionSecured && $method === 'GET') {
            $evenementController->showAddEventForm();
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'evenement/modifier':
        if ($connectionSecured) {
            if ($method === 'POST') {
                if (isset($_POST['action']) && $_POST['action'] === 'modifier_banner') {
                    $evenementController->updateBanner();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'supprimer_banner') {
                    $evenementController->deleteBanner();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'ajouter_image') {
                    $evenementController->addEventImage();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'supprimer_image') {
                    $evenementController->deleteEventImage();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'modifier_evenement') {
                    $evenementController->updateEvent();
                } else {
                    $homeController->page404();
                }
            } elseif ($method === 'GET' && isset($_GET['uiid'])) {
                $evenementController->showEditEventForm();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'evenement/supprimer':
        if ($method === 'POST') {
            $evenementController->deleteEvent();
        } else {
            $homeController->page404();
        }
        break;

    case HOME_URL . 'mes_evenement/participants':
        if ($method === 'POST' && $connectionSecured) {
            if (isset($_POST['action']) && $_POST['action'] === 'accepter') {
                $evenementController->acceptParticipant();
            } elseif (isset($_POST['action']) && $_POST['action'] === 'refuser') {
                $evenementController->refuseParticipant();
            } else {
                $homeController->page404();
            }
        } else {
            $homeController->page404();
        }
        break;

    case HOME_URL . 'mes_favoris':
        if ($connectionSecured) {
            if ($method === 'POST') {
                if (isset($_POST['action']) && $_POST['action'] === 'remove_favorite') {
                    $evenementController->removeFavoriteEvent();
                } else {
                    $homeController->page404();
                }
            } else {
                $evenementController->getAllMyFavouriteEvents();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;

    // Notifications routes
    case HOME_URL . 'notifications':
        if ($connectionSecured) {
            if ($method === 'GET') {
                $notificationController->displayNotifications();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;

    case HOME_URL . 'notifications/count':
        if ($connectionSecured) {
            if ($method === 'GET') {
                $notificationController->notificationsCount();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;

    case HOME_URL . 'notifications/list':
        if ($connectionSecured) {
            if ($method === 'GET') {
                $notificationController->notificationsList();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;

    case HOME_URL . 'notifications/mark-read':
        if ($connectionSecured) {
            if ($method === 'POST') {
                $notificationController->notificationMarkRead();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;

    case HOME_URL . 'notifications/mark-all-read':
        if ($connectionSecured) {

            if ($method === 'POST') {
                $notificationController->notificationsMarkAllRead();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;

    //  User account routes
    case HOME_URL . 'mon_compte':
        if ($method === 'POST' && $_POST['action'] && ($connectionSecured || $connectionSecuredAdmin || $connectionSecuredSuperAdmin)) {
            switch ($_POST['action']) {
                case 'delete_account':
                    $userController->deleteAccount();
                    break;
                case 'change_password':
                    $userController->changePassword();
                    break;
                case 'edit_profile':
                    $userController->editProfile();
                    break;
                case 'add_phone':
                    $userController->addPhone();
                    break;
                case 'add_bio':
                    $userController->addBio();
                    break;
                case 'add_date_of_birth':
                    $userController->addDateOfBirth();
                    break;
                case 'edit_email':
                    $userController->editEmail();
                    break;
                case 'validate_new_email':
                    $userController->validateNewEmail();
                    break;
                case 'cancel_email_change':
                    $userController->cancelEmailChange();
                    break;
                case 'edit_profile_picture':
                    $userController->editProfilePicture();
                    break;
                case 'delete_profile_picture':
                    $userController->deleteProfilePicture();
                    break;
                case 'edit_banner':
                    $userController->editBanner();
                    break;
                case 'delete_banner':
                    $userController->deleteBanner();
                    break;
                default:
                    $homeController->page404();
                    break;
            }
        } elseif ($method === 'GET' && ($connectionSecured || $connectionSecuredAdmin || $connectionSecuredSuperAdmin)) {
            $userController->displayMyAccount();
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    // Admin routes

    case HOME_URL . 'admin/dashboard_admin':
        if ($connectionSecuredAdmin) {
            $adminController->displayAdminDashboard();
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;

    case HOME_URL . 'admin/tous_les_utilisateurs':
        if ($connectionSecuredAdmin && $method === 'POST') {
            // Handle any POST actions for user management here if needed
        } elseif ($connectionSecuredAdmin && $method === 'GET') {
            $adminController->displayAllUsers();
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;

    case HOME_URL . 'admin/utilisateur_details':
        if ($connectionSecuredAdmin && $method === 'POST' && isset($_POST['action'])) {
            // POST action for block/unblock/send_email
            switch ($_POST['action']) {
                case 'block':
                    $adminController->blockUser();
                    break;
                case 'unblock':
                    $adminController->unblockUser();
                    break;
                case 'send_email':
                    $adminController->sendEmailToUser();
                    break;
                default:
                    $homeController->page404();
                    exit;
            }
        } elseif ($connectionSecuredAdmin && $method === 'GET' && isset($_GET['id']) && is_numeric($_GET['id'])) {
            $adminController->displayUserById();
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'admin/toutes_demandes_dactivation_entreprise':
        if ($connectionSecuredAdmin && $method === 'POST') {
            if (isset($_POST['action']) && $_POST['action'] === 'accept') {
                $adminController->acceptEntrepriseActivationRequest();
            } elseif (isset($_POST['action']) && $_POST['action'] === 'refuse') {
                $adminController->refuseEntrepriseActivationRequest();
            } else {
                $homeController->page404();
            }
        } elseif ($connectionSecuredAdmin && $method === 'GET') {
            $adminController->displayAllEntreprisesActivationRequests();
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    // Contact form (public)
    case HOME_URL . 'nous_contacter':
        if ($method === 'POST') {
            $contactController->submitContactForm();
        } else {
            $contactController->displayContactForm();
        }
        break;
    case HOME_URL . 'cgu':
        $homeController->terms_of_service();
        break;
    case HOME_URL . 'mentions_legales':
        $homeController->mentions_legales();
        break;
    case HOME_URL . 'deconnexion':
        $userController->deconnexion();
        break;
    case HOME_URL . '404':
        $homeController->page404();
        break;

    // Admin contact routes - consolidated into single route with POST actions
    case HOME_URL . 'admin/contacts':
        if ($connectionSecuredAdmin) {
            if ($method === 'GET') {
                $contactController->displayAllContacts();
            } elseif ($method === 'POST') {
                // Handle different actions via POST parameter
                $action = isset($_POST['action']) ? $_POST['action'] : null;
                
                switch ($action) {
                    case 'mark_read':
                        $contactController->markContactAsRead();
                        break;
                    case 'archive':
                        $contactController->archiveContact();
                        break;
                    case 'reply':
                        $contactController->replyToContact();
                        break;
                    case 'delete':
                        $contactController->deleteContact();
                        break;
                    default:
                        $homeController->page404();
                        break;
                }
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;

    default:
        $homeController->page404();
        break;
}
