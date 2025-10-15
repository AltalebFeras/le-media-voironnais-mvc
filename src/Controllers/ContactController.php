<?php

namespace src\Controllers;

use DateTime;
use Exception;
use src\Abstracts\AbstractController;
use src\Models\Contact;
use src\Repositories\ContactRepository;
use src\Services\Mail;
use src\Services\Helper;

class ContactController extends AbstractController
{
    protected $repo;

    public function __construct()
    {
        $this->repo = new ContactRepository();
    }

    /**
     * Display contact form
     */
    public function displayContactForm(): void
    {
        $this->render('contact/nous_contacter', ['csrf_token' => $this->generateCsrfToken()]);
    }

    /**
     * Submit contact form
     */
    public function submitContactForm(): void
    {
        try {
            $this->validateCsrfToken('nous_contacter');

            // Get form data
            $firstName = isset($_POST['firstName']) ? htmlspecialchars(trim($_POST['firstName'])) : null;
            $lastName = isset($_POST['lastName']) ? htmlspecialchars(trim($_POST['lastName'])) : null;
            $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : null;
            $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : null;
            $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : null;
            $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : null;

            $errors = [];
            $_SESSION['form_data'] = $_POST;

            // Create contact object
            $contact = new Contact();
            $helper = new Helper();
            $uiid = $helper->generateUiid();

            $contact->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail($email)
                ->setPhone($phone)
                ->setSubject($subject)
                ->setMessage($message)
                ->setStatus('nouveau')
                ->setCreatedAt((new DateTime())->format('Y-m-d H:i:s'))
                ->setUiid($uiid);

            // Validate
            $modelErrors = $contact->validate();
            $errors = array_merge($errors, $modelErrors);

            // Check for existing recent contact from same email interval 60 minutes
            $recentContacts = $this->repo->searchContacts($email);
            if (!empty($recentContacts)) {
                $lastContact = $recentContacts[0];
                $lastContactTime = new DateTime($lastContact->getCreatedAt());
                $now = new DateTime();
                $interval = $now->diff($lastContactTime);
                if ($interval->i < 60 && $interval->h == 0 && $interval->days == 0) {
                    $errors['email'] = "Vous avez déjà envoyé un message récemment. Veuillez attendre avant d'envoyer un nouveau message.";
                }
            }

            $this->returnAllErrors($errors, 'nous_contacter', ['error' => 'true']);

            // Save contact
            $created = $this->repo->createContact($contact);
            $idAdmin = 1;
            $type = 'contact';
            $title = 'Nouveau message de contact';
            $message = 'Vous avez reçu un nouveau message de contact de ' . $firstName . ' ' . $lastName . '.';
            $url = HOME_URL . 'admin/contacts';
            $priority = 0;

            if ($created) {
                // Send notification email to admin
                $this->sendNotification($idAdmin, $type, $title, $message, $url, $priority);

                if ($_SESSION['connected']) {
                    // Send in-app notification to user
                    $idUser = $_SESSION['idUser'];
                    $type = 'message';
                    $title = 'Confirmation de réception de votre message';
                    $message = 'Nous avons bien reçu votre message et nous vous répondrons dans les plus brefs délais.';
                    $url = HOME_URL;
                    $priority = 0;

                    $this->sendNotification($idUser, $type, $title, $message, $url, $priority);
                } else {
                    // Send confirmation email to user
                    $this->sendConfirmationToUser($contact);
                }

                unset($_SESSION['form_data']);
                $_SESSION['success'] = "Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.";
                $this->redirect('nous_contacter');
            } else {
                throw new Exception("Erreur lors de l'envoi du message");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('nous_contacter', ['error' => 'true']);
        }
    }

    /**
     * Send confirmation email to user
     */
    private function sendConfirmationToUser(Contact $contact): void
    {
        try {
            $mail = new Mail();

            $subject = "Confirmation de réception de votre message";

            $body = "
                <h3>Bonjour " . $contact->getFirstName() . " " . $contact->getLastName() . ",</h3>
                
                <p>Nous avons bien reçu votre message concernant : <strong>" . $contact->getSubject() . "</strong></p>
                
                <p>Notre équipe prendra connaissance de votre demande et vous répondra dans les plus brefs délais, généralement sous 24 à 48 heures ouvrées.</p>
                
                <h4>Récapitulatif de votre message :</h4>
                <div style='background-color: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 15px 0;'>
                    " . nl2br($contact->getMessage()) . "
                </div>
                
                <p>Si vous avez des questions urgentes, n'hésitez pas à nous contacter directement.</p>
                
                <p>Cordialement,<br>
                L'équipe du Média Voironnais</p>
                
                <hr>
                <p style='font-size: 0.9em; color: #666;'>
                    Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.
                </p>
            ";

            $mail->sendEmail(
                ADMIN_EMAIL,
                ADMIN_SENDER_NAME,
                $contact->getEmail(),
                $contact->getFirstName() . ' ' . $contact->getLastName(),
                $subject,
                $body
            );
        } catch (Exception $e) {
            // Log error but don't throw exception to not break the main flow
            error_log("Erreur envoi email confirmation contact: " . $e->getMessage());
        }
    }

    /**
     * Get contact by ID (for admin use)
     */
    private function getId(): int|null
    {
        if (isset($_GET['uiid'])) {
            $uiid = htmlspecialchars(trim($_GET['uiid']));
        } elseif (isset($_POST['uiid'])) {
            $uiid = htmlspecialchars(trim($_POST['uiid']));
        } else {
            $uiid = null;
        }
        return $this->repo->getIdByUiid($uiid);
    }

    /**
     * Display all contacts for admin (with pagination and filtering)
     */
    public function displayAllContacts(): void
    {
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $contactsPerPage = 20;
        $status = isset($_GET['status']) ? htmlspecialchars(trim($_GET['status'])) : null;

        // Get contacts with pagination and filtering
        $contacts = $this->repo->getAllContacts($currentPage, $contactsPerPage, $status);
        $totalContacts = $this->repo->countContacts($status);
        $totalPages = (int)ceil($totalContacts / $contactsPerPage);

        // Get statistics for filter tabs
        $stats = [
            'total' => $this->repo->countContacts(),
            'nouveau' => $this->repo->countContacts('nouveau'),
            'lu' => $this->repo->countContacts('lu'),
            'traite' => $this->repo->countContacts('traite'),
            'archive' => $this->repo->countContacts('archive')
        ];

        $this->render('admin/contacts_list', [
            'contacts' => $contacts,
            'totalContacts' => $totalContacts,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'stats' => $stats,
            'title' => 'Messages de Contact'
        ]);
    }

    /**
     * Mark contact as read
     */
    public function markContactAsRead(): void
    {
        try {
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;

            if (!$uiid) {
                throw new Exception("Contact invalide");
            }

            $contact = $this->repo->getContactByUiid($uiid);
            if (!$contact) {
                throw new Exception("Contact introuvable");
            }

            $idContact = $contact->getIdContact();
            $hasResponse = !empty($contact->getResponse()) && !empty($contact->getRepliedAt());

            // If contact has response, cannot mark as 'lu'
            if ($hasResponse) {
                throw new Exception("Impossible de marquer comme lu un message qui a déjà reçu une réponse");
            }

            $this->repo->updateContactStatus($idContact, 'lu');

            $_SESSION['success'] = "Le message a été marqué comme lu";
            $this->redirect('admin/contacts');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('admin/contacts');
        }
    }

    /**
     * Mark contact as treated
     */
    public function markContactAsTreated(): void
    {
        try {
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;

            if (!$uiid) {
                throw new Exception("Contact invalide");
            }

            $contact = $this->repo->getContactByUiid($uiid);
            if (!$contact) {
                throw new Exception("Contact introuvable");
            }

            $idContact = $contact->getIdContact();
            $hasResponse = !empty($contact->getResponse()) && !empty($contact->getRepliedAt());

            // Can only mark as treated if there is a response
            if (!$hasResponse) {
                throw new Exception("Impossible de marquer comme traité un message sans réponse");
            }

            $this->repo->updateContactStatus($idContact, 'traite');

            $_SESSION['success'] = "Le message a été marqué comme traité";
            $this->redirect('admin/contacts');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('admin/contacts');
        }
    }

    /**
     * Archive contact
     */
    public function archiveContact(): void
    {
        try {
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;

            if (!$uiid) {
                throw new Exception("Contact invalide");
            }

            $idContact = $this->repo->getIdByUiid($uiid);
            if (!$idContact) {
                throw new Exception("Contact introuvable");
            }

            // Archive is always allowed regardless of response status
            $this->repo->updateContactStatus($idContact, 'archive');

            $_SESSION['success'] = "Le message a été archivé";
            $this->redirect('admin/contacts');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('admin/contacts');
        }
    }

    /**
     * Reply to contact message
     */
    public function replyToContact(): void
    {
        try {
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
            $message = isset($_POST['replyMessage']) ? htmlspecialchars(trim($_POST['replyMessage'])) : null;

            if (!$uiid || !$message) {
                throw new Exception("Tous les champs sont requis");
            }

            $contact = $this->repo->getContactByUiid($uiid);
            if (!$contact) {
                throw new Exception("Contact introuvable");
            }

            // Check if already replied
            $hasResponse = !empty($contact->getResponse()) && !empty($contact->getRepliedAt());
            if ($hasResponse) {
                throw new Exception("Une réponse a déjà été envoyée pour ce message");
            }

            $idContact = $contact->getIdContact();
            $email = $contact->getEmail();
            $fullName = $contact->getFirstName() . ' ' . $contact->getLastName();
            $subject = 'Réponse: ' . $contact->getSubject();

            // Send email reply
            $mail = new Mail();
            $emailSent = $mail->sendEmail(
                ADMIN_EMAIL,
                ADMIN_SENDER_NAME,
                $email,
                $fullName,
                $subject,
                nl2br($message)
            );

            if ($emailSent) {
                // Save response in database and automatically set status to 'traite'
                $this->repo->addResponse($idContact, $message);

                $_SESSION['success'] = "Votre réponse a été envoyée avec succès et le message a été marqué comme traité";
            } else {
                throw new Exception("Erreur lors de l'envoi de l'email");
            }

            $this->redirect('admin/contacts');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('admin/contacts', ['error' => 'true']);
        }
    }

    /**
     * Delete contact message
     */
    public function deleteContact(): void
    {
        try {
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;

            if (!$uiid) {
                throw new Exception("Contact invalide");
            }

            $idContact = $this->repo->getIdByUiid($uiid);
            if (!$idContact) {
                throw new Exception("Contact introuvable");
            }

            $this->repo->deleteContact($idContact);

            $_SESSION['success'] = "Le message a été supprimé";
            $this->redirect('admin/contacts');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('admin/contacts');
        }
    }
}
