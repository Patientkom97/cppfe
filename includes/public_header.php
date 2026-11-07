<?php
$home = BASE_URL . '/';
$pageTitle = $pageTitle ?? 'CPPF – Conseil pour la promotion et protection de la femme et l’enfant';
$metaDesc  = $metaDesc  ?? 'CPPF : une femme protégée, un enfant épanoui, une communauté forte. Protection, éducation, autonomisation, justice et plaidoyer pour les droits des femmes et des enfants.';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="<?= e($metaDesc) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
  <a class="skip" href="#contenu">Aller au contenu</a>

  <header class="site-header" id="top">
    <div class="wrap bar">
      <a class="brand" href="<?= $home ?>" aria-label="CPPF, accueil">
        <svg class="mark" viewBox="0 0 48 48" aria-hidden="true">
          <circle cx="19" cy="19" r="13" fill="#C8456B"/>
          <circle cx="32" cy="30" r="9" fill="#E3A32B"/>
          <path d="M8 44c3-9 20-9 24 0" fill="none" stroke="#2E7D62" stroke-width="4" stroke-linecap="round"/>
        </svg>
        <span>CPPF</span>
      </a>
      <button class="menu-btn" aria-expanded="false" aria-controls="nav" aria-label="Ouvrir le menu">Menu</button>
      <nav id="nav" class="nav" aria-label="Navigation principale">
        <a href="<?= $home ?>#apropos">Qui sommes-nous</a>
        <a href="<?= $home ?>#actions">Nos actions</a>
        <a href="<?= BASE_URL ?>/actualites.php">Actualités</a>
        <a href="<?= $home ?>#aide">Besoin d’aide</a>
        <a href="<?= $home ?>#contact" class="btn btn-small">Nous contacter</a>
      </nav>
    </div>
  </header>
