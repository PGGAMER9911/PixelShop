<?php
function set_flash(string $type, string $message): void
{
    $_SESSION['flash_' . $type] = $message;
}

function get_flash(string $type): ?string
{
    $key = 'flash_' . $type;
    if (!isset($_SESSION[$key])) {
        return null;
    }
    $message = $_SESSION[$key];
    unset($_SESSION[$key]);
    return $message;
}
