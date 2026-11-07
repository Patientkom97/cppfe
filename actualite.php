<?php
require __DIR__ . '/includes/bootstrap.php';

$stmt = db()->prepare('SELECT * FROM posts WHERE slug = ? AND published = 1');
$stmt->execute([(string)($_GET['slug'] ?? '')]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Article introuvable – CPPF';
} else {
    $pageTitle = $post['title'] . ' – CPPF';
    $metaDesc = $post['excerpt'] ?: mb_substr(strip_tags($post['body']), 0, 150);
}
require __DIR__ . '/includes/public_header.php';
?>
  <main id="contenu">
    <section class="section">
      <div class="wrap article">
        <?php if (!$post): ?>
          <h1 class="page-title">Article introuvable</h1>
          <p>Cet article n’existe pas ou n’est plus publié.</p>
          <a class="btn btn-ghost" href="<?= BASE_URL ?>/actualites.php">Retour aux actualités</a>
        <?php else: ?>
          <a class="back" href="<?= BASE_URL ?>/actualites.php">← Toutes les actualités</a>
          <h1 class="page-title"><?= e($post['title']) ?></h1>
          <time datetime="<?= e(substr($post['created_at'], 0, 10)) ?>"><?= e(fr_date($post['created_at'])) ?></time>
          <?php if ($post['image']): ?>
            <img class="article-img" src="<?= BASE_URL ?>/uploads/<?= e($post['image']) ?>" alt="">
          <?php endif; ?>
          <div class="article-body"><?= paragraphs($post['body']) ?></div>
        <?php endif; ?>
      </div>
    </section>
  </main>
<?php require __DIR__ . '/includes/public_footer.php'; ?>
