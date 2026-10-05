<?php
// ====================================================================
// includes/auth.php
// REQUIREMENT: proteksi halaman dengan session
// BONUS: "Remember Me" lewat cookie
// File ini di-include di setiap halaman yang butuh status login.
// ====================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

function isLoggedIn(): bool
{
    return isset($_SESSION['user_email']);
}

/**
 * BONUS: Remember Me.
 * Kalau session sudah tidak ada tapi cookie "remember_token" masih
 * valid, otomatis login-kan user tanpa perlu isi form lagi.
 */
function attemptRememberLogin(): void
{
    if (isLoggedIn() || empty($_COOKIE['remember_token'])) {
        return;
    }

    $decoded = json_decode(base64_decode($_COOKIE['remember_token']), true);
    if (!is_array($decoded) || empty($decoded['email']) || empty($decoded['token'])) {
        return;
    }

    $users = readUsers();
    $user = findUserByEmail($decoded['email'], $users);

    if ($user && !empty($user['remember_token']) &&
        password_verify($decoded['token'], $user['remember_token'])
    ) {
        session_regenerate_id(true);
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_nama'] = $user['nama'];
    }
}

attemptRememberLogin();

/**
 * Panggil di paling atas halaman yang wajib login (dashboard, edit
 * profile). Kalau belum login, redirect ke login.php.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Silakan login terlebih dahulu.');
        header('Location: login.php');
        exit;
    }
}

/**
 * Set cookie "remember me" untuk satu user. Token acak disimpan
 * versi hash-nya di users.json (pakai password_hash juga), dan versi
 * asli disimpan di cookie milik browser user.
 */
function setRememberCookie(string $email): void
{
    $token = bin2hex(random_bytes(32));
    $hashedToken = password_hash($token, PASSWORD_DEFAULT);

    updateUser($email, ['remember_token' => $hashedToken]);

    $cookieValue = base64_encode(json_encode([
        'email' => $email,
        'token' => $token,
    ]));

    setcookie('remember_token', $cookieValue, [
        'expires' => time() + (30 * 24 * 60 * 60), // 30 hari
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function clearRememberCookie(?string $email): void
{
    if ($email) {
        updateUser($email, ['remember_token' => null]);
    }
    setcookie('remember_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}
