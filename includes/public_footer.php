  <footer class="site-footer">
    <div class="wrap foot">
      <div>
        <strong>CPPF</strong>
        <p>Conseil pour la promotion et protection de la femme et l’enfant</p>
        <p class="small">Protéger les droits, défendre la dignité, bâtir l’avenir.</p>
      </div>
      <div>
        <p class="small">© <?= date('Y') ?> CPPF. Tous droits réservés.</p>
        <?php if (setting('facebook') !== ''): ?>
          <p class="small"><a class="foot-link" href="<?= e(setting('facebook')) ?>" rel="noopener" target="_blank">Suivez-nous sur Facebook</a></p>
        <?php endif; ?>
      </div>
    </div>
  </footer>
  <script src="<?= BASE_URL ?>/js/main.js"></script>
</body>
</html>
