<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- to be removed in production final version -->
  <meta name="robots" content="noindex, nofollow">
  <!-- to be removed in production final version -->


  <?php
  // SEO Variables - can be set from individual pages
  $title = !empty($title) ? (ucfirst($title) . ' | Le Media Voironnais') : 'Le Media Voironnais';
  $description ??= 'Le Media Voironnais - Votre source d\'information locale à Voiron';
  $keywords ??= 'media, voiron, actualités, information locale, voironnais';
  $author ??= 'Le Media Voironnais';
  $ogImage ??= HOME_URL . 'assets/images/og-default.jpg';
  $ogType ??= 'website';
  $twitterCard ??= 'summary_large_image';
  $twitterSite ??= '@lemediavoironnais';
  $canonicalUrl ??= HOME_URL . ltrim($_SERVER['REDIRECT_URL'], '/');
  $locale ??= 'fr_FR';
  $siteName ??= 'Le Media Voironnais';
  ?>

  <title><?= $title ?></title>

  <!-- SEO Meta Tags -->
  <meta name="description" content="<?= htmlspecialchars($description) ?>">
  <meta name="keywords" content="<?= htmlspecialchars($keywords) ?>">
  <meta name="author" content="<?= htmlspecialchars($author) ?>">
  <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">

  <!-- Open Graph Meta Tags -->
  <meta property="og:title" content="<?= htmlspecialchars($title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
  <meta property="og:type" content="<?= htmlspecialchars($ogType) ?>">
  <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
  <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
  <meta property="og:site_name" content="<?= htmlspecialchars($siteName) ?>">
  <meta property="og:locale" content="<?= htmlspecialchars($locale) ?>">

  <!-- Twitter Card Meta Tags -->
  <meta name="twitter:card" content="<?= htmlspecialchars($twitterCard) ?>">
  <meta name="twitter:title" content="<?= htmlspecialchars($title) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($description) ?>">
  <meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>">
  <?php if (isset($twitterSite)): ?>
    <meta name="twitter:site" content="<?= htmlspecialchars($twitterSite) ?>">
  <?php endif; ?>
  <?php if (isset($twitterCreator)): ?>
    <meta name="twitter:creator" content="<?= htmlspecialchars($twitterCreator) ?>">
  <?php endif; ?>

  <!-- Additional SEO Tags -->
  <meta name="language" content="French">
  <meta name="revisit-after" content="7 days">
  <meta name="rating" content="general">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  
  <!-- Geographic Tags -->
  <?php if (isset($geoRegion)): ?>
    <meta name="geo.region" content="<?= htmlspecialchars($geoRegion) ?>">
  <?php endif; ?>
  <?php if (isset($geoPlacename)): ?>
    <meta name="geo.placename" content="<?= htmlspecialchars($geoPlacename) ?>">
  <?php endif; ?>
  <?php if (isset($geoPosition)): ?>
    <meta name="geo.position" content="<?= htmlspecialchars($geoPosition) ?>">
  <?php endif; ?>
  <?php if (isset($icbm)): ?>
    <meta name="ICBM" content="<?= htmlspecialchars($icbm) ?>">
  <?php endif; ?>

  <!-- Article Specific Tags (for news articles) -->
  <?php if (isset($publishedTime)): ?>
    <meta property="article:published_time" content="<?= htmlspecialchars($publishedTime) ?>">
  <?php endif; ?>
  <?php if (isset($modifiedTime)): ?>
    <meta property="article:modified_time" content="<?= htmlspecialchars($modifiedTime) ?>">
  <?php endif; ?>
  <?php if (isset($articleAuthor)): ?>
    <meta property="article:author" content="<?= htmlspecialchars($articleAuthor) ?>">
  <?php endif; ?>
  <?php if (isset($articleSection)): ?>
    <meta property="article:section" content="<?= htmlspecialchars($articleSection) ?>">
  <?php endif; ?>
  <?php if (isset($articleTags) && is_array($articleTags)): ?>
    <?php foreach ($articleTags as $tag): ?>
      <meta property="article:tag" content="<?= htmlspecialchars($tag) ?>">
    <?php endforeach; ?>
  <?php endif; ?>


  <!-- Favicon and Touch Icons -->
  <link rel="icon" href="<?= HOME_URL . 'assets/favicon/favicon.ico' ?>" type="image/x-icon">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= HOME_URL . 'assets/favicon/apple-touch-icon.png' ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= HOME_URL . 'assets/favicon/favicon-32x32.png' ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= HOME_URL . 'assets/favicon/favicon-16x16.png' ?>">
  <link rel="manifest" href="<?= HOME_URL . 'assets/favicon/site.webmanifest' ?>">

  <!-- Make HOME_URL available to JavaScript -->
  <script>
    window.HOME_URL = '<?= HOME_URL ?>';
    window.BASE_URL = '<?= BASE_URL ?>';
  </script>

  <!-- jQuery -->
  <script src="<?= HOME_URL . 'assets/javascript/jquery/dist/jquery.min.js' ?>"></script>
 
  <!-- Global CSS -->
  <link rel="stylesheet" href="<?= HOME_URL . 'assets/css/global.css' ?>">