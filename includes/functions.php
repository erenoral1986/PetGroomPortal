<?php
// Genel yardımcı fonksiyonlar

// Kullanıcının giriş yapıp yapmadığını kontrol eder
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Oturum açmış kullanıcının yönetici veya salon sahibi olup olmadığını kontrol eder
function is_admin() {
    return isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'salon_owner');
}

// Girilen metni güvenli hale getirir (XSS saldırılarına karşı)
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// GET isteklerinde kullanmak için URL oluşturma
function url($page, $params = []) {
    $url = "index.php?page=" . urlencode($page);
    
    foreach ($params as $key => $value) {
        $url .= "&" . urlencode($key) . "=" . urlencode($value);
    }
    
    return $url;
}

// Aktif sayfayı kontrol etme
function is_active($page) {
    $current_page = isset($_GET['page']) ? $_GET['page'] : 'home';
    return $current_page === $page ? 'active' : '';
}

// Tarih formatını değiştirme
function format_date($date) {
    $timestamp = strtotime($date);
    return date('d.m.Y', $timestamp);
}

// Saat formatını değiştirme
function format_time($time) {
    return date('H:i', strtotime($time));
}

// Para birimi formatını değiştirme
function format_currency($amount) {
    return number_format($amount, 2, ',', '.') . ' ₺';
}

// Flash mesajları ayarlama
function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Flash mesajları gösterme
function display_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        echo '<div class="alert alert-' . escape($flash['type']) . ' alert-dismissible fade show" role="alert">';
        echo escape($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        
        // Mesajı gösterdikten sonra sil
        unset($_SESSION['flash_message']);
    }
}

// Sayfalar arası yönlendirme
function redirect($page, $params = []) {
    $url = url($page, $params);
    header("Location: $url");
    exit;
}

// En yakın uygun zamanı bulma yardımcı fonksiyonu
function get_available_time_slots($salon_id, $date, $service_id) {
    global $pdo;
    
    // Gün adını bul (0: Pazartesi - 6: Pazar)
    $day_of_week = date('N', strtotime($date)) - 1;
    
    // Salon çalışma saatlerini al
    $stmt = $pdo->prepare("SELECT * FROM availability WHERE salon_id = ? AND day_of_week = ?");
    $stmt->execute([$salon_id, $day_of_week]);
    $availability = $stmt->fetchAll();
    
    if (empty($availability)) {
        return []; // Bu gün için çalışma saati yok
    }
    
    // Hizmet süresini al
    $stmt = $pdo->prepare("SELECT duration FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch();
    $duration = $service['duration']; // dakika cinsinden
    
    // Mevcut randevuları al
    $stmt = $pdo->prepare("SELECT start_time, end_time FROM appointments WHERE salon_id = ? AND date = ?");
    $stmt->execute([$salon_id, $date]);
    $appointments = $stmt->fetchAll();
    
    $time_slots = [];
    
    // Her çalışma saati aralığı için
    foreach ($availability as $avail) {
        $start = strtotime($avail['start_time']);
        $end = strtotime($avail['end_time']);
        
        // 30 dakikalık aralıklarla kontrol et
        for ($time = $start; $time <= ($end - $duration * 60); $time += 30 * 60) {
            $slot_start = date('H:i:s', $time);
            $slot_end = date('H:i:s', $time + $duration * 60);
            
            // Bu zaman dilimi müsait mi kontrol et
            $is_available = true;
            foreach ($appointments as $appointment) {
                $apt_start = strtotime($appointment['start_time']);
                $apt_end = strtotime($appointment['end_time']);
                
                // Çakışma kontrolü
                if (($time >= $apt_start && $time < $apt_end) || 
                    ($time + $duration * 60 > $apt_start && $time + $duration * 60 <= $apt_end) ||
                    ($time <= $apt_start && $time + $duration * 60 >= $apt_end)) {
                    $is_available = false;
                    break;
                }
            }
            
            if ($is_available) {
                $time_slots[] = [
                    'start_time' => $slot_start,
                    'end_time' => $slot_end,
                    'display_time' => date('H:i', $time)
                ];
            }
        }
    }
    
    return $time_slots;
}

// Şehir listesini alma
function get_cities() {
    return [
        'Adana', 'Adıyaman', 'Afyonkarahisar', 'Ağrı', 'Amasya', 'Ankara', 'Antalya', 'Artvin', 'Aydın', 'Balıkesir',
        'Bilecik', 'Bingöl', 'Bitlis', 'Bolu', 'Burdur', 'Bursa', 'Çanakkale', 'Çankırı', 'Çorum', 'Denizli',
        'Diyarbakır', 'Edirne', 'Elazığ', 'Erzincan', 'Erzurum', 'Eskişehir', 'Gaziantep', 'Giresun', 'Gümüşhane', 'Hakkari',
        'Hatay', 'Isparta', 'Mersin', 'İstanbul', 'İzmir', 'Kars', 'Kastamonu', 'Kayseri', 'Kırklareli', 'Kırşehir',
        'Kocaeli', 'Konya', 'Kütahya', 'Malatya', 'Manisa', 'Kahramanmaraş', 'Mardin', 'Muğla', 'Muş', 'Nevşehir',
        'Niğde', 'Ordu', 'Rize', 'Sakarya', 'Samsun', 'Siirt', 'Sinop', 'Sivas', 'Tekirdağ', 'Tokat',
        'Trabzon', 'Tunceli', 'Şanlıurfa', 'Uşak', 'Van', 'Yozgat', 'Zonguldak', 'Aksaray', 'Bayburt', 'Karaman',
        'Kırıkkale', 'Batman', 'Şırnak', 'Bartın', 'Ardahan', 'Iğdır', 'Yalova', 'Karabük', 'Kilis', 'Osmaniye',
        'Düzce'
    ];
}

// Şehre göre mahalleler
function get_districts($city) {
    $neighborhoods = [
        'İstanbul' => [
            'Acıbadem', 'Adatepe', 'Alemdağ', 'Alibeyköy', 'Altunizade', 'Ambarlı', 'Anadoluhisarı', 'Arnavutköy', 
            'Atakent', 'Ataköy', 'Atalar', 'Ataşehir', 'Ayazağa', 'Ayazma', 'Aydınlı', 'Bahçeköy', 'Bahçelievler', 
            'Bahçeşehir', 'Balat', 'Balmumcu', 'Basınköy', 'Batı Ataşehir', 'Bağlarbaşı', 'Başakşehir', 
            'Bebek', 'Beyazıtağa', 'Beykoz', 'Beylerbeyi', 'Beyoğlu', 'Bostancı'
        ],
        'Ankara' => [
            'Akyurt', 'Altındağ', 'Ayaş', 'Bala', 'Beypazarı', 'Çankaya', 'Çubuk', 'Elmadağ', 'Etimesgut', 
            'Gölbaşı', 'Güdül', 'Haymana', 'Kalecik', 'Kazan', 'Keçiören', 'Kızılcahamam', 'Mamak', 'Nallıhan', 
            'Polatlı', 'Pursaklar', 'Sincan', 'Şereflikoçhisar', 'Yenimahalle'
        ],
        'İzmir' => [
            'Aliağa', 'Balçova', 'Bayındır', 'Bayraklı', 'Bergama', 'Beydağ', 'Bornova', 'Buca', 'Çeşme', 
            'Çiğli', 'Dikili', 'Foça', 'Gaziemir', 'Güzelbahçe', 'Karabağlar', 'Karaburun', 'Karşıyaka', 
            'Kemalpaşa', 'Kınık', 'Kiraz', 'Konak', 'Menderes', 'Menemen', 'Narlıdere', 'Ödemiş'
        ],
        'Antalya' => [
            'Aksu', 'Alanya', 'Altınkum', 'Arapsuyu', 'Aspendos', 'Bahçelievler', 'Belek', 'Boğaçay', 
            'Demre', 'Deniz', 'Dokuma', 'Duraliler', 'Düden', 'Elmali', 'Etiler', 'Fabrikalar', 'Fener', 
            'Gazipaşa', 'Göksu', 'Güllük', 'Güzeloba', 'Hurma', 'Kaleiçi', 'Kaş', 'Kemer', 'Kepez', 'Konakli', 
            'Konyaaltı', 'Kundu', 'Kuzdere', 'Lara', 'Liman', 'Manavgat', 'Meltem'
        ]
    ];
    
    $city = trim($city);
    
    if (isset($neighborhoods[$city])) {
        $districts = $neighborhoods[$city];
        sort($districts); // Alfabetik sırala
        return $districts;
    }
    
    return []; // Şehir bulunamadıysa boş dizi döndür
}

?>