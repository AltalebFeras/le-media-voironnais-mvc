<?php

namespace src\Controllers;

use DateTime;
use Error;
use PDOException;
use PHPMailer\PHPMailer\Exception;
use src\Abstracts\AbstractController;
use src\Models\User;
use src\Repositories\UserRepository;
use src\Services\Encrypt_decrypt;
use src\Services\Mail;

class UserController extends AbstractController
{
    protected $repo;
    public function __construct($repo = null)
    {
        $this->repo = $repo ?? new UserRepository();
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
            'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? '',
            'HTTP_ACCEPT_LANGUAGE' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            'HTTP_ACCEPT_ENCODING' => $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
            'HTTP_ACCEPT' => $_SERVER['HTTP_ACCEPT'] ?? '',
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
    private function sendEmail($firstName, $lastName, $email, $activationToken): bool
    {
        $encrypting = new  Encrypt_decrypt();
        $activationLink = DOMAIN . HOME_URL . 'activate_my_account?token=' . $activationToken . '&email=' . $encrypting->encryptId($email);

        // activation email
        $mail = new Mail();
        $subject = 'Activation de votre compte';
        $body = "Bonjour {$firstName} {$lastName},<br><br>";
        $body .= "Veuillez cliquer sur le lien ci-dessous pour activer votre compte:<br>";
        $body .= "<a href='$activationLink'>Click here</a><br><br>";
        $body .= "<br><br>Merci,<br>L'équipe de support.";

        $sendEmail = $mail->sendEmail('account-activation@feras.fr', 'feras', $email, $lastName, $subject, $body);
        if ($sendEmail) {
            return true;
        } else {
            return false;
        }
    }
    public function treatmentInscription()
    {
        $firstName = isset($_POST['firstName']) ? htmlentities(trim(ucfirst($_POST['firstName']))) : null;
        $lastName = isset($_POST['lastName']) ? htmlentities(trim(ucfirst($_POST['lastName']))) : null;
        $email = isset($_POST['email']) ? htmlentities(trim(strtolower($_POST['email']))) : null;
        $password = isset($_POST['password']) ? trim($_POST['password']) : null;
        $passwordConfirmation = isset($_POST['passwordConfirmation']) ? trim($_POST['passwordConfirmation']) : null;
        $rgpd = isset($_POST['rgpd']) ? htmlentities(trim($_POST['rgpd'])) : null;

        $_SESSION['form_data'] = $_POST;
        $errors = [];

        if ($password !== $passwordConfirmation) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $activationToken = bin2hex(random_bytes(16));
        $profilePicturePath = DOMAIN . HOME_URL . 'assets/uploads/profilePictures/defaultProfile.png';
        $isActivated = false;
        $roleId = 2;
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
        if (strlen($password) < 4) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 4 caractères';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'adresse e-mail est invalide';
        }
        if ($rgpd !== 'on') {
            $errors['rgpd'] = 'Veuillez accepter les conditions d\'utilisation et la politique de confidentialité.';
        } else {
            $rgpdDate = date('Y-m-d H:i:s');
        }
        // Verify reCAPTCHA
        // $recaptchaStatus = $this->checkReCaptcha();
        // if ($recaptchaStatus === false) {
        //     $errors['recaptcha'] = 'Veuillez vérifier que vous n\'êtes pas un robot.';
        // }

        $emailExists = $this->repo->getUser($email);

        if ($emailExists) {
            $errors['email'] = 'Cette adresse e-mail est déjà utilisée';
        }
        // If there are any validation errors, throw one Error with all errors
        $this->returnAllErrors($errors, 'inscription');

        $user = new User([
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'password' => $passwordHash,
            'isActivated' => $isActivated,
            'token' => $activationToken,
            'createdAt' => $createdAt,
            'profilePicturePath' => $profilePicturePath,
            'roleId' => $roleId,
            'rgpdDate' => $rgpdDate
        ]);

        $sendEmail = $this->sendEmail($firstName, $lastName, $email, $activationToken);
        if ($sendEmail) {
            $signUp = $this->repo->signUp($user);
        } else {
            $errors['sendEmail'] = 'Une erreur s\'est produite lors de l\'envoi de l\'e-mail d\'activation. Veuillez réessayer plus tard.';
            $this->returnAllErrors($errors, 'inscription');
        }
        if ($signUp) {
            $_SESSION['success'] = 'Un e-mail d\'activation a été envoyé à votre adresse e-mail. Veuillez vérifier votre boîte de réception et cliquer sur le lien d\'activation pour activer votre compte.';
            $this->redirect('connexion');
        } else {
            $errors['signUp'] = 'Une erreur s\'est produite lors de l\'inscription. Veuillez réessayer plus tard.';
            $this->returnAllErrors($errors, 'inscription');
        }
    }


    public function activateAccount(): void
    {
        try {
            $token = isset($_GET['token']) ? htmlspecialchars(trim($_GET['token'])) : null;

            if (!$token || !preg_match('/^[a-f0-9]{32}$/', $token)) {
                throw new Exception('Le lien d\'activation est invalide ou a expiré.');
            }

            if (strlen($token) !== 32 || !ctype_xdigit($token)) {
                throw new Exception('Le lien d\'activation est invalide ou a expiré.');
            }
            $user = $this->repo->getUserByToken($token);

            if ($user) {
                $userId = $user->getUserId();
                $activateAccount = $this->repo->activateUser($userId);
                if ($activateAccount) {
                    $_SESSION['success'] = 'Votre compte a été activé avec succès! Vous pouvez maintenant vous connecter.';
                    $this->redirect('connexion');
                }
            } else {
                throw new Exception('Le lien d\'activation est invalide ou a expiréeeeeeee.');
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('connexion', ['error' => 'true']);
        }
    }



    public function treatmentConnexion()
    {

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
        if ($user && !password_verify($password, $user->getPassword())) {
            $errors['password'] = 'L\'adresse e-mail est invalide ou le mot de passe est incorrect.';
        }

        if ($user && $user->getIsActivated() === false) {
            $errors['isActivated'] = 'Votre compte n\'est pas encore activé. Veuillez vérifier votre boîte de réception pour l\'e-mail d\'activation.';
        }
     if (IS_PROD === TRUE) {
            // Verify reCAPTCHA
            $recaptchaStatus = $this->checkReCaptcha();
            if ($recaptchaStatus === false) {
                $errors['recaptcha'] = 'Veuillez vérifier que vous n\'êtes pas un robot.';
            }
        }

        $this->returnAllErrors($errors, 'connexion');


        if ($user) {

            $_SESSION['userId'] = $user->getUserId();
            $_SESSION['firstName'] = $user->getFirstName();
            $_SESSION['lastName'] = $user->getLastName();
            $_SESSION['email'] = $user->getEmail();
            $_SESSION['profilePicturePath'] = $user->getprofilePicturePath();
            $_SESSION['isActivated'] = $user->getIsActivated();
            $_SESSION['createdAt'] = $user->getCreatedAtFormatted();
            $_SESSION['updatedAt'] = $user->getUpdatedAtFormatted();
            $_SESSION['roleName'] = $user->getRoleName();

            // Prevent session fixation  
            session_regenerate_id(true);

            $this->makeFingerprint();

            $_SESSION['connected'] = true;
            $_SESSION['lastConnection'] = date('Y-m-d H:i:s');

            $_SESSION['success'] = 'Vous êtes connecté avec succès!';
            $this->redirect('dashboard');
        }
    }

    public function displayDashboard()
    {
        if (!isset($_GET['error'])) {
            unset($_SESSION['form_data']);
        }
        $this->render('dashboard/dashboard');
    }

    public function treatmentForgotMyPassword()
    {
        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : null;
        $_SESSION['form_data'] = $_POST;
        $errors = [];
        if (empty($email)) {
            $errors['field'] = 'Veuillez entrer votre adresse e-mail!';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'adresse e-mail est invalide!';
        }
        // Verify reCAPTCHA
        $recaptchaStatus = $this->checkReCaptcha();
        if ($recaptchaStatus === false) {
            $errors['recaptcha'] = 'Veuillez vérifier que vous n\'êtes pas un robot.';
        }
        $this->returnAllErrors($errors, 'forget_my_password');
        $user = $this->repo->getUser($email);

        if ($user) {
            $userId = $user->getUserId();

            // Check if the user has requested a password reset in the last 5 minutes
            $lastRequest = $user->getResetPasswordRequestTime();
            if ($lastRequest) {
                $last = strtotime($lastRequest);
                $now = time();
                $waitSeconds = 300;
                if ($now - $last < $waitSeconds) {
                    $errors['time'] = 'Vous avez déjà demandé une réinitialisation récemment. Veuillez patienter cinq minutes avant de réessayer.';
                    $this->returnAllErrors($errors, 'forget_my_password');
                }
            }

            // if user has a reset password token, i use it, otherwise i generate a new one
            $user->getResetPasswordToken() ? $resetPasswordToken = $user->getResetPasswordToken() : $resetPasswordToken = bin2hex(random_bytes(16));
            $this->repo->saveResetPasswordToken($userId, $resetPasswordToken);

            // Update the last reset request time
            $resetPasswordRequestTime = $user->setResetPasswordRequestTime(new DateTime());
            $resetPasswordRequestTime = $user->getResetPasswordRequestTime();
            $this->repo->updateResetPasswordRequestTime($userId, $resetPasswordRequestTime);

            // send password reset email (same as before)
            $resetLink = DOMAIN . HOME_URL . "reset_my_password?token=$resetPasswordToken";
            $mail = new Mail();
            $subject = 'Réinitialisation de votre mot de passe';
            $body = "Bonjour " . $user->getFirstName() . " " . $user->getLastName() . ",<br><br>";
            $body .= "Veuillez cliquer sur le lien ci-dessous pour réinitialiser votre mot de passe:<br>";
            $body .= "<br><br>";
            $body .= "<a href='$resetLink'>Réinitialiser le mot de passe</a><br><br>";
            $body .= "<br><br>Merci,<br>L'équipe de support.";

            $mail->sendEmail('do_not_reply@feras.fr', 'feras', $user->getEmail(), $user->getFirstName(), $subject, $body);

            $_SESSION['success'] = 'Un e-mail de réinitialisation du mot de passe a été envoyé à votre adresse e-mail. Veuillez vérifier votre boîte de réception et cliquer sur le lien pour réinitialiser votre mot de passe.';
            $this->redirect('connexion');
        } else {
            $errors['email'] = 'Aucun compte trouvé avec cette adresse e-mail.';
            $this->returnAllErrors($errors, 'forget_my_password');
        }
    }

    public function treatmentResetPassword()
    {
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
        $user = $this->repo->getUserByResetPasswordToken($token);

        if (!$user) {
            $errors['token'] = 'Le lien de réinitialisation du mot de passe est invalide ou a expiré.';
        }

        // Verify reCAPTCHA
        $recaptchaStatus = $this->checkReCaptcha();
        if ($recaptchaStatus === false) {
            $errors['recaptcha'] = 'Veuillez vérifier que vous n\'êtes pas un robot.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . HOME_URL . 'reset_my_password?token=' . $token . '&error=true');
            exit();
        }
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $userId = $user->getUserId();
        $resetPassword = $this->repo->resetPassword($userId, $passwordHash);
        if ($resetPassword) {
            $_SESSION['success'] = 'Votre mot de passe a été réinitialisé avec succès! Vous pouvez maintenant vous connecter.';
            $this->redirect('connexion');
        } else {
            $_SESSION['error'] = 'Une erreur s\'est produite lors de la réinitialisation du mot de passe. Veuillez réessayer plus tard.';
            header('Location: ' . HOME_URL . 'reset_my_password?token=' . $token . '&error=true');
            exit();
        }
    }
    public function displayMyAccount()
    {
        if (!isset($_GET['error'])) {
            unset($_SESSION['form_data']);
        }
        include_once __DIR__ . '/../Views/account/my_account.php';
    }
    public function editProfile()
    {
        try {
            $firstName = isset($_POST['firstName']) ? htmlspecialchars(trim(ucfirst($_POST['firstName']))) : null;
            $lastName = isset($_POST['lastName']) ? htmlspecialchars(trim(ucfirst($_POST['lastName']))) : null;
            $email = isset($_POST['email']) ? htmlspecialchars(trim(strtolower($_POST['email']))) : null;
            $userId = $_SESSION['userId'];
            $updatedAt = new DateTime();
            $errors = [];
            $_SESSION['form_data'] =  $_POST;

            if (empty($firstName) || empty($lastName) || empty($email)) {
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
            if (strlen($email) < 5 || strlen($email) > 255) {
                $errors['emailLength'] = 'L\'adresse e-mail doit contenir entre 5 et 255 caractères';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'L\'adresse e-mail est invalide';
            }

            $user = $this->repo->getUser($email);

            if ($user && $user->getUserId() !== $userId) {
                $errors['emailExist'] = 'Cette adresse e-mail est déjà utilisée';
            }
            // If there are any validation errors, throw one Error with all errors
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . HOME_URL . 'my_account?action=edit_profile&error=true');
                exit();
            }

            $user = new User([
                'userId' => $userId,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'updatedAt' => $updatedAt,
            ]);

            $updateUser = $this->repo->updateUser($user);
            if ($updateUser) {
                $_SESSION['firstName'] = $user->getFirstName();
                $_SESSION['lastName'] = $user->getLastName();
                $_SESSION['email'] = $user->getEmail();
                $_SESSION['updatedAt'] = $user->getUpdatedAt();

                unset($_SESSION['form_data']);
                $_SESSION['success'] = 'Votre profil a été mis à jour avec succès!';
                header('Location: ' . HOME_URL . 'my_account');
            } else {
                throw new Exception("Aucune modification n’a été effectuée.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . HOME_URL . 'my_account?action=edit_profile&error=true');
            exit();
        }
    }
    public function changePassword()
    {
        $userId = $_SESSION['userId'];
        $currentPassword = isset($_POST['currentPassword']) ? htmlspecialchars($_POST['currentPassword']) : null;
        $newPassword = isset($_POST['newPassword']) ? htmlspecialchars($_POST['newPassword']) : null;
        $confirmPassword = isset($_POST['confirmPassword']) ? htmlspecialchars($_POST['confirmPassword']) : null;
        $errors = [];
        $_SESSION['form_data'] = $_POST;
        // i check the current password
        $user = $this->repo->getUserById($userId);
        if (!password_verify($currentPassword, $user->getPassword())) {
            $errors['currentPassword'] = 'Current password is incorrect.';
        }

        //  if new password matches confirm password
        if ($newPassword !== $confirmPassword) {
            $errors['confirmPassword'] = 'New passwords do not match.';
        }
        if (strlen($newPassword) < 8) {
            $errors['length'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . HOME_URL . 'my_account?action=change_password&error=true');
            exit();
        }
        // Hash the new password
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $changePassword = $this->repo->updatePassword($userId, $newPasswordHash);
        if ($changePassword) {
            $_SESSION['success'] = 'Votre mot de passe a été changé avec succès!';
            header('Location: ' . HOME_URL . 'my_account');
            exit();
        } else {
            $_SESSION['error'] = 'Une erreur s\'est produite lors du changement de mot de passe. Veuillez réessayer plus tard.';
            header('Location: ' . HOME_URL . 'my_account?action=change_password&error=true');
            exit();
        }
    }
    public function deleteAccount()
    {
        $userId = $_SESSION['userId'];
        $confirmDelete = isset($_POST['confirmDelete']) ? htmlspecialchars(trim(strtolower($_POST['confirmDelete']))) : null;
        $_SESSION['form_data'] = $_POST;

        // Validation the deleting account of the user connected by an his  ID and confirmDelete
        if (!$userId || $confirmDelete !== 'je confirme') {
            $_SESSION['error'] = 'Texte de confirmation invalide!';
            header('Location: ' . HOME_URL . 'my_account?action=delete_account&error=true');
            exit();
        }

        $this->repo->deleteUser($userId);

        // Log out the user and return success message
        session_destroy();
        session_start();
        $_SESSION['success'] = 'Votre compte a été supprimé avec succès!';
        $this->redirect('connexion');
    }
    public function editProfilePicture()
    {
        $userId = $_SESSION['userId'];
        $currentPicturePath = $_SESSION['profilePicturePath'] ?? null;

        // check if a file was uploaded
        if (!empty($_FILES) && isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/assets/uploads/profilePictures/';
            $fileName = uniqid() . '_' . basename($_FILES['profilePicture']['name']);
            $uploadFile = "{$uploadDir}{$fileName}";

            // make the move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $uploadFile)) {
                // then delete the old profile picture from the server if it's not the default picture
                if ($currentPicturePath && strpos($currentPicturePath, HOME_URL . 'assets/uploads/profilePicture/defaultProfile.png') === false) {
                    $oldFilePath = str_replace(HOME_URL . 'assets/uploads/', __DIR__ . '/../../public/assets/uploads/', $currentPicturePath);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath); // here delete the old file from the server
                    }
                }
                $newProfilePicturePath = DOMAIN . HOME_URL . 'assets/uploads/profilePictures/' . $fileName;
                $this->repo->updateProfilePicture($userId, $newProfilePicturePath);

                $_SESSION['profilePicturePath'] = $newProfilePicturePath;

                $_SESSION['success'] = 'Votre photo de profil a été mise à jour avec succès!';
                header('Location: ' . HOME_URL . 'my_account');
                exit();
            } else {
                $_SESSION['error'] = 'Une erreur s\'est produite lors du téléchargement de la photo de profil. Veuillez réessayer.';
                header('Location: ' . HOME_URL . 'my_account?error=true');
                exit();
            }
        } else {

            $_SESSION['error'] = 'Veuillez sélectionner une image à télécharger.';
            header('Location: ' . HOME_URL . 'my_account?error=true');
            exit();
        }
    }
    public function deleteProfilePicture()
    {
        $userId = $_SESSION['userId'];
        $currentPicturePath = $_SESSION['profilePicturePath']   ?? null;
        // check if the current profile picture is not the default one
        if (!$currentPicturePath || strpos($currentPicturePath, HOME_URL . 'assets/uploads/profilePictures/defaultProfile.png') !== false) {
            $_SESSION['error'] = 'Vous ne pouvez pas supprimer la photo de profil par défaut.';
            header('Location: ' . HOME_URL . 'my_account');
            exit();
        }
        // here we delete file from server if it's not the default picture
        if ($currentPicturePath && strpos($currentPicturePath, HOME_URL . 'assets/uploads/profilePictures/defaultProfile.png') === false) {
            $filePath = str_replace(DOMAIN . HOME_URL . 'assets/uploads/', __DIR__ . '/../../public/assets/uploads/', $currentPicturePath);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // then we update profile picture path in the database to the default picture
        $defaultProfilePicturePath = DOMAIN . HOME_URL . 'assets/uploads/profilePictures/defaultProfile.png';
        $this->repo->updateProfilePicture($userId, $defaultProfilePicturePath);

        // we set default profile picture in session
        $_SESSION['profilePicturePath'] = $defaultProfilePicturePath;

        // finally we redirect with success message
        $_SESSION['success'] = 'Votre photo de profil a été supprimée avec succès!';
        header('Location: ' . HOME_URL . 'my_account');
        exit();
    }
}
