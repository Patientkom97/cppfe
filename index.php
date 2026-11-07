<?php
// Point d'entrée unique pour Vercel : renvoie chaque URL vers le bon fichier PHP du site.
// (Sur un hébergement PHP classique, ce fichier n'est pas utilisé.)

$root = dirname(__DIR__);
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
if ($path === '/' || $path === '') {
    $path = '/index.php';
}

// Liste blanche : /page.php, /admin/page.php ou /api/page.php ; jamais de fichier commençant par "_"
$ok = preg_match('#^/(?:(?:admin|api)/)?[a-z0-9][a-z0-9_-]*\.php$#i', $path) === 1
      && $path !== '/api/index.php';
$file = $root . $path;

if (!$ok || !is_file($file)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Page introuvable.');
}

$_SERVER['SCRIPT_NAME']     = $path;
$_SERVER['PHP_SELF']        = $path;
$_SERVER['SCRIPT_FILENAME'] = $file;
chdir(dirname($file));
require $file;
