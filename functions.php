<?php
// ====================================================================
// includes/functions.php
// Kumpulan fungsi bantu: baca/tulis users.json, sanitasi, dsb.
// ====================================================================

define('USERS_FILE', __DIR__ . '/../data/users.json');

/**
 * REQUIREMENT: data disimpan di file JSON (users.json)
 * Baca semua user dari file JSON. Kalau file belum ada / kosong /
 * rusak, kembalikan array kosong supaya aplikasi tidak crash.
 */
function readUsers(): array
{
    if (!file_exists(USERS_FILE)) {
        return [];
    }

    $raw = file_get_contents(USERS_FILE);
    $data = json_decode($raw, true);

    return is_array($data) ? $data : [];
}

/**
 * Simpan array user ke users.json. Pakai LOCK_EX supaya aman kalau
 * ada beberapa request yang menulis file secara bersamaan.
 */
function saveUsers(array $users): bool
{
    $json = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(USERS_FILE, $json, LOCK_EX) !== false;
}

/**
 * Cari satu user berdasarkan email (tidak case-sensitive).
 */
function findUserByEmail(string $email, array $users): ?array
{
    foreach ($users as $user) {
        if (strcasecmp($user['email'], $email) === 0) {
            return $user;
        }
    }
    return null;
}

/**
 * Update satu user (dicari berdasarkan email) dengan data baru,
 * lalu simpan ulang seluruh file users.json.
 */
function updateUser(string $email, array $newData): bool
{
    $users = readUsers();
    $updated = false;

    foreach ($users as $index => $user) {
        if (strcasecmp($user['email'], $email) === 0) {
            $users[$index] = array_merge($user, $newData);
            $updated = true;
            break;
        }
    }

    return $updated && saveUsers($users);
}

/**
 * REQUIREMENT: sanitasi input dengan htmlspecialchars()
 */
function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Flash message sederhana lewat session, supaya pesan sukses/error
 * tetap muncul setelah redirect (pola Post/Redirect/Get).
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
