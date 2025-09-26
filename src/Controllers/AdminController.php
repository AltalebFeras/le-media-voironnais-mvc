<?php

namespace src\Controllers;

use Exception;
use src\Abstracts\AbstractController;
use src\Repositories\AdminRepository;
use src\Repositories\NotificationRepository;
use src\Services\Mail;

use function PHPSTORM_META\type;

class AdminController extends AbstractController
{
    protected $repo;
    protected $notificationRepo;

    public function __construct()
    {
        $this->repo = new AdminRepository();
        $this->notificationRepo = new NotificationRepository();
    }
    public function displayAdminDashboard()
    {
        $this->render('admin/dashboard_admin');
    }
    public function displayAllUsers()
    {
        // Récupérer tous les utilisateurs avec des paginations
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $usersPerPage = 12;
        $allUsers = $this->repo->findAllUsers($currentPage, $usersPerPage);
        $totalUsers = $this->repo->countUsers();
        $totalPages = (int)ceil($totalUsers / $usersPerPage);

        $this->render('admin/tous_les_utilisateurs', [
            'allUsers' => $allUsers,
            'currentPage' => $currentPage,
            'usersPerPage' => $usersPerPage,
            'totalUsers' => $totalUsers,
            'totalPages' => $totalPages
        ]);
    }

    public function displayUserById()
    {
        $idUser = is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
        $errors = [];
        if (!$idUser) {
            $errors['id'] = "ID utilisateur manquant ou invalide.";
            $this->returnAllErrors($errors, 'admin/utilisateur_details?id=' . $idUser . '&error=true');
        }
        $user = $this->repo->findById($idUser);
        if (!$user) {
            $errors['user'] = "Utilisateur non trouvé.";
            $this->returnAllErrors($errors, 'admin/utilisateur_details?id=' . $idUser . '&error=true');
        }

        $this->render('admin/utilisateur_details', ['user' => $user]);
    }

    // Handle POST actions from utilisateur_details (block, unblock, send_email)

    public function blockUser()
    {

        $idUser = is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
        $errors = [];

        if (!$idUser) {
            $errors['id'] = "ID utilisateur manquant ou invalide.";
        }
        $user = $this->repo->findById($idUser);
        if (!$user) {
            $errors['user'] = "Utilisateur non trouvé.";
        }
        $this->returnAllErrors($errors, 'admin/utilisateur_details?id=' . $idUser . '&error=true');
        $blockSuccess = $this->repo->setBannedStatus($idUser, true);
        if (!$blockSuccess) {
            $errors['block'] = "Échec du blocage de l'utilisateur. Veuillez réessayer.";
            $this->returnAllErrors($errors, 'admin/utilisateur_details?id=' . $idUser . '&error=true');
        }
        $_SESSION['success'] = "L'utilisateur a été banni avec succès.";
        $this->redirect('admin/utilisateur_details?id=' . $idUser);
    }
    public function unblockUser()
    {
        $idUser = is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
        $errors = [];

        if (!$idUser) {
            $errors['id'] = "ID utilisateur manquant ou invalide.";
        }
        $user = $this->repo->findById($idUser);
        if (!$user) {
            $errors['user'] = "Utilisateur non trouvé.";
        }

        $this->returnAllErrors($errors, 'admin/utilisateur_details?id=' . $idUser . '&error=true');
        $unblockSuccess = $this->repo->setBannedStatus($idUser, false);
        if (!$unblockSuccess) {
            $errors[] = "Échec du déblocage de l'utilisateur. Veuillez réessayer.";
            $this->returnAllErrors($errors, 'admin/utilisateur_details?id=' . $idUser . '&error=true');
        }
        $_SESSION['success'] = "L'utilisateur a été débanni avec succès.";
        $this->redirect('admin/utilisateur_details?id=' . $idUser);
    }
    public function sendEmailToUser(): void
    {
        try {
            $idUser = is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
            $errors = [];
            $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : '';
            $body = isset($_POST['body']) ? htmlspecialchars(trim($_POST['body'])) : '';
            $user = $this->repo->findById($idUser);

            // Safely get recipient email/name
            $recipientEmail = isset($user['email']) ? trim($user['email']) : '';
            $recipientName = isset($user['firstName']) ? trim($user['firstName']) : '';

            // Validation
            if (empty($recipientEmail) || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Adresse email invalide.";
            }
            // Require subject and body individually
            if (empty($subject)) {
                $errors['subject'] = "Le sujet est requis.";
            } elseif (strlen($subject) > 255 || strlen($subject) < 3) {
                $errors['subject'] = "Le sujet doit contenir entre 3 et 255 caractères.";
            }

            if (empty($body)) {
                $errors['body'] = "Le message est requis.";
            } elseif (strlen($body) > 1000 || strlen($body) < 10) {
                $errors['body'] = "Le message doit contenir entre 10 et 1000 caractères.";
            }

            if (!$idUser) {
                $errors['id'] = "ID utilisateur manquant ou invalide.";
            }
            if (!$user) {
                $errors['user'] = "Utilisateur non trouvé.";
            }

            // If there are validation errors, store them and redirect so messages.php can display them
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $this->redirect('admin/utilisateur_details?id=' . $idUser . '&error=true');
                return;
            }

            // Send email
            $mail = new Mail();
            $sent = $mail->sendEmail(ADMIN_EMAIL, ADMIN_SENDER_NAME, $recipientEmail, $recipientName, $subject, $body);
            if ($sent) {
                $_SESSION['success'] = "L'email a été envoyé avec succès.";
                $this->redirect('admin/utilisateur_details?id=' . $idUser);
                return;
            } else {
                $_SESSION['errors'] = ['global' => "Erreur lors de l'envoi de l'email."];
                $this->redirect('admin/utilisateur_details?id=' . $idUser . '&error=true');
                return;
            }
        } catch (Exception $e) {
            $errors['global'] = "Erreur lors de l'envoi de l'email.";
            $_SESSION['errors'] = $errors;
            $this->redirect('admin/utilisateur_details?id=' . $idUser . '&error=true');
            return;
        }
    }
    public function displayAllEntreprisesActivationRequests(): void
    {
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $requestsPerPage = 12;
        $allRequests = $this->repo->findAllActivationRequests($currentPage, $requestsPerPage);
        $totalRequests = $this->repo->countActivationRequests();
        $totalPages = (int)ceil($totalRequests / $requestsPerPage);

        $this->render('admin/toutes_demandes_dactivation_entreprise', [
            'allRequests' => $allRequests,
            'currentPage' => $currentPage,
            'requestsPerPage' => $requestsPerPage,
            'totalRequests' => $totalRequests,
            'totalPages' => $totalPages
        ]);
    }

    public function acceptEntrepriseActivationRequest(): void
    {
        try {
            $uiid = isset($_POST['entreprise_uiid']) ? trim($_POST['entreprise_uiid']) : '';
            $message = isset($_POST['message']) ? trim($_POST['message']) : '';
            
            if (empty($uiid)) {
                throw new Exception("UIID de l'entreprise manquant.");
            }

            $entreprise = $this->repo->findEntrepriseByUiid($uiid);
            if (!$entreprise) {
                throw new Exception("Entreprise non trouvée.");
            }

            $acceptSuccess = $this->repo->acceptActivationRequest($uiid);
            if (!$acceptSuccess) {
                throw new Exception("Échec de l'acceptation de la demande. Veuillez réessayer.");
            }
            if ($acceptSuccess && !empty($entreprise['email'])) {
                $mail = new Mail();
                $subject = "Activation de votre compte entreprise";
                $body = "Votre compte entreprise a été activé avec succès. Vous pouvez désormais accéder à toutes les fonctionnalités.<br><br>" .
                    "Cordialement,<br>L'équipe du Média Voironnais";
                $body .= !empty($message) ? "<br><br>Message de l'administrateur :<br>" . nl2br(htmlspecialchars($message)) : "";

                $mail->sendEmail(ADMIN_EMAIL, ADMIN_SENDER_NAME, $entreprise['email'], $entreprise['name'], $subject, $body);
            }
            if ($acceptSuccess) {
                // Add notification to the user
                $userId = $entreprise['idUser'] ?? null;
                if ($userId) {
                    $notification = [
                        'idUser' => $userId,
                        'type' => 'activation',
                        'title' => "Activation de votre compte entreprise",
                        'message' => "Votre compte entreprise \"" . $entreprise['name'] . "\" a été activé avec succès.",
                        'link' => HOME_URL . "mes_entreprises?action=voir&uiid=" . $entreprise['uiid'],
                        'createdAt' => date('Y-m-d H:i:s')
                    ];
                    $this->notificationRepo->pushNotification($notification);
                }
            }

            $_SESSION['success'] = "La demande d'activation de l'entreprise \"" . $entreprise['name'] . "\" a été acceptée avec succès.";
            $this->redirect('admin/toutes_demandes_dactivation_entreprise');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('admin/toutes_demandes_dactivation_entreprise?error=true');
        }
    }

    public function refuseEntrepriseActivationRequest(): void
    {
        try {
            $message = isset($_POST['message']) ? trim($_POST['message']) : '';
            $uiid = isset($_POST['entreprise_uiid']) ? trim($_POST['entreprise_uiid']) : '';
            
            if (empty($uiid)) {
                throw new Exception("UIID de l'entreprise manquant.");
            }
            
            $entreprise = $this->repo->findEntrepriseByUiid($uiid);
            if (!$entreprise) {
                throw new Exception("Entreprise non trouvée.");
            }
            $refuseSuccess = $this->repo->refuseActivationRequest($uiid);
            if (!$refuseSuccess) {
                throw new Exception("Échec du refus de la demande. Veuillez réessayer.");
            }
            if ($refuseSuccess && !empty($entreprise['email'])) {
                $mail = new Mail();
                $subject = "Refus de l'activation de votre compte entreprise";
                $body = "Nous regrettons de vous informer que votre demande d'activation de compte entreprise a été refusée. Pour plus d'informations, veuillez nous contacter.<br><br>";
                $body .= !empty($message) ? "<br><br>Message de l'administrateur :<br>" . nl2br(htmlspecialchars($message)) : "";
                $mail->sendEmail(ADMIN_EMAIL, ADMIN_SENDER_NAME, $entreprise['email'], $entreprise['name'], $subject, $body);
            }
            if ($refuseSuccess) {
                // Add notification to the user
                $userId = $entreprise['idUser'] ?? null;
                if ($userId) {
                    $notification = [
                        'idUser' => $userId,
                        'type' => 'activation',
                        'title' => "Refus de l'activation de votre compte entreprise",
                        'message' => "Votre demande d'activation de compte entreprise \"" . $entreprise['name'] . "\" a été refusée.",
                        'link' => HOME_URL . "mes_entreprises",
                        'createdAt' => date('Y-m-d H:i:s')
                    ];
                    $this->notificationRepo->pushNotification($notification);
                }
            }
            $_SESSION['success'] = "La demande d'activation de l'entreprise \"" . $entreprise['name'] . "\" a été refusée avec succès.";
            $this->redirect('admin/toutes_demandes_dactivation_entreprise');
        } catch (\Throwable $th) {
            $_SESSION['error'] = $th->getMessage();
            $this->redirect('admin/toutes_demandes_dactivation_entreprise?error=true');
        }
    }
}
