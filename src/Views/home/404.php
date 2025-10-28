<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/404.css' ?>">
<main>
	<div class="page_404">
		<div class="container_404">
			<div class="four_zero_four_bg">
				<h1>404</h1>
			</div>
			<div class="content_box_404">
				<h2>Oups ! Page introuvable</h2>
				<p>La page que vous recherchez n'existe pas, a été déplacée ou supprimée.</p>
				<p>Ne vous inquiétez pas, nous sommes là pour vous aider à retrouver votre chemin !</p>

				<div class="error_description">
					<h3>Que pouvez-vous faire ?</h3>
					<ul>
						<li>Vérifier l'URL pour détecter d'éventuelles erreurs de frappe</li>
						<li>Utiliser la barre de recherche ci-dessous pour trouver ce que vous cherchez</li>
						<li>Consulter les liens utiles pour naviguer vers les pages principales</li>
						<li>Retourner à la page d'accueil et explorer notre contenu</li>
					</ul>
				</div>

				<div class="search_box">
					<form action="<?= HOME_URL . 'search'; ?>" method="GET">
						<input type="text" name="q" placeholder="Rechercher sur le site..." aria-label="Recherche">
					</form>
				</div>

				<div class="helpful_links">
					<a href="<?= HOME_URL . 'evenements'; ?>" class="helpful_link">📰 Événements</a>
					<a href="<?= HOME_URL . 'nous_contacter'; ?>" class="helpful_link">✉️ Nous Contacter</a>
				</div>

				<?php if (isset($_SESSION['connected']) && $_SESSION['connected'] === true) : ?>
					<a href="<?= HOME_URL . 'dashboard'; ?>" class="link_404">Retour au tableau de bord</a>
				<?php else : ?>
					<a href="<?= HOME_URL; ?>" class="link_404">Retour à l'accueil</a>
				<?php endif; ?>

				<div class="stats_info">
					<div class="stat_item">
						<span class="number">404</span>
						<span class="label">Code Erreur</span>
					</div>
					<div class="stat_item">
						<span class="number">24/7</span>
						<span class="label">Support</span>
					</div>
					<div class="stat_item">
						<span class="number">100%</span>
						<span class="label">Gratuit</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>