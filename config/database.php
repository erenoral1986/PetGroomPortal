<?php
// Veritabanı bağlantı bilgileri
$host = 'localhost'; // Veritabanı sunucusu
$db_name = 'petkuafor_db'; // Veritabanı adı
$username = 'root'; // Kullanıcı adı (genellikle localhost'ta root)
$password = ''; // Şifre (genellikle localhost'ta boş)

// PDO kullanarak veritabanı bağlantısı oluştur
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    
    // Hata modunu belirle
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Varsayılan fetch modunu belirle
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Emülasyon modunu kapat
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
} catch(PDOException $e) {
    // Hata mesajını göster
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Admin girişi için hash kontrolü
function check_password($password, $hashed_password) {
    return password_verify($password, $hashed_password);
}

// Yeni şifre hash'leme
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
?>