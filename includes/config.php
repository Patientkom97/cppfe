<?php
declare(strict_types=1);

date_default_timezone_set('Africa/Kinshasa');

define('ROOT', dirname(__DIR__));
define('DB_PATH', ROOT . '/data/cppf.sqlite');
define('UPLOAD_DIR', ROOT . '/uploads');

// Chemin du site (fonctionne à la racine ou dans un sous-dossier)
$dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$dir = preg_replace('#/(admin|api)$#', '', $dir);
define('BASE_URL', $dir);

// Rubriques acceptées par le formulaire de contact
const SUBJECTS = ['Demande d’aide', 'Partenariat', 'Devenir bénévole', 'Autre'];
