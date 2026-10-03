<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireLogin();

$success = '';
$errors = [];
$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name'] ?? '');

    if ($name === '') {
        $errors[] = 'Nama wajib diisi.';
    } elseif (strlen($name) < 3) {
        $errors[] = 'Nama minimal 3 karakter.';
    } elseif (strlen($name) > 80) {
        $errors[] = 'Nama maksimal 80 karakter.';
    }

    if (!$errors) {
        $users = loadUsers();
        $index = findUserIndexById($user['id']);

        if ($index >= 0) {
            $users[$index]['name'] = e($name);

            if (saveUsers($users)) {
                $_SESSION['user']['name'] = e($name);
                $user = $_SESSION['user'];
                $success = 'Profile berhasil diperbarui.';
            } else {
                $errors[] = 'Profile gagal disimpan.';
            }
        }
    }
}

$pageTitle = 'Edit Profile';
require 'includes/header.php';
?>

<section class="auth-shell single">
    <div class="auth-card">
        <div class="eyebrow">👤 PROFILE</div>
        <h1>Edit <span>Profile</span></h1>
        <p class="muted">Perbarui nama akun kamu.</p>

        <?php if ($errors): ?>
            <div class="alert error">
                <?php foreach ($errors as $error): ?>
                    <div><?= e($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert success"><?= e($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Nama</label>
            <input id="name" type="text" name="name" value="<?= e($user['name']) ?>" required>

            <label>Email</label>
            <input type="email" value="<?= e($user['email']) ?>" disabled>

            <button class="btn btn-primary" type="submit">Simpan Perubahan →</button>
        </form>

        <p class="switch"><a href="dashboard.php">← Kembali ke Dashboard</a></p>
    </div>
</section>

</main>
</body>
</html>
