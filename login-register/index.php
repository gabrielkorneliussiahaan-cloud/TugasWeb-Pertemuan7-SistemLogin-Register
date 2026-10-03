<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user']) || attemptRememberLogin()) {
    header('Location: dashboard.php');
    exit;
}

header('Location: login.php');
exit;
