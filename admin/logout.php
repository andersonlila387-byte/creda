<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset admin session keys
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_email']);

// Destroy session if no other keys remain, or just redirect
header('Location: login.php');
exit;
