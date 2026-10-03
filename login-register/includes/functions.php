<?php

function loadUsers(): array
{
    if (!file_exists(USERS_FILE)) {
        file_put_contents(USERS_FILE, '[]', LOCK_EX);
    }

    $json = file_get_contents(USERS_FILE);
    $users = json_decode($json, true);

    return is_array($users) ? $users : [];
}

function saveUsers(array $users): bool
{
    return file_put_contents(
        USERS_FILE,
        json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    ) !== false;
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function clean(?string $value): string
{
    return trim((string)$value);
}

function findUserByEmail(string $email): ?array
{
    $email = strtolower(trim($email));

    foreach (loadUsers() as $user) {
        if (strtolower($user['email'] ?? '') === $email) {
            return $user;
        }
    }

    return null;
}

function findUserIndexById(string $id): int
{
    foreach (loadUsers() as $index => $user) {
        if (($user['id'] ?? '') === $id) {
            return $index;
        }
    }

    return -1;
}

function createRememberToken(array &$users, int $index): string
{
    $token = bin2hex(random_bytes(32));
    $users[$index]['remember_token'] = password_hash($token, PASSWORD_DEFAULT);
    $users[$index]['remember_created_at'] = date('Y-m-d H:i:s');
    saveUsers($users);

    setcookie(
        REMEMBER_COOKIE,
        $users[$index]['id'] . ':' . $token,
        [
            'expires' => time() + (REMEMBER_DAYS * 86400),
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );

    return $token;
}

function clearRememberToken(): void
{
    $cookie = $_COOKIE[REMEMBER_COOKIE] ?? '';

    if ($cookie !== '') {
        [$userId] = array_pad(explode(':', $cookie, 2), 2, '');
        $users = loadUsers();
        $index = findUserIndexById($userId);

        if ($index >= 0) {
            unset($users[$index]['remember_token'], $users[$index]['remember_created_at']);
            saveUsers($users);
        }
    }

    setcookie(
        REMEMBER_COOKIE,
        '',
        [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}

function attemptRememberLogin(): bool
{
    if (isset($_SESSION['user']) || empty($_COOKIE[REMEMBER_COOKIE])) {
        return isset($_SESSION['user']);
    }

    [$userId, $token] = array_pad(explode(':', $_COOKIE[REMEMBER_COOKIE], 2), 2, '');

    if ($userId === '' || $token === '') {
        clearRememberToken();
        return false;
    }

    $users = loadUsers();

    foreach ($users as $user) {
        if (($user['id'] ?? '') === $userId && !empty($user['remember_token'])) {
            if (password_verify($token, $user['remember_token'])) {
                session_regenerate_id(true);

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ];

                return true;
            }
        }
    }

    clearRememberToken();
    return false;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
