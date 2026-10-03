<?php
session_start();

define('BASE_URL', '/login-register/');
define('USERS_FILE', __DIR__ . '/../data/users.json');
define('REMEMBER_COOKIE', 'remember_token');
define('REMEMBER_DAYS', 30);
