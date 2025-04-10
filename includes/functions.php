<?php
/**
 * Yardımcı fonksiyonlar
 */

// Oturum başlat
session_start();

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/../config/database.php';

/**
 * URL oluştur
 * 
 * @param string $path Yol
 * @return string Tam URL
 */
function url($path = '') {
    $base_url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 
                'https://' : 'http://';
    $base_url .= $_SERVER['HTTP_HOST'];
    
    if (empty($path)) {
        return $base_url;
    }
    
    return $base_url . '/index.php?page=' . $path;
}

/**
 * Yönlendirme yap
 * 
 * @param string $path Yönlendirilecek sayfa
 * @return void
 */
function redirect($path = '') {
    header('Location: ' . url($path));
    exit;
}

/**
 * Kullanıcı giriş yapmış mı kontrol et
 * 
 * @return bool Giriş durumu
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Kullanıcı rolünü kontrol et
 * 
 * @param string $role Kontrol edilecek rol
 * @return bool Rol doğru mu
 */
function has_role($role) {
    return is_logged_in() && $_SESSION['user_role'] === $role;
}

/**
 * Roller için yetki kontrolü
 * 
 * @param array $allowed_roles İzin verilen roller
 * @return bool Yetkili mi
 */
function is_authorized($allowed_roles = []) {
    if (!is_logged_in()) {
        return false;
    }
    
    return in_array($_SESSION['user_role'], $allowed_roles);
}

/**
 * Giriş gerektiren sayfalar için kontrol
 * 
 * @param array $allowed_roles İzin verilen roller (boş ise tüm giriş yapmış kullanıcılara izin verilir)
 * @return void
 */
function require_login($allowed_roles = []) {
    if (!is_logged_in()) {
        // Flash mesajı ayarla
        set_flash_message('error', 'Bu sayfayı görüntülemek için giriş yapmalısınız.');
        redirect('login');
    }
    
    if (!empty($allowed_roles) && !is_authorized($allowed_roles)) {
        // Flash mesajı ayarla
        set_flash_message('error', 'Bu sayfayı görüntülemek için yetkiniz yok.');
        redirect('home');
    }
}

/**
 * Güvenli metin çıktısı için kaçış
 * 
 * @param string $text Kaçış yapılacak metin
 * @return string Güvenli metin
 */
function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Şifreyi hash'le
 * 
 * @param string $password Hash'lenecek şifre
 * @return string Hash'lenmiş şifre
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Şifre doğrulama
 * 
 * @param string $password Kontrol edilecek şifre
 * @param string $hash Hash'lenmiş şifre
 * @return bool Şifre doğru mu
 */
function check_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Flash mesajı ayarla
 * 
 * @param string $type Mesaj tipi (success, error, warning, info)
 * @param string $message Mesaj içeriği
 * @return void
 */
function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Flash mesajını göster ve temizle
 * 
 * @return string HTML içeriği
 */
function get_flash_message() {
    if (!isset($_SESSION['flash_message'])) {
        return '';
    }
    
    $flash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
    
    $type_class = 'alert-info';
    switch ($flash['type']) {
        case 'success':
            $type_class = 'alert-success';
            break;
        case 'error':
            $type_class = 'alert-danger';
            break;
        case 'warning':
            $type_class = 'alert-warning';
            break;
    }
    
    return '<div class="alert ' . $type_class . ' alert-dismissible fade show" role="alert">
                ' . escape($flash['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
            </div>';
}

/**
 * Şehir için mahalleleri getir
 * 
 * @param string $city Şehir adı
 * @return array Mahalle listesi
 */
function get_districts($city) {
    global $pdo;
    
    // Şehir adı güvenlik kontrolü
    $city = trim($city);
    
    // Önbellek anahtarı oluştur
    $cache_key = 'districts_' . strtolower(str_replace(' ', '_', $city));
    
    // Önbellekte varsa oradan döndür
    if (isset($_SESSION[$cache_key])) {
        return $_SESSION[$cache_key];
    }
    
    // Veritabanından mahalleleri getir
    // Bu örnekte sabit veriler kullanılıyor, gerçek uygulamada veritabanından çekilecek
    $istanbul_districts = ['Adalar', 'Bakırköy', 'Beşiktaş', 'Beykoz', 'Beyoğlu', 'Fatih', 'Kadıköy', 'Kartal', 'Maltepe', 'Pendik', 'Sarıyer', 'Şişli', 'Ümraniye', 'Üsküdar', 'Zeytinburnu'];
    $ankara_districts = ['Altındağ', 'Çankaya', 'Etimesgut', 'Keçiören', 'Mamak', 'Sincan', 'Yenimahalle'];
    $izmir_districts = ['Alsancak', 'Bornova', 'Çiğli', 'Karşıyaka', 'Konak'];
    $antalya_districts = ['Aksu', 'Döşemealtı', 'Kepez', 'Konyaaltı', 'Lara', 'Muratpaşa'];
    
    $districts = [];
    
    switch (mb_strtolower($city, 'UTF-8')) {
        case 'istanbul':
            $districts = $istanbul_districts;
            break;
        case 'ankara':
            $districts = $ankara_districts;
            break;
        case 'izmir':
            $districts = $izmir_districts;
            break;
        case 'antalya':
            $districts = $antalya_districts;
            break;
        default:
            // Diğer şehirler için varsayılan mahalleler
            $districts = ['Merkez'];
    }
    
    // Önbelleğe kaydet
    $_SESSION[$cache_key] = $districts;
    
    return $districts;
}

/**
 * Mevcut sayfayı al
 * 
 * @return string Sayfa adı
 */
function get_current_page() {
    return $_GET['page'] ?? 'home';
}

/**
 * Tarihi formatlı göster
 * 
 * @param string $date Tarih
 * @param string $format Format
 * @return string Formatlanmış tarih
 */
function format_date($date, $format = 'd.m.Y') {
    return date($format, strtotime($date));
}

/**
 * Saati formatlı göster
 * 
 * @param string $time Saat
 * @param string $format Format
 * @return string Formatlanmış saat
 */
function format_time($time, $format = 'H:i') {
    return date($format, strtotime($time));
}

/**
 * Para birimini formatlı göster
 * 
 * @param float $amount Tutar
 * @return string Formatlanmış tutar
 */
function format_money($amount) {
    return number_format($amount, 2, ',', '.') . ' ₺';
}

/**
 * Salon bulmak için aranan şehir ve mahalle bilgisini al
 * 
 * @return array Şehir ve mahalle bilgisi
 */
function get_salon_search_params() {
    $location = $_GET['location'] ?? null;
    $district = $_GET['district'] ?? 'all';
    $pet_type = $_GET['pet_type'] ?? 'all';
    
    return [
        'location' => $location,
        'district' => $district,
        'pet_type' => $pet_type
    ];
}

/**
 * Salon listesini getir
 * 
 * @param array $params Arama parametreleri
 * @return array Salon listesi
 */
function get_salons($params) {
    global $pdo;
    
    $location = $params['location'] ?? null;
    $district = $params['district'] ?? 'all';
    $pet_type = $params['pet_type'] ?? 'all';
    
    // Parametreler boşsa veya geçersizse boş dizi döndür
    if (empty($location)) {
        return [];
    }
    
    // Sorgu hazırla
    $sql = "SELECT s.* FROM salons s";
    $params = [];
    
    // Filtreler
    $where_conditions = [];
    
    // Şehir filtresi
    $where_conditions[] = "s.city LIKE :city";
    $params[':city'] = $location;
    
    // Pet tipi filtresi
    if ($pet_type !== 'all') {
        $sql .= " JOIN services srv ON s.id = srv.salon_id";
        $where_conditions[] = "(srv.pet_type = :pet_type OR srv.pet_type = 'both')";
        $params[':pet_type'] = $pet_type;
    }
    
    // Mahalle filtresi
    if ($district !== 'all') {
        $where_conditions[] = "s.address LIKE :district";
        $params[':district'] = "%$district%";
    }
    
    // WHERE koşullarını ekle
    if (!empty($where_conditions)) {
        $sql .= " WHERE " . implode(" AND ", $where_conditions);
    }
    
    // Tekrarlanan sonuçları önlemek için DISTINCT kullan
    $sql = "SELECT DISTINCT s.* FROM (" . $sql . ") AS s";
    
    // Sorguyu çalıştır
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}
?>