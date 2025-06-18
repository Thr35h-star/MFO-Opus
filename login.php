<?php
require_once '../config.php';
session_start();

// Обработка выхода
if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    session_destroy();
    header('Location: /admin/');
    exit;
}

// Если это не выход, перенаправляем на главную админки
header('Location: /admin/');
exit;