<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/tous_les_utilisateurs.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar_admin.php'; ?>

<main>
	<div class="container py-4">
		<h1>Tous les utilisateurs</h1>
		<?php include_once __DIR__ . '/../includes/messages.php'; ?>

		<?php
		// Safely show total users if provided by controller
		$totalUsers = isset($totalUsers) ? (int)$totalUsers : null;
		?>
		<?php if ($totalUsers !== null): ?>
			<p class="text-muted" style="margin-top:0.25rem;">Total utilisateurs : <?= $totalUsers; ?></p>
		<?php endif; ?>


		<?php
		// Expecting variables: $allUsers (array), $currentPage, $totalPages
		$users = $allUsers ?? [];
		?>

		<?php if (empty($users)): ?>
			<p>Aucun utilisateur trouvé.</p>
		<?php else: ?>
			<div class="users-list">
				<?php foreach ($users as $user): ?>
					<?php
					$id = $user['id'] ?? '';
					$first = $user['firstName'] ?? '';
					$last = $user['lastName'] ?? '';
					$email = $user['email'] ?? '';
					$created = $user['createdAt'] ?? '';
					$avatar = !empty($user['avatarPath']) ? $user['avatarPath'] : (BASE_URL . HOME_URL . 'assets/images/uploads/avatars/default_avatar.png');
					$online = !empty($user['isOnline']) && ((int)$user['isOnline'] === 1);
					// initials fallback
					$initials = '';
					if (!$avatar) {
						$initials = trim(($user['firstName'] ?? '') . ' ' . ($user['lastName'] ?? ''));
						$initials = array_filter(explode(' ', $initials));
						$initials = array_map(function ($s) {
							return mb_substr($s, 0, 1);
						}, $initials);
						$initials = strtoupper(implode('', array_slice($initials, 0, 2)));
						if ($initials === '') $initials = '#' . $id;
					}
					?>
					<article class="card user-card" role="article" aria-labelledby="user-<?= $id; ?>">
						<div class="user-header">
							<span class="avatar" aria-hidden="true">
								<?php if ($avatar): ?>
									<img src="<?= $avatar; ?>" alt="Avatar <?= $id; ?>">
								<?php else: ?>
									<span><?= $initials; ?></span>
								<?php endif; ?>
							</span>
							<div class="user-title">
								<span class="user-name" id="user-<?= $id; ?>"><?= ($first || $last) ? $first . ' ' . $last : 'Utilisateur #' . $id; ?></span>
								<span class="status-dot <?= $online ? 'status-online' : 'status-offline'; ?>" title="<?= $online ? 'En ligne' : 'Hors ligne'; ?>" aria-hidden="false"></span>
							</div>
						</div>
						<div class="email"><strong>Email:</strong> <?= $email; ?></div>
						<div class="created">Inscrit le: <?= (new DateTime($created))->format('d/m/Y à H:i'); ?></div>

						<div class="actions">
							<a href="<?= HOME_URL . 'admin/utilisateur_details?id=' . $id; ?>" class="btn btn-info linkNotDecorated" aria-label="Voir détails utilisateur <?= $id; ?>">Voir détails</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
			<?php include_once __DIR__ . '/../includes/pagination.php'; ?>
		<?php endif; ?>
	</div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>