<?php
require_once '../config.php';
checkAdminAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['position'])) {
    $db = getDB();
    
    $id = (int)$_POST['id'];
    $position = (int)$_POST['position'];
    
    $stmt = $db->prepare("UPDATE offers SET position = ? WHERE id = ?");
    $stmt->execute([$position, $id]);
}

header('Location: /admin/');
exit;