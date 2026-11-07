<?php
require __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

function out(int $code, array $data): never
{
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    out(405, ['ok' => false, 'error' => 'Méthode non autorisée.']);
}
if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
    out(419, ['ok' => false, 'error' => 'Session expirée. Rechargez la page puis réessayez.']);
}
// Champ piège : les robots le remplissent, pas les humains
if (!empty($_POST['website'])) {
    out(200, ['ok' => true]);
}

$name    = trim((string)($_POST['nom'] ?? ''));
$email   = trim((string)($_POST['email'] ?? ''));
$subject = (string)($_POST['sujet'] ?? '');
$body    = trim((string)($_POST['message'] ?? ''));

$errors = [];
if (mb_strlen($name) < 2 || mb_strlen($name) > 120) $errors['nom'] = 'Indiquez votre nom.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 160) $errors['email'] = 'Indiquez une adresse courriel valide.';
if (mb_strlen($body) < 10 || mb_strlen($body) > 4000) $errors['message'] = 'Votre message doit contenir entre 10 et 4000 caractères.';
if (!in_array($subject, SUBJECTS, true)) $subject = 'Autre';
if ($errors) {
    out(422, ['ok' => false, 'errors' => $errors]);
}

// Limite : 5 messages par heure et par adresse IP
$cut = date('Y-m-d H:i:s', time() - 3600);
$count = db()->prepare('SELECT COUNT(*) FROM messages WHERE ip = ? AND created_at > ?');
$count->execute([client_ip(), $cut]);
if ((int)$count->fetchColumn() >= 5) {
    out(429, ['ok' => false, 'error' => 'Trop de messages envoyés. Réessayez plus tard.']);
}

db()->prepare('INSERT INTO messages (name, email, subject, body, ip, created_at) VALUES (?, ?, ?, ?, ?, ?)')
    ->execute([$name, $email, $subject, $body, client_ip(), now()]);

// Notification par courriel (facultative : ignorée si le serveur ne peut pas envoyer)
$to = setting('email');
if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
    $headers = "From: no-reply@" . preg_replace('/[^a-z0-9.\-]/i', '', $_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n"
             . "Reply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
    @mail($to, '=?UTF-8?B?' . base64_encode("[CPPF] $subject") . '?=', "De : $name <$email>\n\n$body", $headers);
}

out(200, ['ok' => true]);
