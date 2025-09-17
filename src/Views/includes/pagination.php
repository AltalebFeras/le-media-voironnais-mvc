<!-- Pagination -->
<?php
$cp = max(1, (int)($currentPage ?? 1));
$tp = max(1, (int)($totalPages ?? 1));
$prev = $cp > 1 ? $cp - 1 : null;
$next = $cp < $tp ? $cp + 1 : null;
// Keep other GET params if present
$queryBase = $_GET;
?>
<div class="pager" role="navigation" aria-label="Pagination utilisateurs">
    <?php
    // previous link
    $queryPrev = $queryBase;
    $queryPrev['page'] = $prev ?? 1;
    $hrefPrev = '?' . http_build_query($queryPrev);
    ?>
    <a class="page-link <?= $prev ? '' : 'disabled'; ?>" href="<?= $hrefPrev; ?>">Précédent</a>

    <span class="page-info">Page <?= $cp; ?> / <?= $tp; ?></span>

    <?php
    $queryNext = $queryBase;
    $queryNext['page'] = $next ?? $tp;
    $hrefNext = '?' . http_build_query($queryNext);
    ?>
    <a class="page-link <?= $next ? '' : 'disabled'; ?>" href="<?= $hrefNext; ?>">Suivant</a>
</div>