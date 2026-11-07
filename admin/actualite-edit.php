<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();

$pdo = db();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$post = ['title' => '', 'excerpt' => '', 'body' => '', 'image' => null, 'published' => 1];

if ($id) {
    $s = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
    $s->execute([$id]);
    $found = $s->fetch();
    if (!$found) {
        flash('Actualité introuvable.', 'err');
        redirect('actualites.php');
    }
    $post = $found;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $post['title']     = trim((string)($_POST['title'] ?? ''));
    $post['excerpt']   = trim((string)($_POST['excerpt'] ?? ''));
    $post['body']      = trim((string)($_POST['body'] ?? ''));
    $post['published'] = isset($_POST['published']) ? 1 : 0;
    $removeImage       = isset($_POST['remove_image']);

    if (mb_strlen($post['title']) < 3 || mb_strlen($post['title']) > 160) $errors[] = 'Le titre doit contenir entre 3 et 160 caractères.';
    if (mb_strlen($post['excerpt']) > 300) $errors[] = 'Le résumé ne doit pas dépasser 300 caractères.';
    if (mb_strlen($post['body']) < 10) $errors[] = 'Le contenu est trop court.';

    $newImage = null;
    if (!$errors) {
        try {
            $newImage = save_image($_FILES['image'] ?? []);
        } catch (RuntimeException $ex) {
            $errors[] = $ex->getMessage();
        }
    }

    if (!$errors) {
        $oldImage = $post['image'];
        $image = $oldImage;
        if ($newImage) {
            $image = $newImage;
        } elseif ($removeImage) {
            $image = null;
        }
        if ($image !== $oldImage) {
            delete_image($oldImage);
        }
        if ($post['excerpt'] === '') {
            $post['excerpt'] = mb_substr(preg_replace('/\s+/', ' ', $post['body']), 0, 160) . (mb_strlen($post['body']) > 160 ? '…' : '');
        }

        if ($id) {
            $pdo->prepare('UPDATE posts SET title=?, slug=?, excerpt=?, body=?, image=?, published=?, updated_at=? WHERE id=?')
                ->execute([$post['title'], unique_slug($post['title'], $id), $post['excerpt'], $post['body'], $image, $post['published'], now(), $id]);
        } else {
            $pdo->prepare('INSERT INTO posts (title, slug, excerpt, body, image, published, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)')
                ->execute([$post['title'], unique_slug($post['title']), $post['excerpt'], $post['body'], $image, $post['published'], now(), now()]);
        }
        flash($post['published'] ? 'Actualité enregistrée et visible sur le site.' : 'Brouillon enregistré.');
        redirect('actualites.php');
    }
}

$adminTitle = $id ? 'Modifier l’actualité' : 'Nouvelle actualité';
require __DIR__ . '/_header.php';
?>
<?php foreach ($errors as $err): ?><div class="flash flash-err" role="alert"><?= e($err) ?></div><?php endforeach; ?>

<form class="panel form" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int)$id ?>">

  <label for="title">Titre</label>
  <input id="title" name="title" type="text" required maxlength="160" value="<?= e($post['title']) ?>">

  <label for="excerpt">Résumé <small>(affiché sur les cartes ; laissé vide, il est créé automatiquement)</small></label>
  <textarea id="excerpt" name="excerpt" rows="2" maxlength="300"><?= e($post['excerpt']) ?></textarea>

  <label for="body">Contenu <small>(séparez les paragraphes par une ligne vide)</small></label>
  <textarea id="body" name="body" rows="12" required><?= e($post['body']) ?></textarea>

  <label for="image">Image <small>(JPG, PNG ou WebP, 2 Mo maximum)</small></label>
  <?php if ($post['image']): ?>
    <p><img class="thumb" src="<?= BASE_URL ?>/uploads/<?= e($post['image']) ?>" alt="Image actuelle"></p>
    <label class="check"><input type="checkbox" name="remove_image" value="1"> Supprimer l’image actuelle</label>
  <?php endif; ?>
  <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp">

  <label class="check"><input type="checkbox" name="published" value="1" <?= $post['published'] ? 'checked' : '' ?>> Publier sur le site</label>

  <div class="actions">
    <button class="btn" type="submit">Enregistrer</button>
    <a class="btn btn-ghost" href="actualites.php">Annuler</a>
  </div>
</form>
<?php require __DIR__ . '/_footer.php'; ?>
