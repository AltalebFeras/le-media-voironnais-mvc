<?php

namespace src\Controllers;

use DateTime;
use Exception;
use src\Abstracts\AbstractController;
use src\Models\User;
use src\Repositories\UserRepository;
use src\Services\Encrypt_decrypt;
use src\Services\Mail;

class AdminController extends AbstractController
{
    protected $repo;
    public function __construct()
    {
        $this->repo = new UserRepository();
    }
    public function displayAdminDashboard()
    {
        $this->render('admin/dashboard_admin');
    }
    public function displayAllUsers()
    {
        $allUsers = $this->repo->findAllUsers();
        $this->render('admin/all_users', ['allUsers' => $allUsers]);
    }
}
    