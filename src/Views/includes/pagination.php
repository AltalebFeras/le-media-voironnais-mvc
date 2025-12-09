<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <div class="pagination-container">

        <div class="pagination-info">
            <p class="text-info">Page <span class="current-page"><?= $currentPage ?></span> sur <span class="total-pages"><?= $totalPages ?></span></p>
        </div>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>" class="pagination-link">
                    <span class="material-icons">chevron_left</span>
                    <span class="spanBtnName">Précédent</span>
                </a>
            <?php endif; ?>

            <?php
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);

            if ($startPage > 1): ?>
                <a href="?page=1" class="pagination-link">1</a>
                <?php if ($startPage > 2): ?>
                    <span class="pagination-dots">...</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a href="?page=<?= $i ?>" class="pagination-link <?= $i === $currentPage ? 'active' : 'pagination-link-inactive' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($endPage < $totalPages): ?>
                <?php if ($endPage < $totalPages - 1): ?>
                    <span class="pagination-dots">...</span>
                <?php endif; ?>
                <a href="?page=<?= $totalPages ?>" class="pagination-link"><?= $totalPages ?></a>
            <?php endif; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>" class="pagination-link">
                    <span class="spanBtnName">Suivant</span>
                    <span class="material-icons">chevron_right</span>
                </a>
            <?php endif; ?>
        </div>


    </div>
<?php endif; ?>

<style>

</style>