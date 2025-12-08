<div class="container mx-auto px-4 py-8 max-w-4xl">
    <article class="prose lg:prose-xl">
        <?php if ($post['imagePath']): ?>
            <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['imagePath']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full rounded-lg mb-6">
        <?php endif; ?>
        
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        
        <div class="flex items-center gap-3 not-prose mb-6">
            <?php if ($post['authorType'] === 'user'): ?>
                <img src="<?= htmlspecialchars($post['user_avatar']) ?>" alt="Avatar" class="w-12 h-12 rounded-full">
                <div>
                    <p class="font-semibold"><?= htmlspecialchars($post['user_firstName'] . ' ' . $post['user_lastName']) ?></p>
                    <p class="text-sm text-gray-500"><?= date('d/m/Y à H:i', strtotime($post['createdAt'])) ?></p>
                </div>
            <?php elseif ($post['authorType'] === 'association'): ?>
                <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['association_logo']) ?>" alt="Logo" class="w-12 h-12 rounded-full">
                <div>
                    <p class="font-semibold"><?= htmlspecialchars($post['association_name']) ?></p>
                    <p class="text-sm text-gray-500"><?= date('d/m/Y à H:i', strtotime($post['createdAt'])) ?></p>
                </div>
            <?php else: ?>
                <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['entreprise_logo']) ?>" alt="Logo" class="w-12 h-12 rounded-full">
                <div>
                    <p class="font-semibold"><?= htmlspecialchars($post['entreprise_name']) ?></p>
                    <p class="text-sm text-gray-500"><?= date('d/m/Y à H:i', strtotime($post['createdAt'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <div><?= nl2br(htmlspecialchars($post['content'])) ?></div>
    </article>
    
    <div class="mt-8">
        <a href="<?= HOME_URL ?>actu" class="btn btn-outline">← Retour aux actualités</a>
    </div>
</div>
