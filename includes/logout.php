<?php
// Kullanıcı oturumunu kapat
session_start();
session_destroy();

// Ana sayfaya yönlendir
header("Location: index.php");
exit;
?>