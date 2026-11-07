<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();

$errors = [];
$values = [
    'phone'    => setting('phone'),
    'email'    => setting('email'),
    'address'  => setting('address'),
    'facebook' => setting('facebook'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($values as $k => $_) {
        $values[$k] = trim((string)($_POST[$k] ?? ''));
    }
    if ($values['phone'] === '' || mb_strlen($values['phone']) > 40) $errors[] = 'Indiquez un numéro de téléphone valide.';
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Indiquez une adresse courriel valide.';
    if ($values['address'] === '' || mb_strlen($values['address']) > 200) $errors[] = 'Indiquez l’adresse (200 caractères maximum).';
    if ($values['facebook'] !== '' && !preg_match('#^https://#i', $values['facebook'])) $errors[] = 'Le lien Facebook doit commencer par https://';

    if (!$errors) {
        $up = db()->prepare('INSERT INTO settings (name, value) VALUES (?, ?) ON CONFLICT(name) DO UPDATE SET value = excluded.value');
        foreach ($values as $k => $v) {
            $up->execute([$k, $v]);
        }
        flash('Coordonnées mises à jour. Elles sont déjà visibles sur le site.');
        redirect('parametres.php');
    }
}

$adminTitle = 'Coordonnées du site';
require __DIR__ . '/_header.php';
?>
<?php foreach ($errors as $err): ?><div class="flash flash-err" role="alert"><?= e($err) ?></div><?php endforeach; ?>

<form class="panel form" method="post">
  <?= csrf_field() ?>
  <p class="hint">Ces informations s’affichent dans la section « Besoin d’aide » et le pied de page du site. Le courriel reçoit aussi une notification à chaque nouveau message.</p>

  <label for="phone">Téléphone</label>
  <input id="phone" name="phone" type="text" required maxlength="40" value="<?= e($values['phone']) ?>">

  <label for="email">Courriel</label>
  <input id="email" name="email" type="email" required maxlength="160" value="<?= e($values['email']) ?>">

  <label for="address">Adresse</label>
  <input id="address" name="address" type="text" required maxlength="200" value="<?= e($values['address']) ?>">

  <label for="facebook">Page Facebook <small>(facultatif)</small></label>
  <input id="facebook" name="facebook" type="url" maxlength="200" placeholder="https://facebook.com/..." value="<?= e($values['facebook']) ?>">

  <div class="actions"><button class="btn" type="submit">Enregistrer</button></div>
</form>
<?php require __DIR__ . '/_footer.php'; ?>
