<?php
// Display all public posts with filtering
$currentFilter = $filter ?? null;
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Actualités</h1>
    
    <!-- Filter buttons -->
    <div class="flex gap-4 mb-8">
        <a href="<?= HOME_URL ?>actu" class="btn <?= !$currentFilter ? 'btn-primary' : 'btn-outline' ?>">Toutes</a>
        <a href="<?= HOME_URL ?>actu?filter=user" class="btn <?= $currentFilter === 'user' ? 'btn-primary' : 'btn-outline' ?>">Utilisateurs</a>
        <a href="<?= HOME_URL ?>actu?filter=association" class="btn <?= $currentFilter === 'association' ? 'btn-primary' : 'btn-outline' ?>">Associations</a>
        <a href="<?= HOME_URL ?>actu?filter=entreprise" class="btn <?= $currentFilter === 'entreprise' ? 'btn-primary' : 'btn-outline' ?>">Entreprises</a>
    </div>

    <!-- Posts grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($posts as $post): ?>
            <article class="card bg-base-100 shadow-xl">
                <?php if ($post['imagePath']): ?>
                    <figure><img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['imagePath']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-48 object-cover"></figure>
                <?php endif; ?>
                
                <div class="card-body">
                    <div class="flex items-center gap-2 mb-2">
                        <?php if ($post['authorType'] === 'user'): ?>
                            <img src="<?= htmlspecialchars($post['user_avatar']) ?>" alt="Avatar" class="w-8 h-8 rounded-full">
                            <span class="text-sm"><?= htmlspecialchars($post['user_firstName'] . ' ' . $post['user_lastName']) ?></span>
                        <?php elseif ($post['authorType'] === 'association'): ?>
                            <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['association_logo']) ?>" alt="Logo" class="w-8 h-8 rounded-full">
                            <span class="text-sm"><?= htmlspecialchars($post['association_name']) ?></span>
                        <?php else: ?>
                            <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['entreprise_logo']) ?>" alt="Logo" class="w-8 h-8 rounded-full">
                            <span class="text-sm"><?= htmlspecialchars($post['entreprise_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <h2 class="card-title"><?= htmlspecialchars($post['title']) ?></h2>
                    <p><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>
                    <p class="text-sm text-gray-500"><?= date('d/m/Y', strtotime($post['createdAt'])) ?></p>
                    
                    <div class="card-actions justify-end">
                        <a href="<?= HOME_URL ?>actu/<?= htmlspecialchars($post['uiid']) ?>" class="btn btn-primary btn-sm">Lire la suite</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="join mt-8 flex justify-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= HOME_URL ?>actu?page=<?= $i ?><?= $currentFilter ? '&filter=' . $currentFilter : '' ?>" 
                   class="join-item btn <?= $i === $currentPage ? 'btn-active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
