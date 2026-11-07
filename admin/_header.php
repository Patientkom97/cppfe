<?php
// À inclure après bootstrap.php et require_admin(). Variables : $adminTitle
$unread = (int)db()->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
$cur = basename($_SERVER['SCRIPT_NAME']);
$link = function (string $file, string $label, string $badge = '') use ($cur) {
    $active = ($cur === $file || ($file === 'actualites.php' && $cur === 'actualite-edit.php')) ? ' class="active" aria-current="page"' : '';
    return '<a href="' . BASE_URL . '/admin/' . $file . '"' . $active . '>' . e($label) . $badge . '</a>';
};
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($adminTitle ?? 'Administration') ?> – CPPF Admin</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/admin/css/admin.css">
</head>
<body>
<div class="layout">
  <aside class="side">
    <div class="side-brand">CPPF <small>Administration</small></div>
    <nav aria-label="Menu d’administration">
      <?= $link('index.php', 'Tableau de bord') ?>
      <?= $link('messages.php', 'Messages', $unread ? ' <span class="badge">' . $unread . '</span>' : '') ?>
      <?= $link('actualites.php', 'Actualités') ?>
      <?= $link('parametres.php', 'Coordonnées du site') ?>
      <?= $link('motdepasse.php', 'Mot de passe') ?>
    </nav>
    <div class="side-foot">
      <a href="<?= BASE_URL ?>/" target="_blank" rel="noopener">Voir le site</a>
      <a href="<?= BASE_URL ?>/admin/logout.php">Se déconnecter (<?= e($_SESSION['username'] ?? '') ?>)</a>
    </div>
  </aside>
  <main class="content">
    <h1><?= e($adminTitle ?? 'Administration') ?></h1>
    <?= flash_show() ?>
