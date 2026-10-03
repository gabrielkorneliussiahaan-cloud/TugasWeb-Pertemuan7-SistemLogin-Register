<?php
$pageTitle = $pageTitle ?? 'Tugas Rutin 7';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Login/Register PHP Native dengan JSON">
    <title><?= e($pageTitle) ?> — Tugas Rutin 7</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="bg-grid"></div>
<header class="site-header">
    <div class="brand">
        <span class="brand-mark">07</span>
        <span>TUGAS RUTIN 7</span>
    </div>
    <?php if (isset($_SESSION['user'])): ?>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a class="nav-logout" href="logout.php">Logout</a>
        </nav>
    <?php endif; ?>
</header>
<main class="page">
