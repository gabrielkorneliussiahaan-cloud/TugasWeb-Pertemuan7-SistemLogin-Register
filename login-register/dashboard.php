<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireLogin();

$user = $_SESSION['user'];
$pageTitle = 'Dashboard';
require 'includes/header.php';
?>

<section class="dashboard-grid">
    <div class="dashboard-main">
        <div class="eyebrow">📊 DASHBOARD</div>
        <div class="welcome">
            <div>
                <p class="muted">Selamat datang kembali,</p>
                <h1><?= e($user['name']) ?> <span>👋</span></h1>
                <p class="muted"><?= e($user['email']) ?></p>
            </div>
            <div class="avatar"><?= e(strtoupper(substr($user['name'], 0, 1))) ?></div>
        </div>

        <div class="stats">
            <div class="stat-card">
                <span>Status</span>
                <strong class="online">● Aktif</strong>
            </div>
            <div class="stat-card">
                <span>Autentikasi</span>
                <strong>Session</strong>
            </div>
            <div class="stat-card">
                <span>Penyimpanan</span>
                <strong>JSON</strong>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">✓</div>
            <div>
                <h3>Login berhasil</h3>
                <p>Akun kamu terlindungi oleh session PHP. Dashboard ini hanya dapat diakses setelah login.</p>
            </div>
        </div>
    </div>

    <aside class="side-card">
        <div class="eyebrow">AKUN</div>
        <div class="account-row">
            <div class="avatar small"><?= e(strtoupper(substr($user['name'], 0, 1))) ?></div>
            <div>
                <strong><?= e($user['name']) ?></strong>
                <span><?= e($user['email']) ?></span>
            </div>
        </div>

        <a class="side-link" href="profile.php">✎ Edit Profile <span>→</span></a>
        <a class="side-link danger-link" href="logout.php">↪ Logout <span>→</span></a>
    </aside>
</section>

</main>
</body>
</html>
