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
    public function displayContactForm(): void
    {
        $this->render('home/contact', ['csrf_token' => $this->generateCsrfToken()]);
    }

    public function submitContactForm(): void
    {
        $this->validateCsrfToken('contact');
        $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : null;
        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : null;
        $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : null;
        $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : null;
        $type = isset($_POST['type']) ? htmlspecialchars(trim($_POST['type'])) : 'info';

        $errors = [];
        if (!$name || !$email || !$title || !$message) {
            $errors['fields'] = 'Tous les champs sont obligatoires.';
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Adresse email invalide.';
        }
        if (!empty($errors)) {
            $_SESSION['form_data'] = $_POST;
            $_SESSION['error'] = $errors;
            $this->redirect('contact?error=true');
        }

        $repo = new \src\Repositories\EvenementRepository();
        $repo->saveContactMessage([
            'idUser' => $_SESSION['idUser'] ?? null,
            'name' => $name,
            'email' => $email,
            'title' => $title,
            'message' => $message,
            'type' => $type
        ]);
        $_SESSION['success'] = 'Votre message a été envoyé avec succès.';
        $this->redirect('contact');
    }
}
