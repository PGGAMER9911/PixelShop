<?php
require_once __DIR__ . '/../config.php';

function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Please login first.';
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void
{
    require_login();
    if (($_SESSION['user']['role'] ?? '') !== 'admin') {
        $_SESSION['flash_error'] = 'Access denied.';
        header('Location: dashboard.php');
        exit;
    }
}

function refresh_session_user(): void
{
    if (!is_logged_in()) {
        return;
    }

    $stmt = db()->prepare('SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user']['id']]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user'] = $user;
    }
}
