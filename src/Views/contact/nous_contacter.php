<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/others/nous_contacter.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="pt">
    <div class="contact-hero">
        <div class="container">
            <div class="contact-hero-content">
                <h1>Nous contacter</h1>
                <p>Une question, une suggestion ou besoin d'aide ? N'hésitez pas à nous contacter !</p>
            </div>
        </div>
    </div>

    <div class="container">
        <?php include_once __DIR__ . '/../includes/messages.php'; ?>
        
        <div class="contact-container">
            <!-- Contact Form -->
            <div class="contact-form-section">
                <div class="contact-form-header">
                    <h2>Envoyez-nous un message</h2>
                    <p>Remplissez le formulaire ci-dessous et nous vous répondrons dans les plus brefs délais.</p>
                </div>

                <form method="POST" action="<?= HOME_URL ?>nous_contacter" class="contact-form">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">Prénom *</label>
                            <input type="text" 
                                   id="firstName" 
                                   name="firstName" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['firstName'] ?? '') ?>" 
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Nom *</label>
                            <input type="text" 
                                   id="lastName" 
                                   name="lastName" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['lastName'] ?? '') ?>" 
                                   required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>" 
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Téléphone</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['phone'] ?? '') ?>" 
                                   placeholder="06 12 34 56 78">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject">Sujet *</label>
                        <select id="subject" name="subject" required>
                            <option value="">-- Choisissez un sujet --</option>
                            <option value="Question générale" <?= ($_SESSION['form_data']['subject'] ?? '') === 'Question générale' ? 'selected' : '' ?>>Question générale</option>
                            <option value="Problème technique" <?= ($_SESSION['form_data']['subject'] ?? '') === 'Problème technique' ? 'selected' : '' ?>>Problème technique</option>
                            <option value="Suggestion d'amélioration" <?= ($_SESSION['form_data']['subject'] ?? '') === 'Suggestion d\'amélioration' ? 'selected' : '' ?>>Suggestion d'amélioration</option>
                            <option value="Signalement de contenu" <?= ($_SESSION['form_data']['subject'] ?? '') === 'Signalement de contenu' ? 'selected' : '' ?>>Signalement de contenu</option>
                            <option value="Demande de partenariat" <?= ($_SESSION['form_data']['subject'] ?? '') === 'Demande de partenariat' ? 'selected' : '' ?>>Demande de partenariat</option>
                            <option value="Presse et média" <?= ($_SESSION['form_data']['subject'] ?? '') === 'Presse et média' ? 'selected' : '' ?>>Presse et média</option>
                            <option value="Autre" <?= ($_SESSION['form_data']['subject'] ?? '') === 'Autre' ? 'selected' : '' ?>>Autre</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" 
                                  name="message" 
                                  rows="6" 
                                  placeholder="Décrivez votre demande en détail..." 
                                  required><?= htmlspecialchars($_SESSION['form_data']['message'] ?? '') ?></textarea>
                        <small class="form-help">Minimum 10 caractères, maximum 2000 caractères</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <span class="material-icons">send</span>
                            Envoyer le message
                        </button>
                    </div>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="contact-info-section">
                <div class="contact-info-card">
                    <h3>Informations de contact</h3>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <span class="material-icons">location_on</span>
                        </div>
                        <div class="contact-details">
                            <h4>Adresse</h4>
                            <p>123 Avenue de la République<br>38500 Voiron, France</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <span class="material-icons">phone</span>
                        </div>
                        <div class="contact-details">
                            <h4>Téléphone</h4>
                            <p><a href="tel:+33476050123">04 76 05 01 23</a></p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <span class="material-icons">email</span>
                        </div>
                        <div class="contact-details">
                            <h4>Email</h4>
                            <p><a href="mailto:contact@lemediavoironnais.fr">contact@lemediavoironnais.fr</a></p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <span class="material-icons">schedule</span>
                        </div>
                        <div class="contact-details">
                            <h4>Horaires</h4>
                            <p>Lundi - Vendredi : 9h00 - 18h00<br>
                               Weekend : Sur rendez-vous</p>
                        </div>
                    </div>
                </div>

                <div class="contact-faq-card">
                    <h3>Questions fréquentes</h3>
                    
                    <div class="faq-item">
                        <h4>Comment créer un compte ?</h4>
                        <p>Cliquez sur "Inscription" dans le menu et remplissez le formulaire. Vous recevrez un email de confirmation.</p>
                    </div>

                    <div class="faq-item">
                        <h4>Comment créer un événement ?</h4>
                        <p>Une fois connecté, accédez à "Mes événements" puis "Ajouter un événement" dans votre tableau de bord.</p>
                    </div>

                    <div class="faq-item">
                        <h4>Sous quel délai recevrai-je une réponse ?</h4>
                        <p>Nous nous engageons à répondre à tous les messages dans un délai de 24 à 48 heures ouvrées.</p>
                    </div>

                    <div class="faq-item">
                        <h4>Comment signaler un problème ?</h4>
                        <p>Utilisez le formulaire ci-contre en sélectionnant "Problème technique" ou "Signalement de contenu".</p>
                    </div>
                </div>

                <div class="contact-social-card">
                    <h3>Suivez-nous</h3>
                    <p>Restez connecté avec nous sur les réseaux sociaux</p>
                    
                    <div class="social-links">
                        <a href="#" class="social-link facebook">
                            <i class="fab fa-facebook-f"></i>
                            <span>Facebook</span>
                        </a>
                        <a href="#" class="social-link twitter">
                            <i class="fab fa-twitter"></i>
                            <span>Twitter</span>
                        </a>
                        <a href="#" class="social-link instagram">
                            <i class="fab fa-instagram"></i>
                            <span>Instagram</span>
                        </a>
                        <a href="#" class="social-link linkedin">
                            <i class="fab fa-linkedin-in"></i>
                            <span>LinkedIn</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>