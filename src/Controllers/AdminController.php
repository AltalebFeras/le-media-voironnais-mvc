<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;
use src\Repositories\AdminRepository;
use src\Services\Mail;

class AdminController extends AbstractController
{
    protected $repo;
    public function __construct()
    {
        $this->repo = new AdminRepository();
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

    public function displayUserById(int $id)
    {
        $idUser = $id;
        $errors = [];
        if (!$idUser) {
            $errors[] = "ID utilisateur manquant ou invalide.";
        }
        $user = $this->repo->findById($idUser);
        if (!$user) {
            $errors[] = "Utilisateur non trouvé.";
        }
        $this->returnAllErrors($errors, 'admin/utilisateur_details?id=' . $idUser . '&error=true');

        $this->render('admin/utilisateur_details', ['user' => $user]);
    }

    // Handle POST actions from utilisateur_details (block, unblock, send_email)

    public function blockUser(int $id)
    {

        $idUser = $id;
        $errors = [];

        if (!$idUser) {
            $errors['id'] = "ID utilisateur manquant ou invalide.";
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
    public function unblockUser(int $id)
    {
        $idUser = $id;
        $errors = [];

        if (!$idUser) {
            $errors[] = "ID utilisateur manquant ou invalide.";
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
    public function sendEmailToUser(int $id)
    {
        $idUser = $id;
        $errors = [];
        $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : '';
        $body = isset($_POST['body']) ? htmlspecialchars(trim($_POST['body'])) : '';
        $user = $this->repo->findById($idUser);
        $recipientEmail = trim($user['email'] ?? null);
        $recipientName = trim($user['firstName']) ?? null;

        if (empty($recipientEmail) || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Adresse email invalide.";
        }
        if (!$subject && !$body) {
            $errors['message'] = "Le sujet et le message sont requis.";
        }
        if (!$idUser) {
            $errors['id'] = "ID utilisateur manquant ou invalide.";
        }
        if (strlen($subject) > 255) {
            $errors['subject'] = "Le sujet ne peut pas dépasser 255 caractères.";
        }
        if (strlen($body) > 1000) {
            $errors['body'] = "Le message ne peut pas dépasser 1000 caractères.";
        }

        $this->returnAllErrors($errors, 'admin/utilisateur_details?id=' . $idUser . '&error=true');
        // Send email
        $mail = new Mail();
        $sent = $mail->sendEmail(ADMIN_EMAIL, ADMIN_SENDER_NAME, $recipientEmail, $recipientName, $subject, $body);
        if ($sent) {
            $_SESSION['success'] = "L'email a été envoyé avec succès.";
            $this->redirect('admin/utilisateur_details?id=' . $idUser . '&success=email_sent');
        } else {
            $errors['global'] = "Erreur lors de l'envoi de l'email.";
            $this->returnAllErrors($errors, 'admin/utilisateur_details?id=' . $idUser . '&error=true');
        }
    }
}
