<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

attemptRememberLogin();

function requireLogin(): void
{
    if (!isset($_SESSION['user'])) {
        flash('error', 'Silakan login terlebih dahulu.');
        header('Location: login.php');
        exit;
    }
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}
