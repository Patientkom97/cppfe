<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $pdo->prepare('DELETE FROM messages WHERE id = ?')->execute([$id]);
        flash('Message supprimé.');
        redirect('messages.php');
    }
    if ($action === 'unread') {
        $pdo->prepare('UPDATE messages SET is_read = 0 WHERE id = ?')->execute([$id]);
        flash('Message marqué comme non lu.');
        redirect('messages.php');
    }
    redirect('messages.php');
}

$viewId = (int)($_GET['id'] ?? 0);
$message = null;
if ($viewId) {
    $s = $pdo->prepare('SELECT * FROM messages WHERE id = ?');
    $s->execute([$viewId]);
    $message = $s->fetch() ?: null;
    if ($message && !$message['is_read']) {
        $pdo->prepare('UPDATE messages SET is_read = 1 WHERE id = ?')->execute([$viewId]);
    }
}
$list = $pdo->query('SELECT id, name, email, subject, is_read, created_at FROM messages ORDER BY created_at DESC')->fetchAll();

$adminTitle = 'Messages';
require __DIR__ . '/_header.php';
?>
<?php if ($message): ?>
  <div class="panel">
    <div class="panel-head">
      <h2><?= e($message['subject']) ?></h2>
      <a class="btn btn-ghost btn-small" href="messages.php">← Retour à la liste</a>
    </div>
    <p class="meta">
      De <strong><?= e($message['name']) ?></strong>
      (<a href="mailto:<?= e($message['email']) ?>?subject=<?= rawurlencode('Re: ' . $message['subject']) ?>"><?= e($message['email']) ?></a>)
      · reçu le <?= e(fr_date($message['created_at'])) ?> à <?= e(date('H:i', strtotime($message['created_at']))) ?>
    </p>
    <div class="msg-body"><?= paragraphs($message['body']) ?></div>
    <div class="actions">
      <a class="btn btn-small" href="mailto:<?= e($message['email']) ?>?subject=<?= rawurlencode('Re: ' . $message['subject']) ?>">Répondre par courriel</a>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$message['id'] ?>"><button class="btn btn-ghost btn-small" name="action" value="unread">Marquer non lu</button></form>
      <form method="post" onsubmit="return confirm('Supprimer définitivement ce message ?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$message['id'] ?>"><button class="btn btn-danger btn-small" name="action" value="delete">Supprimer</button></form>
    </div>
  </div>
<?php endif; ?>

<div class="panel">
  <h2>Boîte de réception (<?= count($list) ?>)</h2>
  <?php if (!$list): ?>
    <p class="empty">Aucun message reçu pour le moment.</p>
  <?php else: ?>
    <div class="table-wrap">
    <table>
      <thead><tr><th>De</th><th>Sujet</th><th>Date</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($list as $m): ?>
        <tr class="<?= $m['is_read'] ? '' : 'unread' ?>">
          <td><a href="messages.php?id=<?= (int)$m['id'] ?>"><?= e($m['name']) ?></a><br><small><?= e($m['email']) ?></small></td>
          <td><?= e($m['subject']) ?></td>
          <td><?= e(fr_date($m['created_at'])) ?></td>
          <td>
            <form method="post" onsubmit="return confirm('Supprimer définitivement ce message ?')">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
              <button class="link-danger" name="action" value="delete">Supprimer</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
