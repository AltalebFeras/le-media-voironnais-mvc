<?php

use src\Controllers\AdminController;
use src\Controllers\HomeController;
use src\Controllers\UserController;
use src\Controllers\AssociationController;
use src\Controllers\EntrepriseController;
use src\Services\ConfigRouter;

$homeController = new HomeController();
$userController = new UserController();
$adminController = new AdminController();
$associationController = new AssociationController();
$entrepriseController = new EntrepriseController();

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
        if ($method === 'GET' && $connectionSecured && !isset($_GET['action'])) {
            $associationController->mesAssociations();
        } else if ($method === 'GET' && $connectionSecured && isset($_GET['action']) && $_GET['action'] === 'voir' && isset($_GET['id'])) {
            $associationController->displayAssociationDetails();
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
        if ($method === 'POST' && $connectionSecured) {
            $associationController->updateAssociation();
        } elseif ($connectionSecured && $method === 'GET') {
            $associationController->showEditForm();
        } else {
            $_SESSION['errors'] = ['Vous devez être connecté pour accéder à cette page.'];
            $homeController->displayAuth();
        }
        break;
    case HOME_URL . 'association/supprimer':
        if ($method === 'POST' && $connectionSecured) {
            $associationController->deleteAssociation();
        } else {
            $homeController->page404();
        }
        break;

    // Entreprise routes
    case HOME_URL . 'mes_entreprises':
        if ($method === 'GET' && $connectionSecured) {
            $entrepriseController->mesEntreprises();
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
        if ($method === 'POST' && $connectionSecured) {
            $entrepriseController->updateEntreprise();
        } elseif ($connectionSecured && $method === 'GET') {
            $entrepriseController->showEditForm();
        } else {
            $entrepriseController->showEditForm();
        }
        break;
    case HOME_URL . 'entreprise/supprimer':
        if ($method === 'POST' && $connectionSecured) {
            $entrepriseController->deleteEntreprise();
        } else {
            $homeController->page404();
        }
        break;

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
