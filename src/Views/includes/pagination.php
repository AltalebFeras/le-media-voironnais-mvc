<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <div class="pagination-container">
        
         <div class="pagination-info">
            <p>Page <?= $currentPage ?> sur <?= $totalPages ?></p>
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
    /* Pagination */
    .pagination-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-top: 3rem;
    }

    .pagination {
        display: flex;
        gap: 8px;
        background: white;
        padding: 10px;
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
    }

    .pagination-link {
        display: flex;
        padding: 12px 16px;
        background: transparent;
        color: #666;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-weight: 500;
        min-width: 44px;
        text-align: center;
    }

    .pagination-link:hover {
        background: #f0f0f0;
        color: #333;
    }

    .pagination-link.active {
        background: #667eea;
        color: white;
        box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
    }

    @media (max-width: 768px) {
        .pagination {
            gap: 4px;
            padding: 8px;
        }

        .pagination-link {
            padding: 8px 10px;
            font-size: 14px;
            min-width: 36px;
        }

        .spanBtnName {
            display: none;
        }

        .pagination-link .material-icons {
            font-size: 20px;
        }

        .pagination-dots {
            padding: 8px 4px;
            font-size: 12px;
        }
    }

    @media (max-width: 480px) {
        .pagination {
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }

        .pagination-link {
            max-width: 200px;
            justify-content: center;
        }

        .pagination-link-inactive {
            display: none;
        }

        .pagination-dots {
            display: none;
        }
    }
</style>