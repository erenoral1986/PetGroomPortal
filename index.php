<?php
// Oturum başlatma
session_start();

// Veritabanı bağlantısı
require_once 'config/database.php';

// Helper fonksiyonları
require_once 'includes/functions.php';

// Site başlık
$site_title = "PetKuaför - Evcil Hayvan Bakım ve Kuaför Hizmetleri";

// Sayfa tespiti
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Sayfa içeriğini yükle
ob_start();
switch ($page) {
    case 'home':
        require_once 'pages/home.php';
        break;
    case 'salons':
        require_once 'pages/salons.php';
        break;
    case 'salon_detail':
        require_once 'pages/salon_detail.php';
        break;
    case 'booking':
        require_once 'pages/booking.php';
        break;
    case 'login':
        require_once 'pages/login.php';
        break;
    case 'register':
        require_once 'pages/register.php';
        break;
    case 'profile':
        require_once 'pages/profile.php';
        break;
    case 'bookings':
        require_once 'pages/bookings.php';
        break;
    case 'admin':
        require_once 'admin/dashboard.php';
        break;
    case 'admin_services':
        require_once 'admin/services.php';
        break;
    case 'admin_appointments':
        require_once 'admin/appointments.php';
        break;
    case 'admin_availability':
        require_once 'admin/availability.php';
        break;
    case 'logout':
        require_once 'includes/logout.php';
        break;
    default:
        require_once 'pages/home.php';
        break;
}
$content = ob_get_clean();

// Ana şablonu yükle
require_once 'includes/layout.php';
?>