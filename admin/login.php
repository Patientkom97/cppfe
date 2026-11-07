<?php
require __DIR__ . '/../includes/bootstrap.php';

if (is_admin()) {
    redirect(BASE_URL . '/admin/index.php');
}

$hasUser = (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn() > 0;
$setup = !$hasUser;   // premier lancement : création du compte administrateur
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($setup) {
        $confirm = (string)($_POST['confirm'] ?? '');
        if (!preg_match('/^[A-Za-z0-9._-]{3,30}$/', $username)) {
            $error = 'Identifiant : 3 à 30 caractères (lettres, chiffres, . _ -).';
        } elseif (strlen($password) < 10) {
            $error = 'Le mot de passe doit contenir au moins 10 caractères.';
        } elseif ($password !== $confirm) {
            $error = 'Les deux mots de passe ne correspondent pas.';
        } else {
            db()->prepare('INSERT INTO users (username, password_hash, created_at) VALUES (?, ?, ?)')
                ->execute([$username, password_hash($password, PASSWORD_DEFAULT), now()]);
            login_user((int)db()->lastInsertId(), $username);
            flash('Compte administrateur créé. Bienvenue !');
            redirect(BASE_URL . '/admin/index.php');
        }
    } elseif (too_many_attempts()) {
        $error = 'Trop de tentatives. Réessayez dans 15 minutes.';
    } else {
        $s = db()->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
        $s->execute([$username]);
        $u = $s->fetch();
        // Vérification même si l'utilisateur n'existe pas, pour garder un temps de réponse constant
        $hash = $u['password_hash'] ?? password_hash('x', PASSWORD_DEFAULT);
        if ($u && password_verify($password, $hash)) {
            clear_attempts();
            login_user((int)$u['id'], $u['username']);
            redirect(BASE_URL . '/admin/index.php');
        }
        record_failed_attempt();
        $error = 'Identifiant ou mot de passe incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Connexion – CPPF Admin</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/admin/css/admin.css">
</head>
<body class="login-page">
  <form class="login-box" method="post" autocomplete="on">
    <h1>CPPF <small>Administration</small></h1>
    <?php if ($setup): ?>
      <p class="hint">Premier lancement : créez le compte administrateur.</p>
    <?php endif; ?>
    <?php if ($error): ?><div class="flash flash-err" role="alert"><?= e($error) ?></div><?php endif; ?>
    <?= csrf_field() ?>

    <label for="username">Identifiant</label>
    <input id="username" name="username" type="text" required autofocus autocomplete="username" value="<?= e($_POST['username'] ?? '') ?>">

    <label for="password">Mot de passe</label>
    <input id="password" name="password" type="password" required autocomplete="<?= $setup ? 'new-password' : 'current-password' ?>">

    <?php if ($setup): ?>
      <label for="confirm">Confirmer le mot de passe</label>
      <input id="confirm" name="confirm" type="password" required autocomplete="new-password">
    <?php endif; ?>

    <button class="btn" type="submit"><?= $setup ? 'Créer le compte' : 'Se connecter' ?></button>
    <p class="back-site"><a href="<?= BASE_URL ?>/">← Retour au site</a></p>
  </form>
</body>
</html>
