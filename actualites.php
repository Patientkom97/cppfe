<?php
require __DIR__ . '/includes/bootstrap.php';

$posts = db()->query('SELECT title, slug, excerpt, image, created_at FROM posts WHERE published = 1 ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Actualités – CPPF';
$metaDesc = 'Les actualités du Conseil pour la promotion et protection de la femme et l’enfant.';
require __DIR__ . '/includes/public_header.php';
?>
  <main id="contenu">
    <section class="section">
      <div class="wrap">
        <h1 class="page-title">Actualités</h1>
        <?php if (!$posts): ?>
          <p>Aucune actualité publiée pour le moment.</p>
        <?php else: ?>
          <div class="news-grid">
            <?php foreach ($posts as $p): ?>
              <article class="news-card">
                <?php if ($p['image']): ?>
                  <a href="<?= BASE_URL ?>/actualite.php?slug=<?= e($p['slug']) ?>"><img src="<?= BASE_URL ?>/uploads/<?= e($p['image']) ?>" alt="" loading="lazy"></a>
                <?php endif; ?>
                <div class="news-body">
                  <time datetime="<?= e(substr($p['created_at'], 0, 10)) ?>"><?= e(fr_date($p['created_at'])) ?></time>
                  <h3><a href="<?= BASE_URL ?>/actualite.php?slug=<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
                  <p><?= e($p['excerpt']) ?></p>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </main>
<?php require __DIR__ . '/includes/public_footer.php'; ?>
