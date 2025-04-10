<?php
// Randevularım sayfası

// Kullanıcı giriş yapmış mı kontrol et
if (!is_logged_in()) {
    // Flash mesajı ayarla
    set_flash_message('warning', 'Randevularınızı görüntülemek için giriş yapmalısınız.');
    
    // Giriş sayfasına yönlendir
    redirect('login');
}

// Aktif sekme
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'upcoming';

// Geçerli sekme kontrolü
if (!in_array($active_tab, ['upcoming', 'past', 'cancelled'])) {
    $active_tab = 'upcoming';
}

// Kullanıcının randevularını al
global $pdo;

// Yaklaşan randevular
$stmt = $pdo->prepare("
    SELECT a.*, s.name as salon_name, s.address as salon_address, s.city as salon_city, 
           sv.name as service_name, sv.price as service_price
    FROM appointments a
    JOIN salons s ON a.salon_id = s.id
    JOIN services sv ON a.service_id = sv.id
    WHERE a.user_id = ? AND a.date >= CURDATE() AND a.status IN ('pending', 'confirmed')
    ORDER BY a.date ASC, a.start_time ASC
");
$stmt->execute([$_SESSION['user_id']]);
$upcoming_appointments = $stmt->fetchAll();

// Geçmiş randevular
$stmt = $pdo->prepare("
    SELECT a.*, s.name as salon_name, s.address as salon_address, s.city as salon_city, 
           sv.name as service_name, sv.price as service_price
    FROM appointments a
    JOIN salons s ON a.salon_id = s.id
    JOIN services sv ON a.service_id = sv.id
    WHERE a.user_id = ? AND (a.date < CURDATE() OR a.status = 'completed')
    ORDER BY a.date DESC, a.start_time DESC
");
$stmt->execute([$_SESSION['user_id']]);
$past_appointments = $stmt->fetchAll();

// İptal edilen randevular
$stmt = $pdo->prepare("
    SELECT a.*, s.name as salon_name, s.address as salon_address, s.city as salon_city, 
           sv.name as service_name, sv.price as service_price
    FROM appointments a
    JOIN salons s ON a.salon_id = s.id
    JOIN services sv ON a.service_id = sv.id
    WHERE a.user_id = ? AND a.status = 'cancelled'
    ORDER BY a.date DESC, a.start_time DESC
");
$stmt->execute([$_SESSION['user_id']]);
$cancelled_appointments = $stmt->fetchAll();

// Duruma göre renk ve ikon belirleme fonksiyonu
function get_status_badge($status) {
    switch ($status) {
        case 'pending':
            return '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Onay Bekliyor</span>';
        case 'confirmed':
            return '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Onaylandı</span>';
        case 'cancelled':
            return '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> İptal Edildi</span>';
        case 'completed':
            return '<span class="badge bg-info"><i class="fas fa-check-double me-1"></i> Tamamlandı</span>';
        default:
            return '<span class="badge bg-secondary"><i class="fas fa-question-circle me-1"></i> Belirsiz</span>';
    }
}

// Randevu iptal işlemi
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $appointment_id = (int)$_GET['cancel'];
    
    // Randevuyu kullanıcının mı ve iptal edilebilir mi kontrol et
    $stmt = $pdo->prepare("
        SELECT * FROM appointments 
        WHERE id = ? AND user_id = ? AND date >= CURDATE() AND status IN ('pending', 'confirmed')
    ");
    $stmt->execute([$appointment_id, $_SESSION['user_id']]);
    $appointment = $stmt->fetch();
    
    if ($appointment) {
        // Randevuyu iptal et
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
        $result = $stmt->execute([$appointment_id]);
        
        if ($result) {
            set_flash_message('success', 'Randevunuz başarıyla iptal edildi.');
        } else {
            set_flash_message('error', 'Randevu iptal edilirken bir hata oluştu. Lütfen tekrar deneyin.');
        }
        
        redirect('bookings');
    } else {
        set_flash_message('error', 'Geçersiz randevu veya bu randevu iptal edilemez.');
        redirect('bookings');
    }
}
?>

<!-- Profil Header -->
<div class="profile-header py-5">
    <div class="container text-center text-white">
        <h1 class="fw-bold mb-0">Randevularım</h1>
        <p class="lead text-white-50">Tüm randevularınızı görüntüleyin ve yönetin</p>
    </div>
</div>

<!-- Randevularım Bölümü -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Sekmeler -->
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active_tab === 'upcoming' ? 'active bg-pet-blue text-white' : ''; ?>" href="<?php echo url('bookings') . '&tab=upcoming'; ?>">
                            <i class="fas fa-calendar-alt me-2"></i> Yaklaşan Randevular
                            <?php if (count($upcoming_appointments) > 0): ?>
                                <span class="badge rounded-pill bg-pet-teal ms-1"><?php echo count($upcoming_appointments); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active_tab === 'past' ? 'active bg-pet-blue text-white' : ''; ?>" href="<?php echo url('bookings') . '&tab=past'; ?>">
                            <i class="fas fa-history me-2"></i> Geçmiş Randevular
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active_tab === 'cancelled' ? 'active bg-pet-blue text-white' : ''; ?>" href="<?php echo url('bookings') . '&tab=cancelled'; ?>">
                            <i class="fas fa-ban me-2"></i> İptal Edilen Randevular
                        </a>
                    </li>
                </ul>
                
                <!-- Randevu Listesi -->
                <div class="tab-content">
                    <!-- Yaklaşan Randevular -->
                    <div class="tab-pane fade <?php echo $active_tab === 'upcoming' ? 'show active' : ''; ?>">
                        <?php if (empty($upcoming_appointments)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                                <h3>Yaklaşan Randevunuz Bulunmuyor</h3>
                                <p class="text-muted mb-4">Henüz randevu oluşturmadınız veya tüm randevularınız tamamlandı.</p>
                                <a href="<?php echo url('salons'); ?>" class="btn bg-pet-blue text-white px-4 py-2">
                                    <i class="fas fa-calendar-plus me-2"></i> Randevu Oluştur
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="row g-4">
                                <?php foreach ($upcoming_appointments as $appointment): ?>
                                    <div class="col-lg-6">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h2 class="h5 fw-bold mb-0"><?php echo escape($appointment['salon_name']); ?></h2>
                                                    <?php echo get_status_badge($appointment['status']); ?>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <p class="text-muted small mb-1">
                                                        <i class="fas fa-map-marker-alt me-1 text-pet-teal"></i> 
                                                        <?php echo escape($appointment['salon_address'] . ', ' . $appointment['salon_city']); ?>
                                                    </p>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between mb-3">
                                                    <div>
                                                        <p class="mb-0 fw-medium"><?php echo escape($appointment['service_name']); ?></p>
                                                        <p class="small text-muted mb-0">
                                                            <i class="fas fa-paw me-1"></i> <?php echo escape($appointment['pet_name']); ?> 
                                                            (<?php echo $appointment['pet_type'] === 'dog' ? 'Köpek' : ($appointment['pet_type'] === 'cat' ? 'Kedi' : 'Diğer'); ?>)
                                                        </p>
                                                    </div>
                                                    <div class="text-end">
                                                        <p class="fw-bold text-pet-blue mb-0"><?php echo format_money($appointment['service_price']); ?></p>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between mb-3">
                                                    <div>
                                                        <div class="small text-muted">Tarih</div>
                                                        <div><?php echo format_date($appointment['date']); ?></div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="small text-muted">Saat</div>
                                                        <div><?php echo format_time($appointment['start_time']); ?> - <?php echo format_time($appointment['end_time']); ?></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between mt-3">
                                                    <a href="<?php echo url('salon_detail') . '&id=' . $appointment['salon_id']; ?>" class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-info-circle me-1"></i> Salon Detayları
                                                    </a>
                                                    <a href="<?php echo url('bookings') . '&cancel=' . $appointment['id']; ?>" class="btn btn-outline-danger btn-sm" data-confirm="Bu randevuyu iptal etmek istediğinize emin misiniz?">
                                                        <i class="fas fa-times me-1"></i> İptal Et
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Geçmiş Randevular -->
                    <div class="tab-pane fade <?php echo $active_tab === 'past' ? 'show active' : ''; ?>">
                        <?php if (empty($past_appointments)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h3>Geçmiş Randevunuz Bulunmuyor</h3>
                                <p class="text-muted mb-4">Henüz tamamlanmış bir randevunuz bulunmuyor.</p>
                                <a href="<?php echo url('salons'); ?>" class="btn bg-pet-blue text-white px-4 py-2">
                                    <i class="fas fa-calendar-plus me-2"></i> Randevu Oluştur
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="row g-4">
                                <?php foreach ($past_appointments as $appointment): ?>
                                    <div class="col-lg-6">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h2 class="h5 fw-bold mb-0"><?php echo escape($appointment['salon_name']); ?></h2>
                                                    <?php echo get_status_badge($appointment['status']); ?>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <p class="text-muted small mb-1">
                                                        <i class="fas fa-map-marker-alt me-1 text-pet-teal"></i> 
                                                        <?php echo escape($appointment['salon_address'] . ', ' . $appointment['salon_city']); ?>
                                                    </p>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between mb-3">
                                                    <div>
                                                        <p class="mb-0 fw-medium"><?php echo escape($appointment['service_name']); ?></p>
                                                        <p class="small text-muted mb-0">
                                                            <i class="fas fa-paw me-1"></i> <?php echo escape($appointment['pet_name']); ?> 
                                                            (<?php echo $appointment['pet_type'] === 'dog' ? 'Köpek' : ($appointment['pet_type'] === 'cat' ? 'Kedi' : 'Diğer'); ?>)
                                                        </p>
                                                    </div>
                                                    <div class="text-end">
                                                        <p class="fw-bold text-pet-blue mb-0"><?php echo format_money($appointment['service_price']); ?></p>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between mb-3">
                                                    <div>
                                                        <div class="small text-muted">Tarih</div>
                                                        <div><?php echo format_date($appointment['date']); ?></div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="small text-muted">Saat</div>
                                                        <div><?php echo format_time($appointment['start_time']); ?> - <?php echo format_time($appointment['end_time']); ?></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-end mt-3">
                                                    <a href="<?php echo url('salon_detail') . '&id=' . $appointment['salon_id']; ?>" class="btn btn-outline-secondary btn-sm me-2">
                                                        <i class="fas fa-info-circle me-1"></i> Salon Detayları
                                                    </a>
                                                    <a href="#" class="btn bg-pet-blue text-white btn-sm">
                                                        <i class="fas fa-star me-1"></i> Değerlendir
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- İptal Edilen Randevular -->
                    <div class="tab-pane fade <?php echo $active_tab === 'cancelled' ? 'show active' : ''; ?>">
                        <?php if (empty($cancelled_appointments)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-ban fa-3x text-muted mb-3"></i>
                                <h3>İptal Edilmiş Randevunuz Bulunmuyor</h3>
                                <p class="text-muted mb-4">Herhangi bir randevunuz iptal edilmemiş.</p>
                                <a href="<?php echo url('salons'); ?>" class="btn bg-pet-blue text-white px-4 py-2">
                                    <i class="fas fa-calendar-plus me-2"></i> Randevu Oluştur
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="row g-4">
                                <?php foreach ($cancelled_appointments as $appointment): ?>
                                    <div class="col-lg-6">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h2 class="h5 fw-bold mb-0"><?php echo escape($appointment['salon_name']); ?></h2>
                                                    <?php echo get_status_badge($appointment['status']); ?>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <p class="text-muted small mb-1">
                                                        <i class="fas fa-map-marker-alt me-1 text-pet-teal"></i> 
                                                        <?php echo escape($appointment['salon_address'] . ', ' . $appointment['salon_city']); ?>
                                                    </p>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between mb-3">
                                                    <div>
                                                        <p class="mb-0 fw-medium"><?php echo escape($appointment['service_name']); ?></p>
                                                        <p class="small text-muted mb-0">
                                                            <i class="fas fa-paw me-1"></i> <?php echo escape($appointment['pet_name']); ?> 
                                                            (<?php echo $appointment['pet_type'] === 'dog' ? 'Köpek' : ($appointment['pet_type'] === 'cat' ? 'Kedi' : 'Diğer'); ?>)
                                                        </p>
                                                    </div>
                                                    <div class="text-end">
                                                        <p class="fw-bold text-pet-blue mb-0"><?php echo format_money($appointment['service_price']); ?></p>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between mb-3">
                                                    <div>
                                                        <div class="small text-muted">Tarih</div>
                                                        <div><?php echo format_date($appointment['date']); ?></div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="small text-muted">Saat</div>
                                                        <div><?php echo format_time($appointment['start_time']); ?> - <?php echo format_time($appointment['end_time']); ?></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-end mt-3">
                                                    <a href="<?php echo url('salon_detail') . '&id=' . $appointment['salon_id']; ?>" class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-info-circle me-1"></i> Salon Detayları
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- İpuçları ve Bilgilendirme -->
<section class="py-5 bg-dark">
    <div class="container">
        <h2 class="h4 fw-bold text-white mb-4">Randevu İpuçları</h2>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card bg-dark border-start border-pet-teal border-4 h-100 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="text-pet-teal"><i class="fas fa-calendar-check fa-2x"></i></span>
                            </div>
                            <div>
                                <h3 class="h5 text-white">Randevu Öncesi</h3>
                                <p class="text-muted mb-0">Randevunuzdan önce evcil hayvanınızı yıkamak zorunda değilsiniz. Salon profesyonelleri tüm bakım ihtiyaçlarını karşılayacaktır.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card bg-dark border-start border-pet-teal border-4 h-100 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="text-pet-teal"><i class="fas fa-suitcase-medical fa-2x"></i></span>
                            </div>
                            <div>
                                <h3 class="h5 text-white">Sağlık Gereksinimi</h3>
                                <p class="text-muted mb-0">Evcil hayvanınızın aşılarının güncel olduğundan emin olun. Salon, güvenlik amacıyla aşı belgeleri talep edebilir.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card bg-dark border-start border-pet-teal border-4 h-100 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="text-pet-teal"><i class="fas fa-clock fa-2x"></i></span>
                            </div>
                            <div>
                                <h3 class="h5 text-white">Randevu Saati</h3>
                                <p class="text-muted mb-0">Randevunuza zamanında gelmeye özen gösterin. 15 dakikadan fazla gecikmelerde randevunuzun yeniden planlanması gerekebilir.</p>
                            </div>
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
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h2 class="fw-bold mb-3">Yeni Bir Randevu Oluşturmak İster misiniz?</h2>
                <p class="lead mb-0">Evcil dostunuz için en yakın pet kuaför salonlarından hemen randevu alın.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo url('salons'); ?>" class="btn btn-light text-pet-blue px-4 py-2 rounded-pill fw-bold">
                    <i class="fas fa-search me-2"></i> Salon Ara
                </a>
            </div>
        </div>
    </div>
</section>