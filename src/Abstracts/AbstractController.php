<?php

namespace src\Abstracts;

use Error;
use Exception;
use src\Repositories\NotificationRepository;

abstract class AbstractController
{
    /**
     * Render a view file with the given data.
     *
     * @param string $view The name of the view file and if it is in a folder should add the folder name like folder name/file name (without extension).
     * @param array $data An associative array of data to pass to the view, it is empty by default.
     * @return void
     */
    public function render($view, array $data = []): void
    {
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewPath)) {
            extract($data);
            include $viewPath;
            // $this->unsetFormData();
        } else {
            throw new Exception("View not found: {$view}");
        }
    }
    /**
     * Redirect to a specified route with optional query parameters.
     **query error=true with this format array ['error' => 'true'] should be added to the URL for all error happen.
     * @param string $route The route exists and defined in the router to redirect to.
     * @param array $query An associative array of query parameters to append to the URL , it is empty by default.
     * @return void
     */
    public function redirect($route, array $query = []): void
    {
        $url = HOME_URL . $route;
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }
        header("Location: {$url}");
        // $this->unsetFormData();
        exit();
    }

    /**
     * Handle and redirect all errors to a specified route.
     * This method checks if the $errors parameter is not empty, and if so, it stores the errors in the session and redirects to the specified route with an error query parameter.
     * If the $errors parameter is empty, it unsets the 'errors' session variable
     * @param mixed $errors
     * @param mixed $route
     * @return void
     */
    public function returnAllErrors($errors, $route, array $query = []): void
    {
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect($route, $query);
        } else {
            unset($_SESSION['errors']);
        }
    }
    /**
     * Check if the reCAPTCHA response is valid.
     * This method retrieves the reCAPTCHA response from the POST data, checks if it is empty, and then verifies it against the Google reCAPTCHA API.
     * If the verification fails, it adds an error message to the $errors array.
     * @return void
     */

    public function checkReCaptcha(): bool
    {
        $recaptchaResponse = $_POST['g-recaptcha-response'];

        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . SECRET_KEY . "&response=$recaptchaResponse");
        $responseKeys = json_decode($response, true);

        if (empty($recaptchaResponse) || intval($responseKeys["success"]) !== 1) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Generate CSRF token and store it in session
     * @return string The generated CSRF token
     */
    public function generateCsrfToken(): string
    {
        $csrfToken = bin2hex(random_bytes(64));
        $_SESSION['csrf_token'] = $csrfToken;
        return $csrfToken;
    }

    /**
     * Validate CSRF token from POST request
     * @return bool Returns true if token is valid, false otherwise
     * @param mixed $route 
     * @param array $errors
     * @return bool Returns true if token is valid, false otherwise 
     */
    public function validateCsrfToken($route): bool
    {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            $errors['csrf'] = 'Token de sécurité invalide. Veuillez réessayer.';
            $this->returnAllErrors($errors, $route, ['error' => 'true']);
            // return false;
        }

        $isValid = hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);

        // Regenerate token after validation (one-time use)
        if ($isValid) {
            unset($_SESSION['csrf_token']);
        }
        // add counter for invalid attempts and block after 10 attempts
        if (!isset($_SESSION['invalid_attempts'])) {
            $_SESSION['invalid_attempts'] = 0;
        }

        if (!$isValid) {
            $_SESSION['invalid_attempts']++;
            if ($_SESSION['invalid_attempts'] >= 10) {

                header("HTTP/1.1 403 Forbidden");
                exit('Trop de tentatives invalides. Veuillez réessayer plus tard.');
            }
            $errors['csrf'] = 'Token de sécurité invalide. Veuillez réessayer.';
            $this->returnAllErrors($errors, $route, ['error' => 'true']);
            // return false;
        } else {
            // Reset counter on successful validation
            unset($_SESSION['invalid_attempts']);
        }

        return $isValid;
    }


    /**
     * Send a notification to a user.
     *
     * @param int $idUser The user ID to notify.
     * @param int $idEvenement The event ID related to the notification.
     * @param string $type The type of notification (e.g.,'activation','inscription','preinscription','invitation','mise_a_jour','rappel','systeme','alert','message','autre').
     * @param string $title The notification title.
     * @param string $message The notification message.
     * @param string $url The URL related to the notification.
     * @return bool True if notification was sent, false otherwise.
     */
    public function sendNotification(int $idUser, string $type, string $title, string $message, string $url , int $priority): bool
    {
        $notification = new NotificationRepository();
        $data = [
            'idUser' => $idUser,
            'idEvenement' => null,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'priority' => $priority ?? false,
            'createdAt' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
        return $notification->pushNotification($data);
    }

    /**
     * Summary of sendNotificationForEvent
     * @param int $idUser
     * @param int $idEvenement
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string $url
     * @param int $priority
     * @return bool
     */
    public function sendNotificationForEvent(int $idUser, int $idEvenement, string $type, string $title, string $message, string $url, int $priority): bool
    {
        $notification = new NotificationRepository();
        $data = [
            'idUser' => $idUser,
            'idEvenement' => $idEvenement,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'priority' => $priority ?? false,
            'createdAt' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
        return $notification->pushNotification($data);
    }
}
