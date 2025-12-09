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
        $this->mail->Port = PORT;

        // Configure authentication based on environment
        if (defined('SMTP_AUTH') && SMTP_AUTH === true) {
            $this->mail->SMTPAuth = true;
            $this->mail->Username = USERNAME;
            $this->mail->Password = PASSWORD;
        } else {
            // For MailDev - no authentication required
            $this->mail->SMTPAuth = false;
        }

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
        $homeUrl = BASE_URL . HOME_URL;

        return <<<HTML
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>$subject</title>
</head>
<body style='font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 5px; background-color: #f4f4f4;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: 0 auto; background-color: #ffffff;'>
        
        <!-- Header -->
        <tr>
            <td style='background-color: #182848; padding: 40px 20px; text-align: center;'>
                <table width='100%' cellpadding='0' cellspacing='0'>
                    <tr>
                        <td style='text-align: center;'>
                            <a href='$homeUrl' style='text-decoration: none;'>
                                <p style='margin: 0; font-size: 24px; font-weight: bold;'>
                                    <span style='color: #EF4444;'>Le </span><span style='color: #10B981;'>Média </span><span style='color: #3B82F6;'>Voironnais</span>
                                </p>
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Main Content -->
        <tr>
            <td style='padding: 40px 30px; border-left: 5px solid #3B82F6; border-right: 5px solid #3B82F6;'>
                <table width='100%' cellpadding='0' cellspacing='0'>
                    <tr>
                        <td>
                            <p style='color: #333333; font-size: 18px; font-weight: 600; margin: 0 0 25px 0;'>
                                $salutation $recipientName,
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style='color: #555555; font-size: 16px; line-height: 1.6;'>
                            $body
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style='color: #333333; font-size: 16px; font-weight: 500; margin: 25px 0 0 0;'>
                                $souhait !
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style='background-color: #182848; padding: 30px 20px; text-align: center;'>
                <table width='100%' cellpadding='0' cellspacing='0'>
                    <tr>
                        <td style='text-align: center;'>
                            <p style='color: #ffffff; margin: 0; font-weight: 700; font-size: 20px;'>
                                L'équipe du Média Voironnais
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align: center; padding-top: 8px;'>
                            <p style='color: #ffffff; margin: 0; font-size: 14px; opacity: 0.9;'>
                                Bougez, partagez, progressez !
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align: center; padding-top: 20px;'>
                            <p style='color: #ffffff; margin: 0; font-size: 12px; opacity: 0.8;'>
                                &copy; $currentYear Le Média Voironnais. Tous droits réservés.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    public function addAttachment($filePath, $name = '')
    {
        try {
            if (!empty($name)) {
                $this->mail->addAttachment($filePath, $name);
            } else {
                $this->mail->addAttachment($filePath);
            }
        } catch (Exception $e) {
            throw new \Exception("Failed to add attachment: " . $e->getMessage());
        }
    }

    /**
     * Sends an HTML email using the configured PHPMailer instance.
     * This method sets the character encoding to UTF-8 to support multilingual content,
     * generates a styled HTML email using a template, sets sender and recipient information,
     * and optionally applies custom headers before sending the email.
     *
     * @param string $senderEmail       The sender's email address .
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
