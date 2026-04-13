<?php
session_start();
session_unset();
session_destroy();

// Hapus cookie Remember Me
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

header("Location: login.php");
exit();
?>