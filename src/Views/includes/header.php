<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?php
  /** translation title */
  $titles = [
    'accueil' => 'Accueil',
    'inscription' => 'Inscription',
    'connexion' => 'Connexion',
    'cgu' => 'CGU',
    'mentions_legales' => 'Mentions légales',
    'dashboard' => 'Dashboard',
  ];

  $title = '';
  $title = explode('/', trim($_SERVER['REDIRECT_URL']));
  $title = $title[1];

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

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer" />
  <?php
  $route = $_SERVER['REDIRECT_URL'];

  // Include CSS file based on current route. This is just a placeholder. Replace with your own logic.
  switch ($route) {

    case HOME_URL:
      echo '<link rel="stylesheet" href="' . HOME_URL . 'assets/styles/accueil.css' . '">';
      break;
    case HOME_URL . 'signIn':
    case HOME_URL . 'signUp':
      echo '<link rel="stylesheet" href="' . HOME_URL . 'assets/styles/auth.css">';
      break;
    case HOME_URL . 'dashboard':
      echo '<link rel="stylesheet" href="' . HOME_URL . 'assets/styles/dashboard.css">';
      break;
    case HOME_URL . 'my_account':
      echo '<link rel="stylesheet" href="' . HOME_URL . 'assets/styles/account.css">';
      break;
    case HOME_URL . 'list_operations':
      echo '<link rel="stylesheet" href="' . HOME_URL . 'assets/styles/persons.css">';
      break;
    default:
      echo '<link rel="stylesheet" href="' . HOME_URL . 'assets/styles/404.css">';
      break;
  }
  ?>
</head>

<body>