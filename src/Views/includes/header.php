<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?php
  /** translation title */
  
  $title = '';
  $title = explode('/', trim($_SERVER['REDIRECT_URL']));
  $title = $title[1];
  
  $titles = [
    'accueil' => 'Accueil',
    'inscription' => 'Inscription',
    'connexion' => 'Connexion',
    'cgu' => 'CGU',
    'mentions_legales' => 'Mentions légales',
    'dashboard' => 'Dashboard',
  ];
  
  if (empty($title)) {
    $title = 'Le Media Voironnais';
  } elseif (isset($titles[$title])) {
    $title =  $titles[$title];
    $title = ucfirst($title) . ' - Le Media Voironnais';
  } else {
    $title = ucfirst($title) . ' - Le Media Voironnais';
  }
  ?>
  <title><?= $title ?></title>

  <link rel="icon" href="<?= HOME_URL . 'assets/favicon/favicon.ico' ?>" type="image/x-icon">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= HOME_URL . 'assets/favicon/apple-touch-icon.png' ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= HOME_URL . 'assets/favicon/favicon-32x32.png' ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= HOME_URL . 'assets/favicon/favicon-16x16.png' ?>">
  <link rel="manifest" href="<?= HOME_URL . 'assets/favicon/site.webmanifest' ?>">
  <link rel="stylesheet" href="<?= HOME_URL . 'assets/css/global.css' ?>">