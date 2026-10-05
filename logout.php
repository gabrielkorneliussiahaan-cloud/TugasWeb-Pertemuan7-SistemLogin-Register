<?php
// ====================================================================
// logout.php
// REQUIREMENT: logout functionality (session_destroy)
// ====================================================================

require_once __DIR__ . '/includes/auth.php';

$emailForCleanup = $_SESSION['user_email'] ?? null;

// Hapus semua data session
$_SESSION = [];
session_destroy();

// Bersihkan juga cookie "remember me" (bonus) supaya tidak auto-login lagi
clearRememberCookie($emailForCleanup);

session_start();
setFlash('success', 'Kamu berhasil logout. Sampai jumpa lagi!');
header('Location: login.php');
exit;
