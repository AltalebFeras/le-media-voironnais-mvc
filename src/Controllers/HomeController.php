<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;

class HomeController extends AbstractController
{
    public function displayHomepage(): void
    {
        $this->render('home/accueil');
    }

    public function displayFormConnexion()
    {
        if (!isset($_GET['error'])) {
            unset($_SESSION['form_data']);
        }
        $this->render('home/connexion');
    }
    public function displayFormInscription()
    {
        if (!isset($_GET['error'])) {
            unset($_SESSION['form_data']);
        }
        $this->render('home/inscription');
    }
    public function displayFormForgetPassword()
    {
        $this->render('home/mdp_oublie');
    }
    public function displayFormResetPassword()
    {
        $this->render('home/reinit_mon_mot_de_passe');
    }
    public function terms_of_service()
    {
        $this->render('home/cgu');
    }
    public function mentions_legales()
    {
        $this->render('home/mentions_legales');
    }
    public function display403()
    {
        $this->render('home/403');
    }
    public function deconnexion(): void
    {
        session_destroy();
        session_start();
        $_SESSION['success'] = 'vous êtes désconnecté avec succès!';
        $this->redirect('connexion');
    }

    public function page404(): void
    {
        header("HTTP/1.1 404 Not Found");
        header("Content-Type: text/html; charset=utf-8");
        $this->render('home/404');
    }
    public function page403(): void
    {
        header("HTTP/1.1 403 Forbidden");
        header("Content-Type: text/html; charset=utf-8");
        $this->render('home/403');
    }
}
