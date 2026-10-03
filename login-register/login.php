<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

attemptRememberLogin();

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$oldEmail = '';
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldEmail = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (!filter_var($oldEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif ($password === '') {
        $error = 'Password wajib diisi.';
    } else {
        $users = loadUsers();
        $index = -1;
        $user = null;

        foreach ($users as $i => $candidate) {
            if (strtolower($candidate['email'] ?? '') === strtolower($oldEmail)) {
                $index = $i;
                $user = $candidate;
                break;
            }
        }

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ];

            if ($remember) {
                createRememberToken($users, $index);
            } else {
                clearRememberToken();
            }

            header('Location: dashboard.php');
            exit;
        }

        $error = 'Email atau password salah.';
    }
}

$pageTitle = 'Login';
require 'includes/header.php';
?>

<section class="auth-shell">
    <div class="auth-card">
        <div class="eyebrow">🔐 AUTHENTICATION</div>
        <h1>Selamat Datang <span>Kembali</span></h1>
        <p class="muted">Masuk ke akun kamu untuk membuka dashboard.</p>

        <?php if ($flash): ?>
            <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="<?= e($oldEmail) ?>"
                   placeholder="nama@email.com" autocomplete="email" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password"
                   placeholder="Masukkan password" autocomplete="current-password" required>

            <label class="check-row">
                <input type="checkbox" name="remember">
                <span>Remember Me <small>— login tetap tersimpan hingga 30 hari</small></span>
            </label>

            <button class="btn btn-primary" type="submit">Login →</button>
        </form>

        <p class="switch">Belum punya akun? <a href="register.php">Buat akun</a></p>
    </div>

    <div class="feature-panel">
        <div class="feature-number">07</div>
        <h2>PHP Native<br><span>+ JSON Storage</span></h2>
        <p>Sistem autentikasi sederhana tanpa database MySQL, sesuai requirement Tugas Rutin 7.</p>
        <div class="feature-list">
            <div>✓ Password Hash</div>
            <div>✓ Session Security</div>
            <div>✓ Input Sanitization</div>
            <div>✓ Remember Me</div>
        </div>
    </div>
</section>

</main>
</body>
</html>
