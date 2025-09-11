<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="bodyMainContent page_404">

    <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>
	<h1>Page introuvable</h1>

    <div class="four_zero_four_bg">
		</div>
	
    <div class="contant_box_404">
		<p>La page que vous recherchez n'est pas disponible!</p>
		<h3>
			On dirait que vous êtes perdu
        </h3>

		<p>La page que vous recherchez n'existe pas ou a été supprimée.</p>
		<?php if (isset($_SESSION['connected']) && $_SESSION['connected'] === true) : ?>
			<a href="/dashboard" class="link_404">Retour au tableau de bord</a>
		<?php else : ?>
			<a href="/" class="link_404">Retour à l'accueil</a>
		<?php endif; ?>
    </div>

</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>