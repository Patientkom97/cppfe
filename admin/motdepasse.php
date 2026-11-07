<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $current = (string)($_POST['current'] ?? '');
    $new     = (string)($_POST['new'] ?? '');
    $confirm = (string)($_POST['confirm'] ?? '');

    $s = db()->prepare('SELECT password_hash FROM users WHERE id = ?');
    $s->execute([$_SESSION['uid']]);
    $hash = (string)$s->fetchColumn();

    if (!password_verify($current, $hash)) $errors[] = 'Le mot de passe actuel est incorrect.';
    if (strlen($new) < 10) $errors[] = 'Le nouveau mot de passe doit contenir au moins 10 caractères.';
    if ($new !== $confirm) $errors[] = 'Les deux nouveaux mots de passe ne correspondent pas.';

    if (!$errors) {
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['uid']]);
        session_regenerate_id(true);
        flash('Mot de passe modifié.');
        redirect('motdepasse.php');
    }
}

$adminTitle = 'Mot de passe';
require __DIR__ . '/_header.php';
?>
<?php foreach ($errors as $err): ?><div class="flash flash-err" role="alert"><?= e($err) ?></div><?php endforeach; ?>

<form class="panel form" method="post" autocomplete="off">
  <?= csrf_field() ?>
  <label for="current">Mot de passe actuel</label>
  <input id="current" name="current" type="password" required autocomplete="current-password">

  <label for="new">Nouveau mot de passe <small>(10 caractères minimum)</small></label>
  <input id="new" name="new" type="password" required autocomplete="new-password">

  <label for="confirm">Confirmer le nouveau mot de passe</label>
  <input id="confirm" name="confirm" type="password" required autocomplete="new-password">

  <div class="actions"><button class="btn" type="submit">Changer le mot de passe</button></div>
</form>
<?php require __DIR__ . '/_footer.php'; ?>
