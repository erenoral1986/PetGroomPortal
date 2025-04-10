<?php
// Şehre göre mahalle listesi getirme API

// CORS ayarları (gerekirse ayarlayın)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

// Fonsiyonları içe aktar
require_once '../includes/functions.php';

// Şehir parametresini al
$city = isset($_GET['city']) ? $_GET['city'] : '';

if (empty($city)) {
    // Şehir parametresi yoksa boş dizi döndür
    echo json_encode(['districts' => []]);
    exit;
}

// Şehir için mahalleleri al
$districts = get_districts($city);

// Mahallelerin başına "Tüm Mahalleler" seçeneği ekle
array_unshift($districts, 'Tüm Mahalleler');

// JSON olarak döndür
echo json_encode(['districts' => $districts]);
?>