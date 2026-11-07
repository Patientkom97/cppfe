<?php
declare(strict_types=1);

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name('cppf_sess');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}

function is_admin(): bool
{
    return !empty($_SESSION['uid']);
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect(BASE_URL . '/admin/login.php');
    }
}

function login_user(int $id, string $username): void
{
    session_regenerate_id(true);
    $_SESSION['uid'] = $id;
    $_SESSION['username'] = $username;
}

function client_ip(): string
{
    return substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
}

/** Bloque après 5 échecs en 15 minutes depuis la même adresse IP. */
function too_many_attempts(): bool
{
    $cut = date('Y-m-d H:i:s', time() - 900);
    $s = db()->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND at > ?');
    $s->execute([client_ip(), $cut]);
    return (int)$s->fetchColumn() >= 5;
}

function record_failed_attempt(): void
{
    db()->prepare('INSERT INTO login_attempts (ip, at) VALUES (?, ?)')->execute([client_ip(), now()]);
}

function clear_attempts(): void
{
    db()->prepare('DELETE FROM login_attempts WHERE ip = ?')->execute([client_ip()]);
}
