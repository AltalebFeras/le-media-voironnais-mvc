<?php

use src\Controllers\AdminController;
use src\Controllers\HomeController;
use src\Controllers\UserController;
use src\Services\ConfigRouter;

$homeController = new HomeController();
$userController = new UserController();
$adminController = new AdminController();

$route = $_SERVER['REDIRECT_URL'] ?? '/';
$method = ConfigRouter::getMethod();

// var_dump($_SERVER);
$connectionSecured = isset($_SESSION['connected']) && $_SESSION['role'] === 'user' && ConfigRouter::checkConnection();
$connectionSecuredAdmin = isset($_SESSION['connectedAdmin']) && $_SESSION['role'] === 'admin' && ConfigRouter::checkConnection();
$connectionSecuredSuperAdmin = isset($_SESSION['connectedSuperAdmin']) && $_SESSION['role'] === 'super_admin' && ConfigRouter::checkConnection();

switch ($route) {

    case HOME_URL:
        $homeController->displayHomepage();
        break;

    case HOME_URL . 'connexion':
        if ($method === 'POST') {
            $userController->treatmentConnexion();
        } else {
            if ($connectionSecured) {
                $userController->displayDashboard();
            } else {
                $homeController->displayFormConnexion();
            }
        }
        break;

    case HOME_URL . 'inscription':
        if ($method === 'POST') {
            $userController->treatmentInscription();
        } else {
            if ($connectionSecured) {
                $userController->displayDashboard();
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

    case HOME_URL . 'dashboard':
        if ($connectionSecured) {
            $userController->displayDashboard();
        } else {
            $homeController->displayHomepage();
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
            $homeController->page404();
        }
        break;
    
    case HOME_URL . 'dashboard_admin':
        if ($connectionSecuredAdmin) {
            $adminController->displayAdminDashboard();
        } else {
            $homeController->displayHomepage();
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
