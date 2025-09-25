<?php

use src\Controllers\AdminController;
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

$route = $_SERVER['REDIRECT_URL'] ?? '/';
$method = ConfigRouter::getMethod();

// var_dump($_SERVER);
$connectionSecured = isset($_SESSION['connected']) && $_SESSION['role'] === 'user' && ConfigRouter::checkConnection();
$connectionSecuredAdmin = isset($_SESSION['connectedAdmin']) && $_SESSION['role'] === 'admin' && ConfigRouter::checkConnection();
$connectionSecuredSuperAdmin = isset($_SESSION['connectedSuperAdmin']) && $_SESSION['role'] === 'super_admin' && ConfigRouter::checkConnection();

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
    case HOME_URL . 'evenements':
        $evenementController->listEvents();
        break;
    // User routes

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
            } else if ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['uiid'])) {
                $associationController->deleteAssociation();
            } elseif ($method === 'GET') {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'association/ajouter':
        if ($method === 'POST' && $connectionSecured) {
            $associationController->addAssociation();
        } elseif ($connectionSecured && $method === 'GET') {
            $associationController->showAddForm();
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'association/modifier':
        if ($connectionSecured) {
            if ($method === 'POST') {
                if (isset($_GET['action']) && $_GET['action'] === 'modifier_banner') {
                    $associationController->updateBanner();
                } elseif (isset($_GET['action']) && $_GET['action'] === 'supprimer_banner') {
                    $associationController->deleteBanner();
                } elseif (isset($_GET['action']) && $_GET['action'] === 'modifier_logo') {
                    $associationController->updateLogo();
                } elseif (isset($_GET['action']) && $_GET['action'] === 'supprimer_logo') {
                    $associationController->deleteLogo();
                } elseif (!$_GET['action']) {
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

    // Entreprise routes
    case HOME_URL . 'mes_entreprises':
        if ($connectionSecured) {
            if ($method === 'GET' && !isset($_GET['uiid'])) {
                $entrepriseController->mesEntreprises();
            } elseif ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'voir' && isset($_GET['uiid'])) {
                $entrepriseController->displayEntrepriseDetails();
            } elseif ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['uiid'])) {
                $entrepriseController->deleteEntreprise();
            } else {
                $homeController->page404();
            }
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'entreprise/ajouter':
        if ($method === 'POST' && $connectionSecured) {
            $entrepriseController->addEntreprise();
        } elseif ($connectionSecured && $method === 'GET') {
            $entrepriseController->showAddForm();
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'entreprise/modifier':
        if ($connectionSecured) {
            if ($method === 'POST') {
                if (isset($_GET['action']) && $_GET['action'] === 'modifier_banner') {
                    $entrepriseController->updateBanner();
                } elseif (isset($_GET['action']) && $_GET['action'] === 'supprimer_banner') {
                    $entrepriseController->deleteBanner();
                } elseif (isset($_GET['action']) && $_GET['action'] === 'modifier_logo') {
                    $entrepriseController->updateLogo();
                } elseif (isset($_GET['action']) && $_GET['action'] === 'supprimer_logo') {
                    $entrepriseController->deleteLogo();
                } elseif (!isset($_GET['action'])) {
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
    case HOME_URL . 'entreprise/demander_activation_mon_entreprise':
        if ($method === 'POST' && isset($_GET['uiid'])) {
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
            } elseif ($method === 'POST' && isset($_GET['entreprise_uiid']) && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['realisation_uiid'])) {
                $realisationController->deleteRealisation();
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

    // Evenement routes
    case HOME_URL . 'mes_evenements':
        if ($connectionSecured) {
            if ($method === 'GET' && !isset($_GET['uiid'])) {
                $evenementController->mesEvenements();
            } elseif ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'voir' && isset($_GET['uiid'])) {
                $evenementController->displayEventDetails();
            } elseif ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['uiid'])) {
                $evenementController->deleteEvent();
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
                if (isset($_GET['action']) && $_GET['action'] === 'modifier_banner') {
                    $evenementController->updateBanner();
                } elseif (isset($_GET['action']) && $_GET['action'] === 'supprimer_banner') {
                    $evenementController->deleteBanner();
                } elseif (isset($_GET['action']) && $_GET['action'] === 'ajouter_image') {
                    $evenementController->addEventImage();
                } elseif (isset($_GET['action']) && $_GET['action'] === 'supprimer_image') {
                    $evenementController->deleteEventImage();
                } elseif (!isset($_GET['action'])) {
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
        if ($method === 'POST' && $_GET['action'] && ($connectionSecured || $connectionSecuredAdmin || $connectionSecuredSuperAdmin)) {
            switch ($_GET['action']) {
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
                    $homeController->page403();
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
        if ($connectionSecuredAdmin && $method === 'POST' && isset($_GET['action'])) {
            // POST action for block/unblock/send_email
            switch ($_GET['action']) {
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

    default:
        $homeController->page404();
        break;
}
