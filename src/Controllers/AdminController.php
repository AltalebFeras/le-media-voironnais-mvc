<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;
use src\Repositories\AdminRepository;

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
        $idUser = (int)$_GET['id'] ? htmlspecialchars($_GET['id']) : null;
        $errors = [];
        if (!$idUser) {
            $errors[] = "ID utilisateur manquant ou invalide.";
        }
        $user = $this->repo->findById($idUser);
        if (!$user) {
            $errors[] = "Utilisateur non trouvé.";
        }
        $this->returnAllErrors($errors, 'admin/utilisateur_details?id=' . $idUser . '&error=true');

        // var_dump($user);
        // die;
        $this->render('admin/utilisateur_details', ['user' => $user]);
    }
}
