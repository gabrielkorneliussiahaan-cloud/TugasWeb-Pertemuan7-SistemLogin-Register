<?php
// ====================================================================
// index.php
// Titik masuk: arahkan ke dashboard kalau sudah login, kalau belum
// arahkan ke halaman login.
// ====================================================================

require_once __DIR__ . '/includes/auth.php';

header('Location: ' . (isLoggedIn() ? 'dashboard.php' : 'login.php'));
exit;
