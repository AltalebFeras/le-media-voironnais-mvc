<?php

namespace src\Controllers;

use DateTime;
use Exception;
use src\Abstracts\AbstractController;
use src\Models\User;
use src\Repositories\UserRepository;
use src\Repositories\FriendRepository;
use src\Services\Encrypt_decrypt;
use src\Services\Mail;
use src\Services\Helper;

class UserController extends AbstractController
{
    protected $repo;
    private $friendRepo;
    
    public function __construct()
    {
        $this->repo = new UserRepository();
        $this->friendRepo = new FriendRepository();
    }
    /**
     * Generate a unique fingerprint for the machine connected to the server.
     * This fingerprint is based on various server variables that are unique to the machine.
     * This helps in identifying the machine and can be used for security purposes.
     * The fingerprint is stored in the session for later use.
     * @return string
     */
    private function makeFingerprint(): string
    {
        $machineConnectedServerData = [
            // 'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? '',
            // 'HTTP_ACCEPT_LANGUAGE' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            // 'HTTP_ACCEPT_ENCODING' => $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
            // 'HTTP_ACCEPT' => $_SERVER['HTTP_ACCEPT'] ?? '',
        ];
        $fingerprint = hash('sha256', json_encode($machineConnectedServerData));
        return $_SESSION['machine_fingerprint'] = $fingerprint;
    }

    /**
     * Send an activation email using PHPMailer and template to the user.
     * @param mixed $firstName
     * @param mixed $lastName
     * @param mixed $email
     * @param mixed $activationToken
     * @return bool
     */

    public function treatmentInscription(): void
    {
        // Validate CSRF token first
        $this->validateCsrfToken('inscription');

        $firstName = isset($_POST['firstName']) ? htmlentities(trim(ucfirst($_POST['firstName']))) : null;
        $lastName = isset($_POST['lastName']) ? htmlentities(trim(ucfirst($_POST['lastName']))) : null;
        $email = isset($_POST['email']) ? htmlentities(trim(strtolower($_POST['email']))) : null;
        $password = isset($_POST['password']) ? trim($_POST['password']) : null;
        $passwordConfirmation = isset($_POST['passwordConfirmation']) ? trim($_POST['passwordConfirmation']) : null;
        $rgpd = isset($_POST['rgpd']) ? htmlentities(trim($_POST['rgpd'])) : null;

        $errors = [];
        $_SESSION['form_data'] = $_POST;

        if ($password !== $passwordConfirmation) {
            $errors['password_not_match'] = 'Les mots de passe ne correspondent pas';
        }

        $activationToken = bin2hex(random_bytes(16));
        $roleId = 3; // Default role FOR 'user'
        $createdAt = date('Y-m-d H:i:s');

        if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($passwordConfirmation) || !$rgpd) {
            $errors['global'] = 'Veuillez remplir tous les champs';
        }

        if (strlen($firstName) < 3 || strlen($lastName) < 3) {
            $errors['fullName'] = 'Le prénom et le nom doivent contenir au moins 3 caractères';
        }
        if (!preg_match('/^[A-Za-zÀ-ÿ\s\-]+$/', $firstName)) {
            $errors['firstName'] = 'Le prénom ne doit contenir que des lettres';
        }
        if (strlen($password) < 8) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email_invalid'] = 'L\'adresse e-mail est invalide';
        }
        if ($rgpd !== 'on') {
            $errors['rgpd'] = 'Veuillez accepter les conditions d\'utilisation et la politique de confidentialité.';
        } else {
            $rgpdDate = date('Y-m-d H:i:s');
        }
        // // Verify reCAPTCHA
        // if (IS_PROD === TRUE) {
        //     // Verify reCAPTCHA
        //     $recaptchaStatus = $this->checkReCaptcha();
        //     if ($recaptchaStatus === false) {
        //         $errors['recaptcha'] = 'Veuillez vérifier que vous n\'êtes pas un robot.';
        //     }
        // }

        $emailExists = $this->repo->getUser($email);

        if ($emailExists) {
            $errors['email_exists'] = 'Cette adresse e-mail est déjà utilisée';
        }
        $accountExistsAndDeleted = $this->repo->getDeletedUserByEmail($email);
        if ($accountExistsAndDeleted) {
            $errors['email_exists'] = 'Un compte avec cette adresse e-mail a été supprimé. Veuillez contacter le support pour plus d\'informations.';
        }
        // If there are any validation errors, throw one Error with all errors
        $this->returnAllErrors($errors, 'inscription', ['error' => 'true']);
        // Use SEL as a pepper: append the secret to the plaintext before hashing
        $passwordHash = password_hash($password . SEL, PASSWORD_DEFAULT);

        $user = new User();
        // Generate UIID for new user
        $helper = new Helper();
        $uiid = $helper->generateUiid();
        $slug = $helper->generateSlug($firstName, $lastName);
        $existSlug = $this->repo->isSlugExists($slug);
        if ($existSlug) {

            $suffix = random_int(1, 999999);
            $finalSlug = "{$slug}-{$suffix}";
            while ($this->repo->isSlugExists($finalSlug)) {
                $suffix++;
                $finalSlug = "{$slug}-{$suffix}";
            }
            $slug = $finalSlug;
        }

        $user->setUiid($uiid);
        $user->setSlug($slug);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setPassword($passwordHash);
        $user->setIsActivated(false);
        $user->setIsBanned(false);
        $user->setIsDeleted(false);
        $user->setToken($activationToken);
        $user->setCreatedAt($createdAt);
        $user->setIdRole($roleId);
        $user->setRgpdAcceptedDate($rgpdDate);


        $mail = new Mail();
        $activationLink = BASE_URL . HOME_URL . 'activer_mon_compte';
        $subject = 'Activation de votre compte';
        $body = "Veuillez cliquer sur le bouton ci-dessous pour activer votre compte :<br>";
        $body .= "<a href='$activationLink?token=$activationToken' style='display: inline-block; padding: 10px 20px; font-size: 16px; color: #ffffff; background-color: #007BFF; text-decoration: none; border-radius: 5px;'>Activer mon compte</a><br><br>";
        $body .= "Si le bouton ne fonctionne pas, copiez et collez le lien ci-dessous dans votre navigateur :<br>";
        $body .= "$activationLink?token=$activationToken<br><br>";
        $body .= "Si vous n'avez pas créé de compte, veuillez ignorer cet e-mail.<br>";

        $sendEmail = $mail->sendEmail(ADMIN_EMAIL, ADMIN_SENDER_NAME, $email, $firstName, $subject, $body);
        if ($sendEmail) {
            $signUp = $this->repo->signUp($user);
        } else {
            $errors['sendEmail'] = 'Une erreur s\'est produite lors de l\'envoi de l\'e-mail d\'activation. Veuillez réessayer plus tard.';
            $this->returnAllErrors($errors, 'inscription', ['error' => 'true']);
        }
        if ($signUp) {
            $_SESSION['success'] = 'Un e-mail d\'activation a été envoyé à votre adresse e-mail. Veuillez vérifier votre boîte de réception et cliquer sur le lien d\'activation pour activer votre compte.';
            $this->redirect('connexion');
        } else {
            $errors['signUp'] = 'Une erreur s\'est produite lors de l\'inscription. Veuillez réessayer plus tard.';
            $this->returnAllErrors($errors, 'inscription', ['error' => 'true']);
        }
    }


    public function activateAccount(): void
    {
        try {
            $token = isset($_GET['token']) ? htmlspecialchars(trim($_GET['token'])) : null;

            if (!$token || !preg_match('/^[a-f0-9]{32}$/', $token)) {
                throw new Exception('Le lien d\'activation est invalide ou votre compte a déjà été activé.');
            }

            if (strlen($token) !== 32 || !ctype_xdigit($token)) {
                throw new Exception('Le lien d\'activation est invalide ou a expiré.');
            }
            $user = $this->repo->getUserByToken($token);

            if ($user) {
                $idUser = $user->getIdUser();
                $activateAccount = $this->repo->activateUser($idUser);
                if ($activateAccount) {
                    $_SESSION['success'] = 'Votre compte a été activé avec succès! Vous pouvez maintenant vous connecter.';
                    $this->redirect('connexion');
                }
            } else {
                throw new Exception('Le lien d\'activation est invalide ou votre compte a déjà été activé.');
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('connexion', ['error' => 'true']);
        }
    }

    public function treatmentConnexion(): void
    {
        // Validate CSRF token first
        $this->validateCsrfToken('connexion');

        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : null;
        $password = isset($_POST['password']) ? htmlspecialchars(trim($_POST['password'])) : null;
        $_SESSION['form_data'] = $_POST;
        $errors = [];
        if (empty($email) || empty($password)) {
            $errors['fields'] = 'Veuillez remplir tous les champs';
        }

        $user = $this->repo->getUser($email);

        if (!$user) {
            $errors['email'] = 'L\'adresse e-mail est invalide ou le mot de passe est incorrect.';
        }
        // verify using the same pepper (SEL) that was used when hashing
        if ($user && !password_verify($password . SEL, $user->getPassword())) {
            $errors['password'] = 'L\'adresse e-mail est invalide ou le mot de passe est incorrect.';
        }

        if ($user && $user->getIsActivated() === false) {
            $errors['isActivated'] = 'Votre compte n\'est pas encore activé. Veuillez vérifier votre boîte de réception pour l\'e-mail d\'activation.';
        }
        if ($user && $user->getIsBanned() === true) {
            $errors['isBanned'] = 'Votre compte a été banni. Veuillez contacter le support pour plus d\'informations.';
        }
        // if (IS_PROD === TRUE) {
        //     // Verify reCAPTCHA
        //     $recaptchaStatus = $this->checkReCaptcha();
        //     if ($recaptchaStatus === false) {
        //         $errors['recaptcha'] = 'Veuillez vérifier que vous n\'êtes pas un robot.';
        //     }
        // }
        if ($user && $user->getIsDeleted() === true) {
            $errors['isDeleted'] = 'Votre compte a été supprimé, Veuillez contacter le support.';
        }
        $this->returnAllErrors($errors, 'connexion', ['error' => 'true']);

        $lastSeen = (new DateTime())->format('Y-m-d H:i:s');
        $idUser = $user->getIdUser();

        $this->repo->updateLastSeenAndSetUserOnline($idUser, $lastSeen);

        if ($user) {
            //   datetime string
            $_SESSION['idUser'] = $idUser;
            $_SESSION['userUiid'] = $user->getUiid(); // Store UIID instead of exposing ID
            $_SESSION['firstName'] = $user->getFirstName();
            $_SESSION['lastName'] = $user->getLastName();
            $_SESSION['email'] = $user->getEmail();
            $_SESSION['phone'] = $user->getPhone();
            $_SESSION['avatarPath'] = $user->getAvatarPath() ?? BASE_URL . HOME_URL . 'assets/images/uploads/avatars/default_avatar.png';
            $_SESSION['bannerPath'] = $user->getBannerPath() ?? BASE_URL . HOME_URL . 'assets/images/uploads/banners/default_banner.jpg';
            $_SESSION['bio'] = $user->getBio();
            $_SESSION['dateOfBirth'] = $user->getDateOfBirthFormatted();
            $_SESSION['isActivated'] = $user->getIsActivated();
            $_SESSION['isOnline'] = true;
            $_SESSION['lastSeen'] = $lastSeen;
            $_SESSION['createdAt'] = $user->getCreatedAtFormatted();
            $_SESSION['updatedAt'] = $user->getUpdatedAtFormatted();

            // Prevent session fixation  
            session_regenerate_id(true);
            $this->makeFingerprint();

            $_SESSION['role'] = $user->getRoleName();
            if ($_SESSION['role'] === 'admin') {
                $_SESSION['connectedAdmin'] = true;
                $_SESSION['success'] = 'Vous êtes connecté en tant qu\'administrateur!';
                $this->redirect('admin/dashboard_admin');
                exit();
            } elseif ($_SESSION['role'] === 'super_admin') {
                $_SESSION['connectedSuperAdmin'] = true;
                $_SESSION['success'] = 'Vous êtes connecté en tant que super administrateur!';
                $this->redirect('admin/dashboard_super_admin');
            } elseif ($_SESSION['role'] === 'user') {
                if (isset($_SESSION['redirect_after_login'])) {
                    $_SESSION['connected'] = true;
                    $_SESSION['success'] = 'Vous êtes connecté avec succès!';
                    header('Location: ' . $_SESSION['redirect_after_login']);
                    unset($_SESSION['redirect_after_login']);
                    exit();
                }
                $_SESSION['connected'] = true;
                $_SESSION['success'] = 'Vous êtes connecté avec succès!';
                $this->redirect('dashboard');
            } else {
                $_SESSION['connected'] = true;
                $_SESSION['success'] = 'Vous êtes connecté avec succès!';
                $this->redirect('dashboard');
            }
        } else {
            $errors['unknown'] = 'Une erreur inconnue est survenue. Veuillez réessayer plus tard.';
            $this->returnAllErrors($errors, 'connexion', ['error' => 'true']);
        }
    }
    public function deconnexion(): void
    {
        if (isset($_SESSION['newEmail'])) {
            $idUser = $_SESSION['idUser'];
            $this->repo->clearAuthCode($idUser);
        }
        if (isset($_SESSION['isOnline'])) {
            $this->repo->makeUserOffline($_SESSION['idUser']);
        }
        session_destroy();
        session_start();

        $_SESSION['success'] = 'vous êtes désconnecté avec succès!';
        $this->redirect('connexion');
    }
    public function displayDashboard()
    {
        $this->render('user/dashboard');
    }

    public function treatmentForgotMyPassword(): void
    {
        // Validate CSRF token
        $this->validateCsrfToken('mdp_oublie');

        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : null;
        $_SESSION['form_data'] = $_POST;
        $errors = [];
        if (empty($email)) {
            $errors['field'] = 'Veuillez entrer votre adresse e-mail!';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'adresse e-mail est invalide!';
        }
        // // Verify reCAPTCHA
        // if (IS_PROD === TRUE) {
        //     // Verify reCAPTCHA
        //     $recaptchaStatus = $this->checkReCaptcha();
        //     if ($recaptchaStatus === false) {
        //         $errors['recaptcha'] = 'Veuillez vérifier que vous n\'êtes pas un robot.';
        //     }
        // }
        $user = $this->repo->getUser($email);
        if ($user && $user->getIsActivated() === false) {
            $errors['isActivated'] = 'Vous ne pouvez pas réinitialiser votre mot de passe avant d\'avoir activé votre compte.';
        }

        if ($user && $user->getIsDeleted() === true) {
            $errors['isDeleted'] = 'Votre compte a été supprimé, Veuillez contacter le support.';
        }
        // verify the last time of the reset password at least 15 minutes ago 
        if ($user) {
            $lastReset = $user->getPasswordResetAt();
            if ($lastReset) {
                $lastResetDateTime = new DateTime($lastReset);
                $now = new DateTime();
                $interval = $now->getTimestamp() - $lastResetDateTime->getTimestamp();
                if ($interval < 3600) { // 3600 seconds = 1 hour
                    $errors['tooManyRequests'] = 'Une demande de réinitialisation du mot de passe a déjà été effectuée récemment. Veuillez réessayer plus tard.';
                }
            }
        }

        $this->returnAllErrors($errors, 'mdp_oublie', ['error' => 'true']);

        if ($user) {
            $idUser = $user->getIdUser();

            $token = bin2hex(random_bytes(16));

            // send password reset email (same as before)
            $resetLink = BASE_URL . HOME_URL . "reinit_mon_mot_de_passe?token=$token";
            $mail = new Mail();
            $subject = 'Réinitialisation de votre mot de passe';
            $body = "Veuillez cliquer sur le lien ci-dessous pour réinitialiser votre mot de passe:<br>";
            $body .= "<br><br>";
            $body .= "<a href='$resetLink'>Réinitialiser le mot de passe</a><br><br>";

            $sendEmail = $mail->sendEmail(ADMIN_EMAIL, ADMIN_SENDER_NAME, $user->getEmail(), $user->getFirstName(), $subject, $body);

            $_SESSION['success'] = 'Un e-mail de réinitialisation du mot de passe a été envoyé à votre adresse e-mail. Veuillez vérifier votre boîte de réception et cliquer sur le lien pour réinitialiser votre mot de passe.';
            if ($sendEmail) {
                $this->repo->saveTokenAndUpdatePasswordResetAt($idUser, $token, (new DateTime())->format('Y-m-d H:i:s'));
                $this->redirect('connexion');
            } else {
                $errors['sendEmail'] = 'Une erreur s\'est produite lors de l\'envoi de l\'e-mail de réinitialisation. Veuillez réessayer plus tard.';
                $this->returnAllErrors($errors, 'mdp_oublie', ['error' => 'true']);
            }
        } else {
            $errors['email'] = 'Aucun compte trouvé avec cette adresse e-mail.';
            $this->returnAllErrors($errors, 'mdp_oublie?error=true');
        }
    }

    public function treatmentResetPassword(): void
    {
        // Validate CSRF token
        $this->validateCsrfToken('reinit_mon_mot_de_passe');

        $token = isset($_POST['token']) ? htmlspecialchars($_POST['token']) : null;
        $newPassword = isset($_POST['newPassword']) ? htmlspecialchars($_POST['newPassword']) : null;
        $confirmPassword = isset($_POST['confirmPassword']) ? htmlspecialchars($_POST['confirmPassword']) : null;
        $errors = [];
        $_SESSION['form_data'] = $_POST;

        if ($newPassword !== $confirmPassword) {
            $errors['password'] = 'Les mots de passe ne correspondent pas.';
        }
        if (strlen($newPassword) < 8) {
            $errors['length'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if (empty($token) || !preg_match('/^[a-f0-9]{32}$/', $token)) {
            $errors['token'] = 'Le lien de réinitialisation du mot de passe est invalide ou a expiré.';
        }
        $user = $this->repo->getUserByToken($token);

        if (!$user) {
            $errors['token'] = 'Le lien de réinitialisation du mot de passe est invalide ou a expiré.';
        }

        // // Verify reCAPTCHA
        // if (IS_PROD === TRUE) {
        //     // Verify reCAPTCHA
        //     $recaptchaStatus = $this->checkReCaptcha();
        //     if ($recaptchaStatus === false) {
        //         $errors['recaptcha'] = 'Veuillez vérifier que vous n\'êtes pas un robot.';
        //     }
        // }

        $this->returnAllErrors($errors, 'reinit_mon_mot_de_passe', ['token' => $token, 'error' => 'true']);

        // Hash new password with pepper
        $passwordHash = password_hash($newPassword . SEL, PASSWORD_DEFAULT);
        $idUser = $user->getIdUser();
        $resetPassword = $this->repo->resetPassword($idUser, $passwordHash);
        if ($resetPassword) {
            $_SESSION['success'] = 'Votre mot de passe a été réinitialisé avec succès! Vous pouvez maintenant vous connecter.';
            $this->redirect('connexion');
        } else {
            $_SESSION['error'] = 'Une erreur s\'est produite lors de la réinitialisation du mot de passe. Veuillez réessayer plus tard.';
            $this->redirect('reinit_mon_mot_de_passe', ['token' => $token, 'error' => 'true']);
        }
    }
    public function displayMyAccount(): void
    {
        $this->render('user/mon_compte');
    }
    public function editProfile(): void
    {
        try {
            $firstName = isset($_POST['firstName']) ? htmlspecialchars(trim(ucfirst($_POST['firstName']))) : null;
            $lastName = isset($_POST['lastName']) ? htmlspecialchars(trim(ucfirst($_POST['lastName']))) : null;
            $updatedAt = new DateTime();
            $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : null;
            $bio = isset($_POST['bio']) ? htmlspecialchars(trim($_POST['bio'])) : null;
            $dateOfBirth = isset($_POST['dateOfBirth']) ? htmlspecialchars(trim($_POST['dateOfBirth'])) : null;
            $idUser = $_SESSION['idUser'];
            $errors = [];
            $_SESSION['form_data'] =  $_POST;

            if (empty($firstName) || empty($lastName)) {
                $errors['fields'] = 'Veuillez remplir tous les champs';
            }
            if (strlen($firstName) < 3 || strlen($lastName) < 3) {
                $errors['length'] = 'Le prénom et le nom doivent contenir au moins 2 caractères';
            }
            if (!preg_match('/^[A-Za-zÀ-ÿ\s\-]+$/', $firstName)) {
                $errors['firstName'] = 'Le prénom ne doit contenir que des lettres';
            }
            if (!preg_match('/^[A-Za-zÀ-ÿ\s\-]+$/', $lastName)) {
                $errors['lastName'] = 'Le nom ne doit contenir que des lettres';
            }

            if ($phone) {
                if (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
                    $errors['phone'] = 'Le numéro de téléphone est invalide. Il doit contenir entre 7 et 15 chiffres et peut commencer par un +.';
                }
            }
            //  validate dateOfBirth if provided and not in the future and less than 13 years from now

            if ($dateOfBirth) {
                $dob = DateTime::createFromFormat('Y-m-d', $dateOfBirth);
                $now = new DateTime();
                $minDate = (new DateTime())->modify('-120 years'); // Minimum age 120 years
                $maxDate = (new DateTime())->modify('-16 years'); // Minimum age 16 years
                if (!$dob || $dob > $now || $dob < $minDate || $dob > $maxDate) {
                    $errors['dateOfBirth'] = 'La date de naissance est invalide. Vous devez avoir au moins 16 ans.';
                }
            }


            $user = $this->repo->getUserById($_SESSION['idUser']);


            $this->returnAllErrors($errors, 'mon_compte', ['action' => 'edit_profile', 'error' => 'true']);


            $user = new User();
            $user->setIdUser($idUser);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setUpdatedAt($updatedAt->format('Y-m-d H:i:s'));
            $user->setPhone($phone);
            $user->setBio($bio);
            $user->setDateOfBirth($dateOfBirth);

            $updateUser = $this->repo->updateUser($user);

            if ($updateUser) {
                $_SESSION['firstName'] = $user->getFirstName();
                $_SESSION['lastName'] = $user->getLastName();
                $_SESSION['phone'] = $user->getPhone();
                $_SESSION['bio'] = $user->getBio();
                $_SESSION['dateOfBirth'] = $user->getDateOfBirthFormatted();
                $_SESSION['updatedAt'] = $user->getUpdatedAtFormatted();

                $_SESSION['success'] = 'Votre profil a été mis à jour avec succès!';
                $this->redirect('mon_compte');
            } else {
                throw new Exception("Aucune modification n’a été effectuée.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mon_compte', ['action' => 'edit_profile', 'error' => 'true']);
            exit();
        }
    }
    public function editEmail(): void
    {
        $idUser = $_SESSION['idUser'];
        $email = isset($_POST['email']) ? htmlspecialchars(trim(strtolower($_POST['email']))) : null;
        $_SESSION['form_data'] =  $_POST;
        $errors = [];

        if (!$email) {
            $errors['empty'] = 'Veuillez entrer une adresse e-mail.';
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'adresse e-mail est invalide.';
        }

        $user = $this->repo->getUserById($idUser);

        // Check if the new email is already used by another user
        $existingUser = $this->repo->getUser($email);
        if ($existingUser && $existingUser->getIdUser() != $idUser) {
            $errors['emailExist'] = 'Cette adresse e-mail est déjà utilisée';
        }

        if ($user && $user->getEmail() === $email) {
            $errors['sameEmail'] = 'Veuillez entrer une nouvelle adresse e-mail différente de l\'ancienne.';
        }
        // // verify reCAPTCHA
        // if (IS_PROD === TRUE) {
        //     $recaptchaStatus = $this->checkReCaptcha();
        //     if ($recaptchaStatus === false) {
        //         $errors['recaptcha'] = 'Veuillez vérifier que vous n\'êtes pas un robot.';
        //     }
        // }
        // verify the last time of the email change at least 30 days ago
        if ($user) {
            $lastEmailChange = $user->getEmailChangedAt();
            if ($lastEmailChange) {
                $lastEmailChangeDateTime = new DateTime($lastEmailChange);
                $now = new DateTime();
                $interval = $now->getTimestamp() - $lastEmailChangeDateTime->getTimestamp();
                if ($interval < 2592000) { // 2592000 seconds = 30 days
                    $errors['tooManyRequests'] = 'Une demande de changement d\'adresse e-mail a déjà été effectuée récemment. Veuillez réessayer plus tard.';
                }
            }
        }
        $this->returnAllErrors($errors, 'mon_compte', ['action' => 'edit_profile', 'field' => 'email', 'error' => 'true']);

        // send conformation email with code of six numbers to new email address
        $authCode = rand(100000, 999999);
        while ($this->repo->getUserByAuthCode($authCode)) {
            $authCode = rand(100000, 999999);
        }
        $authCodeSave = $this->repo->saveAuthCodeAndUpdateEmailChangeAt($idUser, $authCode, (new DateTime())->format('Y-m-d H:i:s'));
        if ($authCodeSave) {
            $mail = new Mail();
            $subject = 'Confirmation de votre nouvelle adresse e-mail';
            $body = "Veuillez utiliser le code ci-dessous pour confirmer votre nouvelle adresse e-mail:<br>";
            $body .= "<h2>$authCode</h2><br><br>";
            $mail->sendEmail(ADMIN_EMAIL, ADMIN_SENDER_NAME, $_SESSION['email'], $_SESSION['firstName'], $subject, $body);
            $_SESSION['newEmail'] = $email;
        } else {
            $errors['saveCode'] = 'Une erreur s\'est produite lors de la génération du code de confirmation. Veuillez réessayer plus tard.';
            $this->returnAllErrors($errors, 'mon_compte', ['action' => 'edit_profile', 'field' => 'email', 'error' => 'true']);
        }
        $_SESSION['success'] = 'Un code de confirmation a été envoyé à votre nouvelle adresse e-mail. Veuillez vérifier votre boîte de réception et entrer le code pour confirmer votre nouvelle adresse e-mail.';
        $this->redirect('mon_compte', ['action' => 'confirm_new_email']);
    }

    public function validateNewEmail(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $authCode = isset($_POST['authCode']) ? htmlspecialchars(trim($_POST['authCode'])) : null;
            $_SESSION['form_data'] = $_POST;
            $errors = [];

            if (!$authCode) {
                $errors['empty'] = 'Veuillez entrer le code de confirmation.';
            }
            if ($authCode && !preg_match('/^[0-9]{6}$/', $authCode)) {
                $errors['code'] = 'Le code de confirmation est invalide.';
            }
            $this->returnAllErrors($errors, 'mon_compte', ['action' => 'confirm_new_email', 'error' => 'true']);

            $user = $this->repo->getUserById($idUser);
            if (!$user) {
                throw new Exception('Utilisateur introuvable.');
            }

            $savedCode = $user->getAuthCode();
            if ($savedCode && $savedCode == $authCode) {
                $newEmail = $_SESSION['newEmail'] ?? null;
                if (!$newEmail) {
                    throw new Exception('Nouvelle adresse e-mail manquante.');
                }
                $updateEmail = $this->repo->updateUserEmail($idUser, $newEmail);
                if ($updateEmail) {
                    unset($_SESSION['newEmail']);
                    $this->repo->clearAuthCode($idUser);
                    $_SESSION['email'] = $newEmail;
                    $_SESSION['success'] = 'Votre adresse e-mail a été mise à jour avec succès!';
                    $this->redirect('mon_compte');
                } else {
                    throw new Exception('Une erreur s\'est produite lors de la mise à jour de votre adresse e-mail. Veuillez réessayer plus tard.');
                }
            } else {
                throw new Exception('Le code de confirmation est incorrect.');
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mon_compte', ['action' => 'confirm_new_email', 'error' => 'true']);
        }
    }

    public function cancelEmailChange(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $this->repo->clearAuthCode($idUser);
            if (isset($_SESSION['newEmail'])) {
                unset($_SESSION['newEmail']);
            }
            $_SESSION['success'] = 'Le changement d\'adresse e-mail a été annulé avec succès.';
            $this->redirect('mon_compte');
        } catch (Exception $e) {
            $_SESSION['error'] = 'Une erreur s\'est produite lors de l\'annulation du changement d\'adresse e-mail.';
            $this->redirect('mon_compte', ['action' => 'confirm_new_email', 'error' => 'true']);
        }
    }

    public function addPhone(): void
    {
        $idUser = $_SESSION['idUser'];
        $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : null;
        $_SESSION['form_data'] =  $_POST;
        $errors = [];

        if (!$phone) {
            $errors['empty'] = 'Veuillez entrer un numéro de téléphone.';
        }
        if ($phone) {
            if (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
                $errors['phone'] = 'Le numéro de téléphone est invalide. Il doit contenir entre 7 et 15 chiffres et peut commencer par un +.';
            }
        }
        // If there are any validation errors, throw one Error with all errors
        $this->returnAllErrors($errors, 'mon_compte', ['action' => 'edit_profile', 'field' => 'phone', 'error' => 'true']);


        $user = $this->repo->getUserById($idUser);
        $user->setPhone($phone);
        $addPhone = $this->repo->updateUserPhone($user);
        if ($addPhone) {
            $_SESSION['phone'] = $user->getPhone();
            $_SESSION['success'] = 'Votre numéro de téléphone a été ajouté avec succès.';
            $this->redirect('mon_compte');
        } else {
            $_SESSION['error'] = 'Une erreur s\'est produite lors de l\'ajout de votre numéro de téléphone. Veuillez réessayer plus tard.';
            $this->redirect('mon_compte', ['action' => 'edit_profile', 'field' => 'phone', 'error' => 'true']);
        }
    }
    public function addBio(): void
    {
        $idUser = $_SESSION['idUser'];
        $bio = isset($_POST['bio']) ? htmlspecialchars(trim($_POST['bio'])) : null;
        $_SESSION['form_data'] =  $_POST;
        $errors = [];

        if (!$bio) {
            $errors['empty'] = 'Veuillez entrer une biographie.';
        }
        if (strlen($bio) > 500) {
            $errors['length'] = 'La biographie ne doit pas dépasser 500 caractères.';
        }
        // If there are any validation errors, throw one Error with all errors

        $this->returnAllErrors($errors, 'mon_compte', ['action' => 'edit_profile', 'field' => 'bio', 'error' => 'true']);

        $user = $this->repo->getUserById($idUser);
        $user->setBio($bio);
        $addBio = $this->repo->updateUserBio($user);
        if ($addBio) {
            $_SESSION['bio'] = $user->getBio();
            $_SESSION['success'] = 'Votre biographie a été ajoutée avec succès.';
            $this->redirect('mon_compte');
        } else {
            $_SESSION['error'] = 'Une erreur s\'est produite lors de l\'ajout de votre biographie. Veuillez réessayer plus tard.';
            $this->redirect('mon_compte', ['action' => 'edit_profile', 'field' => 'bio', 'error' => 'true']);
        }
    }
    public function addDateOfBirth(): void
    {
        $idUser = $_SESSION['idUser'];
        $dateOfBirth = isset($_POST['dateOfBirth']) ? htmlspecialchars(trim($_POST['dateOfBirth'])) : null;
        $_SESSION['form_data'] =  $_POST;
        $errors = [];

        if (!$dateOfBirth) {
            $errors['empty'] = 'Veuillez entrer une date de naissance.';
        }
        if ($dateOfBirth) {
            $dob = DateTime::createFromFormat('Y-m-d', $dateOfBirth);
            $now = new DateTime();
            $minDate = (new DateTime())->modify('-120 years'); // Minimum age 120 years
            $maxDate = (new DateTime())->modify('-16 years'); // Minimum age 16 years
            if (!$dob || $dob > $now || $dob < $minDate || $dob > $maxDate) {
                $errors['dateOfBirth'] = 'La date de naissance est invalide. Vous devez avoir au moins 16 ans.';
            }
        }
        // If there are any validation errors, throw one Error with all errors

        $this->returnAllErrors($errors, 'mon_compte', ['action' => 'edit_profile', 'field' => 'date_of_birth', 'error' => 'true']);

        $user = $this->repo->getUserById($idUser);
        $user->setDateOfBirth($dateOfBirth);
        $addDateOfBirth = $this->repo->updateUserDateOfBirth($user);
        if ($addDateOfBirth) {
            $_SESSION['dateOfBirth'] = $user->getDateOfBirthFormatted();
            $_SESSION['success'] = 'Votre date de naissance a été ajoutée avec succès.';
            $this->redirect('mon_compte');
        } else {
            $_SESSION['error'] = 'Une erreur s\'est produite lors de l\'ajout de votre date de naissance. Veuillez réessayer plus tard.';
            $this->redirect('mon_compte', ['action' => 'edit_profile', 'field' => 'date_of_birth', 'error' => 'true']);
        }
    }

    public function changePassword(): void
    {
        $idUser = $_SESSION['idUser'];
        $currentPassword = isset($_POST['currentPassword']) ? htmlspecialchars($_POST['currentPassword']) : null;
        $newPassword = isset($_POST['newPassword']) ? htmlspecialchars($_POST['newPassword']) : null;
        $confirmPassword = isset($_POST['confirmPassword']) ? htmlspecialchars($_POST['confirmPassword']) : null;
        $errors = [];
        $_SESSION['form_data'] = $_POST;
        // Vérifie le mot de passe actuel
        $user = $this->repo->getUserById($idUser);
        // verify current password using pepper
        if (!password_verify($currentPassword . SEL, $user->getPassword())) {
            $errors['currentPassword'] = 'Le mot de passe actuel est incorrect.';
        }

        // Vérifie si le nouveau mot de passe correspond à la confirmation
        if ($newPassword !== $confirmPassword) {
            $errors['confirmPassword'] = 'Les nouveaux mots de passe ne correspondent pas.';
        }
        if (strlen($newPassword) < 8) {
            $errors['length'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        // If there are any validation errors, throw one Error with all errors
        $this->returnAllErrors($errors, 'mon_compte', ['action' => 'change_password', 'error' => 'true']);
        // Hash du nouveau mot de passe
        $newPasswordHash = password_hash($newPassword . SEL, PASSWORD_DEFAULT);
        $changePassword = $this->repo->updatePassword($idUser, $newPasswordHash);
        if ($changePassword) {
            $_SESSION['success'] = 'Votre mot de passe a été changé avec succès !';
            $this->redirect('mon_compte');
        } else {
            $_SESSION['error'] = 'Une erreur s\'est produite lors du changement de mot de passe. Veuillez réessayer plus tard.';
            $this->redirect('mon_compte', ['action' => 'change_password', 'error' => 'true']);
        }
    }
    public function deleteAccount(): void
    {
        $idUser = $_SESSION['idUser'];
        $confirmDelete = isset($_POST['confirmDelete']) ? htmlspecialchars(trim(strtolower($_POST['confirmDelete']))) : null;
        $_SESSION['form_data'] = $_POST;

        // Validation the deleting account of the user connected by an his  ID and confirmDelete
        if (!$idUser || $confirmDelete !== 'je confirme') {
            $_SESSION['error'] = 'Texte de confirmation invalide!';
            $this->redirect('mon_compte', ['action' => 'delete_account', 'error' => 'true']);
        }
        $deletedAt = (new DateTime())->format('Y-m-d H:i:s');
        $deleteUser = $this->repo->deleteUser($idUser, $deletedAt);
        if (!$deleteUser) {
            $_SESSION['error'] = 'Une erreur s\'est produite lors de la suppression de votre compte. Veuillez réessayer plus tard.';
            $this->redirect('mon_compte', ['action' => 'delete_account', 'error' => 'true']);
        }

        // Send confirmation email of account deletion
        if ($deleteUser) {
            $mail = new Mail();
            $subject = 'Confirmation de la suppression de votre compte';
            $body = "Votre compte a été supprimé avec succès. Si vous souhaitez réactiver votre compte, veuillez contacter le support dans les 6 mois suivant la suppression.<br>";
            $mail->sendEmail(ADMIN_EMAIL, ADMIN_SENDER_NAME, $_SESSION['email'], $_SESSION['firstName'], $subject, $body);
        }
        // Log out the user and return success message
        session_destroy();
        session_start();
        $_SESSION['success'] = 'Votre compte a été supprimé avec succès!';
        $this->redirect('connexion');
    }
    public function editProfilePicture(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $currentPicturePath = $_SESSION['avatarPath'] ?? null;

            // Handle image upload using Helper
            $helper = new Helper();
            $avatarPath = $helper->handleImageUpload('profilePicture', 'avatars');

            // Delete old profile picture if not default
            if ($currentPicturePath && strpos($currentPicturePath, HOME_URL . 'assets/images/uploads/avatars/default_avatar.png') === false) {
                $oldImagePath = str_replace(BASE_URL . HOME_URL, '', $currentPicturePath);
                $helper->handleDeleteImage($oldImagePath);
            }

            // Build new profile picture URL
            $newProfilePicturePath = BASE_URL . HOME_URL . $avatarPath;

            // Update database
            $this->repo->updateProfilePicture($idUser, $newProfilePicturePath);
            $_SESSION['avatarPath'] = $newProfilePicturePath;

            $_SESSION['success'] = 'Votre photo de profil a été mise à jour avec succès!';
            $this->redirect('mon_compte');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mon_compte', ['error' => 'true']);
        }
    }

    public function deleteProfilePicture(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $currentPicturePath = $_SESSION['avatarPath'] ?? null;

            // Check if current profile picture is not the default one
            if (!$currentPicturePath || strpos($currentPicturePath, HOME_URL . 'assets/images/uploads/avatars/default_avatar.png') !== false) {
                throw new Exception('Aucun avatar à supprimer.');
            }

            // Delete file from server using Helper
            $helper = new Helper();
            $oldImagePath = str_replace(BASE_URL . HOME_URL, '', $currentPicturePath);
            $helper->handleDeleteImage($oldImagePath);

            // Set to default profile picture
            $defaultProfilePicturePath = BASE_URL . HOME_URL . 'assets/images/uploads/avatars/default_avatar.png';
            $this->repo->updateProfilePicture($idUser, $defaultProfilePicturePath);
            $_SESSION['avatarPath'] = $defaultProfilePicturePath;

            $_SESSION['success'] = 'Votre photo de profil a été supprimée avec succès!';
            $this->redirect('mon_compte');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mon_compte');
        }
    }

    public function editBanner(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $currentBannerPath = $_SESSION['bannerPath'] ?? null;

            // Handle image upload using Helper
            $helper = new Helper();
            $bannerPath = $helper->handleImageUpload('banner', 'banners');

            // Delete old banner if not default
            if ($currentBannerPath && strpos($currentBannerPath, HOME_URL . 'assets/images/uploads/banners/default_banner.jpg') === false) {
                $oldImagePath = str_replace(BASE_URL . HOME_URL, '', $currentBannerPath);
                $helper->handleDeleteImage($oldImagePath);
            }

            // Build new banner URL
            $newBannerPath = BASE_URL . HOME_URL . $bannerPath;

            // Update database
            $this->repo->updateBanner($idUser, $newBannerPath);
            $_SESSION['bannerPath'] = $newBannerPath;

            $_SESSION['success'] = 'Votre bannière a été mise à jour avec succès!';
            $this->redirect('mon_compte');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mon_compte', ['error' => 'true']);
        }
    }

    public function deleteBanner(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $currentBannerPath = $_SESSION['bannerPath'] ?? null;

            // Check if current banner is not the default one
            if (!$currentBannerPath || strpos($currentBannerPath, HOME_URL . 'assets/images/uploads/banners/default_banner.jpg') !== false) {
                throw new Exception('Aucune bannière à supprimer.');
            }

            // Delete file from server using Helper
            $helper = new Helper();
            $oldImagePath = str_replace(BASE_URL . HOME_URL, '', $currentBannerPath);
            $helper->handleDeleteImage($oldImagePath);

            // Set to default banner
            $defaultBannerPath = BASE_URL . HOME_URL . 'assets/images/uploads/banners/default_banner.jpg';
            $this->repo->updateBanner($idUser, $defaultBannerPath);
            $_SESSION['bannerPath'] = $defaultBannerPath;

            $_SESSION['success'] = 'Votre bannière a été supprimée avec succès!';
            $this->redirect('mon_compte');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mon_compte', ['error' => 'true']);
        }
    }

    public function displayUserProfile(string $userSlug): void
    {
        try {
            $user = $this->repo->getUserBySlug($userSlug);
            
            if (!$user || $user->getIsDeleted() || $user->getIsBanned() || !$user->getIsActivated()) {
                throw new Exception("Le profil demandé n'existe pas");
            }

            // Check friendship status if user is connected
            $friendshipStatus = null;
            $isOwnProfile = false;
            
            if (isset($_SESSION['idUser'])) {
                $currentUserId = $_SESSION['idUser'];
                $isOwnProfile = ($currentUserId === $user->getIdUser());
                
                if (!$isOwnProfile) {
                    // Check if current user sent request to this user
                    $sentStatus = $this->friendRepo->getFriendshipStatus($currentUserId, $user->getIdUser());
                    // Check if this user sent request to current user
                    $receivedStatus = $this->friendRepo->getFriendshipStatus($user->getIdUser(), $currentUserId);
                    
                    if ($sentStatus === 'accepte' || $receivedStatus === 'accepte') {
                        $friendshipStatus = 'friends';
                    } elseif ($sentStatus === 'en_attente') {
                        $friendshipStatus = 'request_sent';
                    } elseif ($receivedStatus === 'en_attente') {
                        $friendshipStatus = 'request_received';
                    } elseif ($sentStatus === 'bloque') {
                        $friendshipStatus = 'blocked_by_me';
                    } elseif ($receivedStatus === 'bloque') {
                        $friendshipStatus = 'blocked_by_them';
                    } else {
                        $friendshipStatus = 'none';
                    }
                }
            }

            $this->render('user/profil_utilisateur', [
                'user' => $user,
                'title' => $user->getFirstName() . ' ' . $user->getLastName(),
                'friendshipStatus' => $friendshipStatus,
                'isOwnProfile' => $isOwnProfile
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('404');
        }
    }
}
