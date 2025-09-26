<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;

class HomeController extends AbstractController
{
    public function displayHomepage(): void
    {
        $this->render('home/accueil');
    }

    public function displayAuth(): void
    {
        $this->render('home/connexion', ['csrf_token' => $this->generateCsrfToken()]);
    }
    public function displayFormInscription(): void
    {
        $this->render('home/inscription', ['csrf_token' => $this->generateCsrfToken()]);
    }
    public function displayFormForgetPassword(): void
    {
        $this->render('home/mdp_oublie', ['csrf_token' => $this->generateCsrfToken()]);
    }
    public function displayFormResetPassword(): void
    {
        $this->render('home/reinit_mon_mot_de_passe', ['csrf_token' => $this->generateCsrfToken()]);
    }
    public function terms_of_service(): void
    {
        $this->render('home/cgu');
    }
    public function mentions_legales(): void
    {
        $this->render('home/mentions_legales');
    }
    public function page403(): void
    {
        header("HTTP/1.1 403 Forbidden");
        header("Content-Type: text/html; charset=utf-8");
        $this->render('home/403');
    }
    public function page404(): void
    {
        header("HTTP/1.1 404 Not Found");
        header("Content-Type: text/html; charset=utf-8");
        $this->render('home/404');
    }
}
