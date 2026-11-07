<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $s = $pdo->prepare('SELECT image FROM posts WHERE id = ?');
        $s->execute([$id]);
        delete_image($s->fetchColumn() ?: null);
        $pdo->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
        flash('Actualité supprimée.');
    } elseif ($action === 'toggle') {
        $pdo->prepare('UPDATE posts SET published = 1 - published, updated_at = ? WHERE id = ?')->execute([now(), $id]);
        flash('Statut de publication mis à jour.');
    }
    redirect('actualites.php');
}

$posts = $pdo->query('SELECT id, title, published, created_at FROM posts ORDER BY created_at DESC')->fetchAll();

$adminTitle = 'Actualités';
require __DIR__ . '/_header.php';
?>
<div class="panel">
  <div class="panel-head">
    <h2>Toutes les actualités (<?= count($posts) ?>)</h2>
    <a class="btn btn-small" href="actualite-edit.php">Nouvelle actualité</a>
  </div>
  <?php if (!$posts): ?>
    <p class="empty">Aucune actualité. Créez la première : elle apparaîtra sur le site.</p>
  <?php else: ?>
    <div class="table-wrap">
    <table>
      <thead><tr><th>Titre</th><th>Statut</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($posts as $p): ?>
        <tr>
          <td><a href="actualite-edit.php?id=<?= (int)$p['id'] ?>"><?= e($p['title']) ?></a></td>
          <td><span class="tag <?= $p['published'] ? 'tag-ok' : 'tag-draft' ?>"><?= $p['published'] ? 'Publiée' : 'Brouillon' ?></span></td>
          <td><?= e(fr_date($p['created_at'])) ?></td>
          <td class="row-actions">
            <a href="actualite-edit.php?id=<?= (int)$p['id'] ?>">Modifier</a>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><button class="link" name="action" value="toggle"><?= $p['published'] ? 'Dépublier' : 'Publier' ?></button></form>
            <form method="post" onsubmit="return confirm('Supprimer définitivement cette actualité ?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><button class="link-danger" name="action" value="delete">Supprimer</button></form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
