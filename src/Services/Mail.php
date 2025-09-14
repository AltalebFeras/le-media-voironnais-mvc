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
     * Usage: $this->generateTemplate($subject, $body)
     */
    private function generateTemplate(string $recipientName, string $subject, string $body): string
    {
        $salutation = $this->getSalutation();
        $souhait = $this->getSouhait();
        $currentYear = date('Y');
        $logoPath = DOMAIN . HOME_URL . 'assets/images/logo/logo.png';

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$subject</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%); padding: 40px 20px; text-align: center; }
        .content { padding: 40px 30px;border-left: 5px solid #3B82F6; border-right: 5px solid #3B82F6; }
        .footer { background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%); padding: 30px 20px; text-align: center; }
        .logo-container { display: inline-flex; align-items: center; }
        .logo-text { font-size: 24px; font-weight: bold; color: #ffffff; margin: 0; }
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
            <a href="/" style="text-decoration: none;">
                <div class="logo-container">
                    <p class="logo-text">
                        <span style="color: #EF4444;">Le </span><span style="color: #10B981;">Média</span><span style="color: #3B82F6;">Voironnais</span>
                    </p>
                </div>
            </a>
        </div>
        <!-- Main Content Section -->
        <div class="content">
            <p class="greeting">$salutation $recipientName,</p>
            <div class="content-body">
                $body
            </div>
            <p class="closing">$souhait !</p>
        </div>
        <!-- Footer Section -->
        <div class="footer">
            <p class="team-name">L'équipe du Média Voironnais</p>
            <p class="team-tagline">Bougez, partagez, progressez !</p>
            <p class="copyright">&copy; $currentYear Le Média Voironnais. Tous droits réservés.</p>
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
     * @param string $senderEmail       The sender's email address.
     * @param string $senderName   The sender's display name.
     * @param string $recipientEmail         The recipient's email address.
     * @param string $recipientName     The recipient's display name.
     * @param string $subject    The subject of the email.
     * @param string $body       The main body content of the email (before templating).
     * @param array  $headers    Optional associative array of additional headers (e.g., ['X-Priority' => '1']).
     *
     * @throws \Exception        If email sending fails or PHPMailer throws an error.
     *
     * @return void
     */

    public function sendEmail($senderEmail, $senderName, $recipientEmail, $recipientName, $subject, $body, $headers = []): bool
    {
        try {
            // Set the charset to UTF-8 for French and English compatibility
            $this->mail->CharSet = 'UTF-8';

            // Generate the template with the subject and body

            $emailContent = $this->generateTemplate($recipientName, $subject, $body);

            // Recipients
            $this->mail->setFrom($senderEmail, $senderName);
            $this->mail->addAddress($recipientEmail, $recipientName);

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
