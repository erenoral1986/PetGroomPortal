<?php
/**
 * Veritabanı bağlantı ayarları
 * 
 * PHPMyAdmin ile kullanılacak veritabanı bağlantı bilgileri
 */

// Veritabanı bağlantı bilgileri
$host = 'localhost';      // Veritabanı sunucusu
$db_name = 'petkuafor_db'; // Veritabanı adı
$username = 'root';       // Veritabanı kullanıcı adı
$password = '';           // Veritabanı şifresi
$charset = 'utf8mb4';     // Karakter seti

// DSN (Data Source Name) oluştur
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

// PDO seçenekleri ayarla
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // Hataları exception olarak göster
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Varsayılan getirme modu
    PDO::ATTR_EMULATE_PREPARES   => false,                    // Emüle edilmiş prepares yerine gerçek prepares kullan
    PDO::ATTR_PERSISTENT         => true,                     // Kalıcı bağlantı kullan (performans için)
];

try {
    // PDO bağlantısını oluştur
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Hata durumunda göster
    die('Veritabanı bağlantısı başarısız oldu: ' . $e->getMessage());
}

?>