<?php
// Salon detay sayfası

// Salon ID'sini al
$salon_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Salon ID geçerli değilse ana sayfaya yönlendir
if ($salon_id <= 0) {
    redirect('home');
}

// Salon bilgilerini al
global $pdo;
$stmt = $pdo->prepare("SELECT * FROM salons WHERE id = ?");
$stmt->execute([$salon_id]);
$salon = $stmt->fetch();

// Salon bulunamadıysa 404 sayfasına yönlendir
if (!$salon) {
    include 'pages/404.php';
    exit;
}

// Salon hizmetlerini al
$stmt = $pdo->prepare("SELECT * FROM services WHERE salon_id = ? ORDER BY price ASC");
$stmt->execute([$salon_id]);
$services = $stmt->fetchAll();

// Hizmetleri kategorilere ayır
$service_categories = [
    'dog' => [],
    'cat' => [],
    'both' => []
];

foreach ($services as $service) {
    $service_categories[$service['pet_type']][] = $service;
}

// Aktif kategori
$active_category = isset($_GET['category']) && array_key_exists($_GET['category'], $service_categories) 
                 ? $_GET['category'] 
                 : 'both';
                 
// Salon değerlendirmeleri (örnek veri)
$reviews = [
    [
        'user_name' => 'Mehmet K.',
        'rating' => 5,
        'date' => '2025-03-15',
        'comment' => 'Köpeğimiz Paşa\'ya gösterilen ilgi ve bakım mükemmeldi. Çok nazik ve profesyonel bir ekip. Kesinlikle tekrar geleceğiz!',
        'pet_name' => 'Paşa',
        'pet_type' => 'Köpek'
    ],
    [
        'user_name' => 'Ayşe Y.',
        'rating' => 4,
        'date' => '2025-03-10',
        'comment' => 'Kedim Minnoş\'a verilen bakım için teşekkürler. Çok mutlu görünüyor. Tek eksik, randevu saatinde biraz beklememiz oldu.',
        'pet_name' => 'Minnoş',
        'pet_type' => 'Kedi'
    ],
    [
        'user_name' => 'Ali D.',
        'rating' => 5,
        'date' => '2025-03-05',
        'comment' => 'Harika bir deneyimdi! Köpeğimiz Luna tıraş sonrası çok güzel oldu ve çalışanlar çok ilgiliydi. Fiyat-performans olarak mükemmel.',
        'pet_name' => 'Luna',
        'pet_type' => 'Köpek'
    ]
];

// Çalışma saatleri (örnek veri)
$work_hours = [
    'Pazartesi' => '09:00 - 18:00',
    'Salı' => '09:00 - 18:00',
    'Çarşamba' => '09:00 - 18:00',
    'Perşembe' => '09:00 - 18:00',
    'Cuma' => '09:00 - 18:00',
    'Cumartesi' => '10:00 - 16:00',
    'Pazar' => 'Kapalı'
];

// Ortalama puan hesapla
$avg_rating = array_reduce($reviews, function($carry, $item) {
    return $carry + $item['rating'];
}, 0) / count($reviews);
?>

<!-- Salon Detay Header -->
<div class="salon-detail-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>" class="text-white">Ana Sayfa</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo url('salons'); ?>" class="text-white">Salonlar</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page"><?php echo escape($salon['name']); ?></li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3"><?php echo escape($salon['name']); ?></h1>
                <p class="lead text-white-50 mb-4"><?php echo escape($salon['description']); ?></p>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="me-3">
                        <span class="badge bg-pet-teal fs-6 px-3 py-2">
                            <i class="fas fa-star me-1"></i> <?php echo number_format($avg_rating, 1); ?>
                        </span>
                    </div>
                    <p class="text-white mb-0"><?php echo count($reviews); ?> değerlendirme</p>
                </div>
                
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?php echo url('book_appointment') . '&salon_id=' . $salon_id; ?>" class="btn bg-pet-blue text-white px-4 py-2 rounded-pill fw-bold">
                        <i class="fas fa-calendar-plus me-2"></i> Randevu Al
                    </a>
                    <a href="#services" class="btn btn-outline-light px-4 py-2 rounded-pill">
                        <i class="fas fa-list-alt me-2"></i> Hizmetler
                    </a>
                    <a href="#reviews" class="btn btn-outline-light px-4 py-2 rounded-pill">
                        <i class="fas fa-star me-2"></i> Değerlendirmeler
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Salon Bilgileri -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">İletişim Bilgileri</h2>
                        
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex mb-3">
                                <i class="fas fa-map-marker-alt text-pet-blue mt-1 me-3"></i>
                                <div>
                                    <strong class="d-block mb-1">Adres</strong>
                                    <p class="text-muted mb-0"><?php echo escape($salon['address']); ?>, <?php echo escape($salon['city']); ?> <?php echo escape($salon['zip_code']); ?></p>
                                </div>
                            </li>
                            <li class="d-flex mb-3">
                                <i class="fas fa-phone-alt text-pet-blue mt-1 me-3"></i>
                                <div>
                                    <strong class="d-block mb-1">Telefon</strong>
                                    <p class="text-muted mb-0"><?php echo escape($salon['phone']); ?></p>
                                </div>
                            </li>
                            <li class="d-flex mb-3">
                                <i class="fas fa-envelope text-pet-blue mt-1 me-3"></i>
                                <div>
                                    <strong class="d-block mb-1">E-posta</strong>
                                    <p class="text-muted mb-0"><?php echo escape($salon['email']); ?></p>
                                </div>
                            </li>
                            <li class="d-flex">
                                <i class="fas fa-clock text-pet-blue mt-1 me-3"></i>
                                <div>
                                    <strong class="d-block mb-1">Çalışma Saatleri</strong>
                                    <p class="text-muted mb-0">
                                        <?php echo format_time($salon['opens_at']); ?> - <?php echo format_time($salon['closes_at']); ?>
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">Çalışma Günleri</h2>
                        
                        <ul class="list-group list-group-flush">
                            <?php foreach ($work_hours as $day => $hours): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><?php echo $day; ?></span>
                                    <span class="badge bg-dark"><?php echo $hours; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">Konum</h2>
                        
                        <div class="bg-dark rounded mb-3" style="height: 200px;">
                            <!-- Harita görüntüsü buraya eklenecek -->
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                <div class="text-center p-3">
                                    <i class="fas fa-map-marked-alt fa-2x text-pet-teal mb-2"></i>
                                    <p class="text-white small mb-0">Harita görüntüsü</p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="https://maps.google.com/?q=<?php echo urlencode($salon['address'] . ', ' . $salon['city']); ?>" target="_blank" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-directions me-2"></i> Yol Tarifi Al
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <!-- Salon Hizmetleri -->
                <div id="services" class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold mb-4">Hizmetlerimiz</h2>
                        
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $active_category === 'both' ? 'active bg-pet-blue text-white' : ''; ?>" href="<?php echo url('salon_detail') . '&id=' . $salon_id . '&category=both#services'; ?>">
                                    <i class="fas fa-paw me-1"></i> Tüm Hizmetler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $active_category === 'dog' ? 'active bg-pet-blue text-white' : ''; ?>" href="<?php echo url('salon_detail') . '&id=' . $salon_id . '&category=dog#services'; ?>">
                                    <i class="fas fa-dog me-1"></i> Köpekler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $active_category === 'cat' ? 'active bg-pet-blue text-white' : ''; ?>" href="<?php echo url('salon_detail') . '&id=' . $salon_id . '&category=cat#services'; ?>">
                                    <i class="fas fa-cat me-1"></i> Kediler
                                </a>
                            </li>
                        </ul>
                        
                        <?php if ($active_category === 'both'): ?>
                            <!-- Tüm Hizmetler -->
                            <div class="accordion" id="servicesAccordion">
                                <?php if (!empty($service_categories['dog'])): ?>
                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#dogServices">
                                            <i class="fas fa-dog me-2"></i> Köpek Bakım Hizmetleri
                                        </button>
                                    </h3>
                                    <div id="dogServices" class="accordion-collapse collapse show" data-bs-parent="#servicesAccordion">
                                        <div class="accordion-body">
                                            <div class="list-group list-group-flush">
                                                <?php foreach ($service_categories['dog'] as $service): ?>
                                                    <div class="list-group-item px-0">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h4 class="h6 fw-bold mb-1"><?php echo escape($service['name']); ?></h4>
                                                                <p class="text-muted small mb-0"><?php echo escape($service['description']); ?></p>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="fw-bold mb-1 text-pet-blue"><?php echo format_money($service['price']); ?></div>
                                                                <div class="small text-muted"><?php echo $service['duration']; ?> dk</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($service_categories['cat'])): ?>
                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#catServices">
                                            <i class="fas fa-cat me-2"></i> Kedi Bakım Hizmetleri
                                        </button>
                                    </h3>
                                    <div id="catServices" class="accordion-collapse collapse" data-bs-parent="#servicesAccordion">
                                        <div class="accordion-body">
                                            <div class="list-group list-group-flush">
                                                <?php foreach ($service_categories['cat'] as $service): ?>
                                                    <div class="list-group-item px-0">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h4 class="h6 fw-bold mb-1"><?php echo escape($service['name']); ?></h4>
                                                                <p class="text-muted small mb-0"><?php echo escape($service['description']); ?></p>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="fw-bold mb-1 text-pet-blue"><?php echo format_money($service['price']); ?></div>
                                                                <div class="small text-muted"><?php echo $service['duration']; ?> dk</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($service_categories['both'])): ?>
                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#bothServices">
                                            <i class="fas fa-paw me-2"></i> Ortak Bakım Hizmetleri
                                        </button>
                                    </h3>
                                    <div id="bothServices" class="accordion-collapse collapse" data-bs-parent="#servicesAccordion">
                                        <div class="accordion-body">
                                            <div class="list-group list-group-flush">
                                                <?php foreach ($service_categories['both'] as $service): ?>
                                                    <div class="list-group-item px-0">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h4 class="h6 fw-bold mb-1"><?php echo escape($service['name']); ?></h4>
                                                                <p class="text-muted small mb-0"><?php echo escape($service['description']); ?></p>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="fw-bold mb-1 text-pet-blue"><?php echo format_money($service['price']); ?></div>
                                                                <div class="small text-muted"><?php echo $service['duration']; ?> dk</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <!-- Filtrelenmiş Hizmetler -->
                            <div class="list-group list-group-flush">
                                <?php if (empty($service_categories[$active_category])): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                        <p class="mb-0">Bu kategoride henüz hizmet bulunmuyor.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($service_categories[$active_category] as $service): ?>
                                        <div class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="h6 fw-bold mb-1"><?php echo escape($service['name']); ?></h4>
                                                    <p class="text-muted small mb-0"><?php echo escape($service['description']); ?></p>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold mb-1 text-pet-blue"><?php echo format_money($service['price']); ?></div>
                                                    <div class="small text-muted"><?php echo $service['duration']; ?> dk</div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-center mt-4">
                            <a href="<?php echo url('book_appointment') . '&salon_id=' . $salon_id; ?>" class="btn bg-pet-blue text-white px-4">
                                <i class="fas fa-calendar-plus me-2"></i> Randevu Al
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Müşteri Değerlendirmeleri -->
                <div id="reviews" class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold mb-4">Müşteri Değerlendirmeleri</h2>
                        
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-3">
                                <span class="display-6 fw-bold"><?php echo number_format($avg_rating, 1); ?></span>
                            </div>
                            <div>
                                <div class="star-rating mb-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $avg_rating): ?>
                                            <i class="fas fa-star"></i>
                                        <?php elseif ($i - 0.5 <= $avg_rating): ?>
                                            <i class="fas fa-star-half-alt"></i>
                                        <?php else: ?>
                                            <i class="far fa-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <p class="small text-muted mb-0"><?php echo count($reviews); ?> değerlendirme</p>
                            </div>
                        </div>
                        
                        <div class="list-group list-group-flush">
                            <?php foreach ($reviews as $review): ?>
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h3 class="h6 fw-bold mb-0"><?php echo escape($review['user_name']); ?></h3>
                                        <span class="small text-muted"><?php echo format_date($review['date']); ?></span>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <div class="star-rating me-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $review['rating']): ?>
                                                    <i class="fas fa-star"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-star text-muted"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="small text-muted">
                                            <?php echo escape($review['pet_name']); ?> (<?php echo escape($review['pet_type']); ?>)
                                        </div>
                                    </div>
                                    <p class="mb-0"><?php echo escape($review['comment']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Galeri -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold mb-4">Galeri</h2>
                        
                        <div class="row g-3">
                            <?php for ($i = 1; $i <= 6; $i++): ?>
                                <div class="col-md-4 col-6">
                                    <div class="ratio ratio-1x1">
                                        <img src="https://source.unsplash.com/random/300x300/?pet,grooming&sig=<?php echo $salon_id . $i; ?>" class="img-fluid rounded" alt="Salon Görseli">
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-pet-blue text-white">
    <div class="container">
        <div class="row align-items-center justify-content-between">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h2 class="fw-bold mb-3"><?php echo escape($salon['name']); ?> ile Randevunuzu Hemen Oluşturun</h2>
                <p class="lead mb-0">Evcil dostunuz için profesyonel bakım hizmetlerimizden hemen yararlanın.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo url('book_appointment') . '&salon_id=' . $salon_id; ?>" class="btn btn-light text-pet-blue px-4 py-2 rounded-pill fw-bold">
                    <i class="fas fa-calendar-plus me-2"></i> Randevu Al
                </a>
            </div>
        </div>
    </div>
</section>