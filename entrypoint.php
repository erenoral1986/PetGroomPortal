<?php
/**
 * Bu dosya, PHP projesi için temel başlatıcı ve hata gösterici bir dosyadır.
 * Sunucu hatalarını görmek ve debug etmek için kullanılır.
 */

// Hata göstermeyi aktifleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Oturum başlat
session_start();

// index.php'ye yönlendir
require_once 'index.php';
?>