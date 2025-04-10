<?php
/**
 * Pet Kuaför - Ana Giriş Dosyası
 * 
 * Yönlendirme işlevselliğini yönetir ve ilgili sayfa dosyasını yükler
 */

// Yardımcı fonksiyonları yükle
require_once 'includes/functions.php';

// Mevcut sayfayı al
$page = $_GET['page'] ?? 'home';

// Geçerli sayfaları tanımla
$valid_pages = [
    'home', 'services', 'salons', 'salon_detail', 'login', 'register', 'logout', 
    'book_appointment', 'profile', 'bookings', 'about', 'contact',
    'admin_dashboard', 'admin_services', 'admin_availability', 'admin_appointments'
];

// Sayfa parametresi güvenliği
$page = in_array($page, $valid_pages) ? $page : 'home';

// Özel yetki gerektiren sayfalar için kontrol
$admin_pages = ['admin_dashboard', 'admin_services', 'admin_availability', 'admin_appointments'];
$salon_owner_pages = ['admin_dashboard', 'admin_services', 'admin_availability', 'admin_appointments'];
$customer_pages = ['profile', 'bookings'];

// Sayfa yetki kontrolleri
if (in_array($page, $admin_pages) && !has_role('admin')) {
    // Admin yetkisi gerektiren sayfa
    set_flash_message('error', 'Bu sayfayı görüntülemek için yönetici yetkisine sahip olmalısınız.');
    redirect('home');
}

if (in_array($page, $salon_owner_pages) && !has_role('salon_owner') && !has_role('admin')) {
    // Salon sahibi yetkisi gerektiren sayfa
    set_flash_message('error', 'Bu sayfayı görüntülemek için salon sahibi olmalısınız.');
    redirect('home');
}

if (in_array($page, $customer_pages) && !is_logged_in()) {
    // Kullanıcı girişi gerektiren sayfa
    set_flash_message('error', 'Bu sayfayı görüntülemek için giriş yapmalısınız.');
    redirect('login');
}

// Özel sayfalar için işlemler
if ($page === 'logout') {
    // Çıkış işlemi
    session_unset();
    session_destroy();
    
    // Yeni oturum başlat ve çıkış mesajı göster
    session_start();
    set_flash_message('success', 'Başarıyla çıkış yaptınız.');
    redirect('home');
    exit;
}

// Header'ı yükle
include 'includes/header.php';

// Sayfa dosyasını yükle
$page_path = 'pages/' . $page . '.php';

if (file_exists($page_path)) {
    include $page_path;
} else {
    // Sayfa bulunamazsa 404 hatası göster
    include 'pages/404.php';
}

// Footer'ı yükle
include 'includes/footer.php';
?>