<?php

namespace src\Services;

class ConfigRouter
{
    /**
     *  function to verify the method of the request 
     * @return string
     */
    public static function getMethod()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === "POST" && isset($_POST['_method']) && !empty($_POST['_method'])) {
            switch ($_POST['_method']) {
                case 'DELETE':
                case 'POST':
                    $method = "POST";
                    break;
                case 'GET':
                    $method = "GET";
                    break;
                default:
                    //
                    break;
            }
        }

        return $method;
    }
    /**
     * function to check the connection of the user
     * It checks if the session is started, if not it starts a new session.
     * It then checks if the session variable 'machine_fingerprint' is set and matches the current server data fingerprint.
     * If the fingerprint does not match, it destroys the session and redirects the user to the sign-in page with an error message.  
     * @return bool
     */
    public static function checkConnection(): bool
    {
        $runningServerData = [
            'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? '',
            'HTTP_ACCEPT_LANGUAGE' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            'HTTP_ACCEPT_ENCODING' => $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
            'HTTP_ACCEPT' => $_SERVER['HTTP_ACCEPT'] ?? '',
        ];
        // Create a fingerprint based on the server data
        $actualFingerprint = hash('sha256', json_encode($runningServerData));
        // get the fingerprint stored in the session and matche it with the actual fingeprint
        $fingerprintStockedInSession = $_SESSION['machine_fingerprint'] ?? null;
        if (!isset($fingerprintStockedInSession) || $fingerprintStockedInSession !== $actualFingerprint) {
            session_destroy();
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error'] = 'Votre session est expirée! veuillez vous reconnecter.';
            header('Location: ' . HOME_URL . 'signIn');
            exit();
        }

        return true;
    }
}
