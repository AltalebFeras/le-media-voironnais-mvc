<?php

namespace src\Services;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../../vendor/autoload.php';

final class Mail
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->configureSMTP();
    }
    private function configureSMTP()
    {
        // SMTP configuration
        $this->mail->isSMTP();
        $this->mail->Host = HOST;
        $this->mail->SMTPAuth = true;
        $this->mail->Port = PORT;
        $this->mail->Username = USERNAME;
        $this->mail->Password = PASSWORD;

        // For email on server side only, it uses the SSL protocol
        if (IS_PROD === true) {
            $this->mail->SMTPSecure = 'ssl';
        }
    }
    private function generateTemplate($HOME_URL, $DOMAIN, $logoPath, $subject, $body)
    {
        return "
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset='utf-8'>
                <meta http-equiv='x-ua-compatible' content='ie=edge'>
                <title>Tirso</title>
                <meta name='viewport' content='width=device-width, initial-scale=1'>
                <style>
                    /* Global Styles */
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        color: #333;
                        background-color: #f4f4f9;
                        margin: 20px;
                        padding: 0px ;
                    }
                    .container {
                        max-width: 600px;
                        margin: 20px auto;
                        background: #fff;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    }
                    .email-header {
                        background:rgb(119, 233, 102);
                        padding: 20px;
                        text-align: center;
                    }
                    .email-header .logo {
                        max-width: 120px;
                    }
                    .email-content {
                        border-left: 1px solid black; 
                        border-right: 1px solid black;
                        padding: 20px;
                    }
                    .email-content h3 {
                        color: #004f9e;
                        margin-bottom: 10px;
                    }
                    h3 {
                        font-size: 18px;
                    }
                    .email-content p {
                        font-size: 16px;
                        margin-bottom: 15px;
                    }
                    .footer-text {
                        background: #2A6174;
                        text-align: center;
                        padding: 15px;
                        font-size: 14px;
                        color: white;
                    }
                    .footer-text p {
                        margin: 0;
                    }
                </style>
            </head>

            <body>
                <div class='container'>
                    <!-- Email Header -->
                    <div class='email-header'>
                    <a href='{$DOMAIN}{$HOME_URL}'><img src='{$logoPath}' alt='Logo de site tirage au sort' class='logo'></a>
                    </div>

                    <!-- Email Content -->
                    <div class='email-content'>
                        <h3>{$subject}</h3>
                        <p>{$body}</p>
                    </div>

                    <!-- Email Footer -->
                    <div class='footer-text'>
                        <p>&copy; " . date('Y') . " TIRSO. Tous droits réservés.</p>
                    </div>
                </div>
            </body>
        </html>";
    }

    private function getSalutation(): string
    {
        $time = (int) date('H');
        if ($time < 12) {
            return 'Bonjour';
        } elseif ($time < 16) {
            return 'Bon après-midi';
        }
        return 'Bonsoir';
    }

    private function getSouhait(): string
    {
        $time = (int) date('H');
        if ($time < 4 || $time >= 22) {
            return 'Bonne nuit';
        } elseif ($time < 12) {
            return 'Bonne journée';
        } elseif ($time < 16) {
            return 'Bon après-midi';
        }
        return 'Bonne soirée';
    }

    /**
     * Alternative template generator with salutation and souhait.
     * Usage: $this->generateTemplateV2($subject, $body)
     */
    private function generateTemplateV2(string $subject, string $body): string
    {
        $salutation = $this->getSalutation();
        $souhait = $this->getSouhait();
        $currentYear = date('Y');
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{\$subject}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%); padding: 40px 20px; text-align: center; }
        .content { padding: 40px 30px;border-left: 5px solid #3B82F6; border-right: 5px solid #3B82F6; }
        .footer { background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%); padding: 30px 20px; text-align: center; }
        .logo-container { display: inline-flex; align-items: center; }
        .logo-svg { width: 32px; height: 32px; margin-right: 8px; }
        .logo-text { font-size: 24px; font-weight: bold; color: #ffffff; margin: 0; }
        .tagline { color: #ffffff; margin: 15px 0 0 0; font-size: 16px; opacity: 0.9; }
        .greeting { color: #333333; font-size: 18px; font-weight: 600; margin: 0 0 25px 0; }
        .content-body { color: #555555; font-size: 16px; line-height: 1.6; margin: 0 0 25px 0; }
        .closing { color: #333333; font-size: 16px; font-weight: 500; margin: 25px 0 0 0; }
        .team-name { color: #ffffff; font-size: 20px; font-weight: 700; margin: 0; }
        .team-tagline { color: #ffffff; font-size: 14px; margin: 8px 0 0 0; opacity: 0.9; }
        .copyright { color: #ffffff; font-size: 12px; margin: 20px 0 0 0; opacity: 0.8; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <a href="https://gosportif365.netlify.app/" style="text-decoration: none;">
                <div class="logo-container">
                    <svg xmlns="http://www.w3.org/2000/svg" class="logo-svg" viewBox="0 0 24 24" fill="none">
                        <path d="M15 8V6a3 3 0 00-6 0v2" stroke="#3B82F6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M5 10v4a2 2 0 002 2h2" stroke="#F59E42" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M17 10h2a2 2 0 012 2v4a2 2 0 01-2 2h-2" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M9 16v2a2 2 0 002 2h0a2 2 0 002-2v-2m-4 0h4" stroke="#f63bc7ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="logo-text">
                        <span style="color: #EF4444;">Go</span><span style="color: #10B981;">Sportif</span><span style="color: #3B82F6;">365</span>
                    </p>
                </div>
            </a>
            <p class="tagline">Votre plateforme de défis sportifs</p>
        </div>
        <!-- Main Content Section -->
        <div class="content">
            <p class="greeting">{\$salutation},</p>
            <div class="content-body">
                {\$body}
            </div>
            <p class="closing">{\$souhait} !</p>
        </div>
        <!-- Footer Section -->
        <div class="footer">
            <p class="team-name">L'équipe GoSportif365</p>
            <p class="team-tagline">Bougez, partagez, progressez !</p>
            <p class="copyright">&copy; {\$currentYear} GoSportif365. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    public function addAttachment($filePath)
    {
        try {
            $this->mail->addAttachment($filePath);
        } catch (Exception $e) {
            throw new \Exception("Failed to add attachment: " . $e->getMessage());
        }
    }

    /**
     * Sends an HTML email using the configured PHPMailer instance.
     *
     * This method sets the character encoding to UTF-8 to support multilingual content,
     * generates a styled HTML email using a template, sets sender and recipient information,
     * and optionally applies custom headers before sending the email.
     *
     * @param string $from       The sender's email address.
     * @param string $fromName   The sender's display name.
     * @param string $to         The recipient's email address.
     * @param string $toName     The recipient's display name.
     * @param string $subject    The subject of the email.
     * @param string $body       The main body content of the email (before templating).
     * @param array  $headers    Optional associative array of additional headers (e.g., ['X-Priority' => '1']).
     *
     * @throws \Exception        If email sending fails or PHPMailer throws an error.
     *
     * @return void
     */

    public function sendEmail($from, $fromName, $to, $toName, $subject, $body, $headers = []): bool
    {
        try {
            // Set the charset to UTF-8 for French and English compatibility
            $this->mail->CharSet = 'UTF-8';

            // Generate the template with the subject and body
            $HOME_URL = HOME_URL;
            $DOMAIN = DOMAIN;
            $logoPath = DOMAIN . HOME_URL . 'assets/images/logo/logo.png';
            $emailContent = $this->generateTemplate($HOME_URL, $DOMAIN, $logoPath, $subject, $body);

            // Recipients
            $this->mail->setFrom($from, $fromName);
            $this->mail->addAddress($to, $toName);

            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = mb_encode_mimeheader($subject, 'UTF-8');
            $this->mail->Body = $emailContent;

            // Apply custom headers
            foreach ($headers as $key => $value) {
                $this->mail->addCustomHeader($key, $value);
            }

            // Send the email
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$this->mail->ErrorInfo}");
            throw new \Exception("Failed to send email: " . $e->getMessage());
        }
    }
}
