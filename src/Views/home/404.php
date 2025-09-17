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
				<p>La page que vous recherchez n'existe pas ou a été supprimée.</p>
				<p>On dirait que vous êtes perdu...</p>
				<?php if (isset($_SESSION['connected']) && $_SESSION['connected'] === true) : ?>
					<a href="<?= HOME_URL . 'dashboard'; ?>" class="link_404">Retour au tableau de bord</a>
				<?php else : ?>
					<a href="<?= HOME_URL; ?>" class="link_404">Retour à l'accueil</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>