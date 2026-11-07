<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();

$pdo = db();
$stats = [
    'total'  => (int)$pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn(),
    'unread' => (int)$pdo->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn(),
    'pub'    => (int)$pdo->query('SELECT COUNT(*) FROM posts WHERE published = 1')->fetchColumn(),
    'draft'  => (int)$pdo->query('SELECT COUNT(*) FROM posts WHERE published = 0')->fetchColumn(),
];
$recent = $pdo->query('SELECT id, name, subject, is_read, created_at FROM messages ORDER BY created_at DESC LIMIT 5')->fetchAll();

$adminTitle = 'Tableau de bord';
require __DIR__ . '/_header.php';
?>
<div class="stats">
  <a class="stat" href="messages.php"><strong><?= $stats['unread'] ?></strong><span>message<?= $stats['unread'] > 1 ? 's' : '' ?> non lu<?= $stats['unread'] > 1 ? 's' : '' ?></span></a>
  <a class="stat" href="messages.php"><strong><?= $stats['total'] ?></strong><span>message<?= $stats['total'] > 1 ? 's' : '' ?> reçu<?= $stats['total'] > 1 ? 's' : '' ?></span></a>
  <a class="stat" href="actualites.php"><strong><?= $stats['pub'] ?></strong><span>actualité<?= $stats['pub'] > 1 ? 's' : '' ?> publiée<?= $stats['pub'] > 1 ? 's' : '' ?></span></a>
  <a class="stat" href="actualites.php"><strong><?= $stats['draft'] ?></strong><span>brouillon<?= $stats['draft'] > 1 ? 's' : '' ?></span></a>
</div>

<div class="panel">
  <div class="panel-head">
    <h2>Derniers messages</h2>
    <a class="btn btn-small" href="actualite-edit.php">Nouvelle actualité</a>
  </div>
  <?php if (!$recent): ?>
    <p class="empty">Aucun message reçu pour le moment.</p>
  <?php else: ?>
    <table>
      <thead><tr><th>De</th><th>Sujet</th><th>Date</th></tr></thead>
      <tbody>
      <?php foreach ($recent as $m): ?>
        <tr class="<?= $m['is_read'] ? '' : 'unread' ?>">
          <td><a href="messages.php?id=<?= (int)$m['id'] ?>"><?= e($m['name']) ?></a></td>
          <td><?= e($m['subject']) ?></td>
          <td><?= e(fr_date($m['created_at'])) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
