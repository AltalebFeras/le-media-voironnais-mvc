<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Mes Actualités</h1>
        <a href="<?= HOME_URL ?>post/ajouter" class="btn btn-primary">+ Nouvelle actualité</a>
    </div>

    <?php if (empty($posts)): ?>
        <div class="alert alert-info">
            <p>Vous n'avez pas encore créé d'actualité.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($posts as $post): ?>
                <div class="card bg-base-100 shadow-xl">
                    <?php if ($post['imagePath']): ?>
                        <figure><img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['imagePath']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-48 object-cover"></figure>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h2 class="card-title"><?= htmlspecialchars($post['title']) ?></h2>
                        <p><?= htmlspecialchars(substr($post['content'], 0, 100)) ?>...</p>
                        
                        <div class="badge <?= $post['isPublished'] ? 'badge-success' : 'badge-warning' ?>">
                            <?= $post['isPublished'] ? 'Publié' : 'Brouillon' ?>
                        </div>
                        
                        <div class="card-actions justify-end mt-4">
                            <a href="<?= HOME_URL ?>post/modifier?uiid=<?= htmlspecialchars($post['uiid']) ?>" class="btn btn-sm btn-primary">Modifier</a>
                            <form method="POST" action="<?= HOME_URL ?>post/supprimer" onsubmit="return confirm('Êtes-vous sûr ?')">
                                <input type="hidden" name="uiid" value="<?= htmlspecialchars($post['uiid']) ?>">
                                <button type="submit" class="btn btn-sm btn-error">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="join mt-8 flex justify-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= HOME_URL ?>mes_posts?page=<?= $i ?>" class="join-item btn <?= $i === $currentPage ? 'btn-active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
