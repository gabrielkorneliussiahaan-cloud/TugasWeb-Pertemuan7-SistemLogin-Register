<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$oldName = '';
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldName = clean($_POST['name'] ?? '');
    $oldEmail = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($oldName === '') {
        $errors[] = 'Nama wajib diisi.';
    } elseif (strlen($oldName) < 3) {
        $errors[] = 'Nama minimal 3 karakter.';
    } elseif (strlen($oldName) > 80) {
        $errors[] = 'Nama maksimal 80 karakter.';
    }

    if ($oldEmail === '') {
        $errors[] = 'Email wajib diisi.';
    } elseif (!filter_var($oldEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    if ($password === '') {
        $errors[] = 'Password wajib diisi.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }

    if (empty($errors) && findUserByEmail($oldEmail)) {
        $errors[] = 'Email sudah terdaftar.';
    }

    if (empty($errors)) {
        $users = loadUsers();

        $users[] = [
            'id' => bin2hex(random_bytes(16)),
            'name' => e($oldName),
            'email' => strtolower($oldEmail),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if (saveUsers($users)) {
            flash('success', 'Registrasi berhasil. Silakan login dengan akun baru.');
            header('Location: login.php');
            exit;
        }

        $errors[] = 'Data gagal disimpan. Periksa permission folder data.';
    }
}

$pageTitle = 'Register';
require 'includes/header.php';
?>

<section class="auth-shell single">
    <div class="auth-card">
        <div class="eyebrow">✨ CREATE ACCOUNT</div>
        <h1>Buat <span>Akun Baru</span></h1>
        <p class="muted">Lengkapi data di bawah untuk membuat akun.</p>

        <?php if ($errors): ?>
            <div class="alert error">
                <strong>Periksa kembali:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <label for="name">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="<?= e($oldName) ?>"
                   placeholder="Contoh: Gabriel Siahaan" autocomplete="name" required>

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="<?= e($oldEmail) ?>"
                   placeholder="nama@email.com" autocomplete="email" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password"
                   placeholder="Minimal 6 karakter" autocomplete="new-password" required>

            <label for="confirm_password">Konfirmasi Password</label>
            <input id="confirm_password" type="password" name="confirm_password"
                   placeholder="Ulangi password" autocomplete="new-password" required>

            <button class="btn btn-primary" type="submit">Daftar Sekarang →</button>
        </form>

        <p class="switch">Sudah punya akun? <a href="login.php">Login</a></p>
    </div>
</section>

</main>
</body>
</html>
