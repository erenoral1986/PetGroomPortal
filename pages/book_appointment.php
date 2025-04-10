<?php
// Randevu alma sayfası

// Kullanıcı giriş yapmış mı kontrol et
if (!is_logged_in()) {
    // Flash mesajı ayarla
    set_flash_message('warning', 'Randevu almak için giriş yapmalısınız.');
    
    // Giriş sayfasına yönlendir
    redirect('login');
}

// Salon ID'sini al
$salon_id = isset($_GET['salon_id']) ? (int)$_GET['salon_id'] : 0;

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
$stmt = $pdo->prepare("SELECT * FROM services WHERE salon_id = ? ORDER BY name ASC");
$stmt->execute([$salon_id]);
$services = $stmt->fetchAll();

// Form verileri
$service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
$date = isset($_POST['date']) ? $_POST['date'] : '';
$time_slot = isset($_POST['time_slot']) ? $_POST['time_slot'] : '';
$pet_name = isset($_POST['pet_name']) ? $_POST['pet_name'] : '';
$pet_type = isset($_POST['pet_type']) ? $_POST['pet_type'] : '';
$pet_breed = isset($_POST['pet_breed']) ? $_POST['pet_breed'] : '';
$notes = isset($_POST['notes']) ? $_POST['notes'] : '';

// Form gönderildi mi kontrolü
$submitted = false;
$success = false;
$form_errors = [];

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basit doğrulama
    if (empty($service_id) || $service_id <= 0) {
        $form_errors['service_id'] = 'Lütfen bir hizmet seçin';
    }
    
    if (empty($date)) {
        $form_errors['date'] = 'Tarih gereklidir';
    } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
        $form_errors['date'] = 'Geçmiş bir tarih seçemezsiniz';
    }
    
    if (empty($time_slot)) {
        $form_errors['time_slot'] = 'Saat gereklidir';
    }
    
    if (empty($pet_name)) {
        $form_errors['pet_name'] = 'Evcil hayvan adı gereklidir';
    }
    
    if (empty($pet_type)) {
        $form_errors['pet_type'] = 'Evcil hayvan türü gereklidir';
    }
    
    // Hata yoksa randevuyu kaydet
    if (empty($form_errors)) {
        // Gerçek uygulamada burada veritabanına kaydetme işlemi yapılır
        // Seçilen hizmeti bul
        $selected_service = null;
        foreach ($services as $service) {
            if ($service['id'] == $service_id) {
                $selected_service = $service;
                break;
            }
        }
        
        if ($selected_service) {
            // Bitiş saatini hesapla
            $start_time = $time_slot;
            $duration_minutes = $selected_service['duration'];
            $end_time = date('H:i:s', strtotime($start_time . " + $duration_minutes minutes"));
            
            // Randevuyu veritabanına ekle
            $stmt = $pdo->prepare("
                INSERT INTO appointments 
                (user_id, salon_id, service_id, date, start_time, end_time, status, pet_name, pet_type, pet_breed, notes, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, NOW())
            ");
            
            $success = $stmt->execute([
                $_SESSION['user_id'],
                $salon_id,
                $service_id,
                $date,
                $start_time,
                $end_time,
                $pet_name,
                $pet_type,
                $pet_breed,
                $notes
            ]);
            
            // Form başarıyla gönderildi olarak işaretle
            $submitted = true;
            
            // Başarılı mesajı belirle
            if ($success) {
                set_flash_message('success', 'Randevunuz başarıyla oluşturuldu. Onay durumunu "Randevularım" sayfasından takip edebilirsiniz.');
                redirect('bookings');
            } else {
                $form_errors['general'] = 'Randevu oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.';
            }
        }
    }
}

// Bugünün tarihi
$today = date('Y-m-d');
// 3 ay sonrası için maksimum tarih
$max_date = date('Y-m-d', strtotime('+3 months'));

// Seçili hizmet türü
$selected_pet_type = $pet_type ?: 'dog';
?>

<!-- Page Header -->
<div class="py-4 bg-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>" class="text-white">Ana Sayfa</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo url('salons'); ?>" class="text-white">Salonlar</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo url('salon_detail') . '&id=' . $salon_id; ?>" class="text-white"><?php echo escape($salon['name']); ?></a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Randevu Al</li>
                    </ol>
                </nav>
                <h1 class="fw-bold text-white mb-2">Randevu Oluştur</h1>
                <p class="lead text-muted"><?php echo escape($salon['name']); ?> salonundan randevu alın.</p>
            </div>
        </div>
    </div>
</div>

<!-- Booking Section -->
<section class="py-5">
    <div class="container">
        <?php if ($submitted && $success): ?>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center py-5">
                        <div class="display-1 text-pet-blue mb-4">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2 class="mb-3">Randevunuz Oluşturuldu!</h2>
                        <p class="lead mb-4">Randevunuz başarıyla oluşturuldu. Randevu detaylarınız e-posta adresinize gönderildi.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="<?php echo url('bookings'); ?>" class="btn bg-pet-blue text-white px-4 py-2">
                                <i class="fas fa-calendar-check me-2"></i> Randevularım
                            </a>
                            <a href="<?php echo url('salon_detail') . '&id=' . $salon_id; ?>" class="btn btn-outline-secondary px-4 py-2">
                                <i class="fas fa-arrow-left me-2"></i> Salon Sayfasına Dön
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <!-- Salon Bilgileri -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 100;">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3"><?php echo escape($salon['name']); ?></h2>
                            
                            <p class="text-muted small mb-3">
                                <i class="fas fa-map-marker-alt me-1 text-pet-teal"></i> 
                                <?php echo escape($salon['address']); ?>, <?php echo escape($salon['city']); ?>
                            </p>
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-2">
                                    <i class="fas fa-phone-alt text-pet-teal"></i>
                                </div>
                                <div>
                                    <?php echo escape($salon['phone']); ?>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-2">
                                    <i class="fas fa-clock text-pet-teal"></i>
                                </div>
                                <div>
                                    <?php echo format_time($salon['opens_at']); ?> - <?php echo format_time($salon['closes_at']); ?>
                                </div>
                            </div>
                            
                            <hr class="my-3">
                            
                            <div id="bookingSummary" class="mb-3 d-none">
                                <h3 class="h6 fw-bold mb-3">Randevu Özeti</h3>
                                
                                <div class="mb-2">
                                    <div class="small text-muted">Hizmet</div>
                                    <div class="fw-medium" id="summaryService">-</div>
                                </div>
                                
                                <div class="mb-2">
                                    <div class="small text-muted">Tarih</div>
                                    <div class="fw-medium" id="summaryDate">-</div>
                                </div>
                                
                                <div class="mb-2">
                                    <div class="small text-muted">Saat</div>
                                    <div class="fw-medium" id="summaryTime">-</div>
                                </div>
                                
                                <div>
                                    <div class="small text-muted">Ücret</div>
                                    <div class="fw-bold fs-5 text-pet-blue" id="summaryPrice">-</div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info small" role="alert">
                                <i class="fas fa-info-circle me-2"></i> Randevu saatinden en az 4 saat öncesine kadar ücretsiz iptal edebilirsiniz.
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-lg-5">
                            <h2 class="h4 fw-bold mb-4">Randevu Bilgileri</h2>
                            
                            <?php if (isset($form_errors['general'])): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $form_errors['general']; ?>
                                </div>
                            <?php endif; ?>
                            
                            <form id="appointmentForm" method="POST" action="<?php echo url('book_appointment') . '&salon_id=' . $salon_id; ?>" class="booking-form">
                                <!-- 1. Hizmet Seçimi -->
                                <div class="mb-4">
                                    <label class="form-label fw-medium mb-3">Hizmet Seçin <span class="text-danger">*</span></label>
                                    
                                    <div class="mb-3">
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="pet_type_filter" id="pet_type_dog" value="dog" <?php echo $selected_pet_type === 'dog' ? 'checked' : ''; ?>>
                                            <label class="btn btn-outline-secondary" for="pet_type_dog">
                                                <i class="fas fa-dog me-1"></i> Köpek
                                            </label>
                                            
                                            <input type="radio" class="btn-check" name="pet_type_filter" id="pet_type_cat" value="cat" <?php echo $selected_pet_type === 'cat' ? 'checked' : ''; ?>>
                                            <label class="btn btn-outline-secondary" for="pet_type_cat">
                                                <i class="fas fa-cat me-1"></i> Kedi
                                            </label>
                                            
                                            <input type="radio" class="btn-check" name="pet_type_filter" id="pet_type_all" value="all" <?php echo $selected_pet_type === 'all' ? 'checked' : ''; ?>>
                                            <label class="btn btn-outline-secondary" for="pet_type_all">
                                                <i class="fas fa-paw me-1"></i> Tümü
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <select class="form-select <?php echo isset($form_errors['service_id']) ? 'is-invalid' : ''; ?>" id="service_id" name="service_id">
                                        <option value="">Bir hizmet seçin</option>
                                        <?php foreach ($services as $service): ?>
                                            <option 
                                                value="<?php echo $service['id']; ?>" 
                                                data-price="<?php echo $service['price']; ?>" 
                                                data-duration="<?php echo $service['duration']; ?>"
                                                data-pet-type="<?php echo $service['pet_type']; ?>"
                                                <?php echo $service_id == $service['id'] ? 'selected' : ''; ?>
                                            >
                                                <?php echo escape($service['name']); ?> (<?php echo format_money($service['price']); ?>, <?php echo $service['duration']; ?> dk)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($form_errors['service_id'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $form_errors['service_id']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- 2. Tarih ve Saat -->
                                <div class="mb-4">
                                    <label class="form-label fw-medium mb-3">Tarih ve Saat Seçin <span class="text-danger">*</span></label>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="date" class="form-label">Tarih</label>
                                            <input type="date" class="form-control <?php echo isset($form_errors['date']) ? 'is-invalid' : ''; ?>" id="date" name="date" min="<?php echo $today; ?>" max="<?php echo $max_date; ?>" value="<?php echo $date; ?>">
                                            <?php if (isset($form_errors['date'])): ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $form_errors['date']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="time_slot" class="form-label">Saat</label>
                                            <select class="form-select <?php echo isset($form_errors['time_slot']) ? 'is-invalid' : ''; ?>" id="time_slot" name="time_slot">
                                                <option value="">Tarih seçin</option>
                                                <!-- JavaScript ile doldurulacak -->
                                            </select>
                                            <?php if (isset($form_errors['time_slot'])): ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $form_errors['time_slot']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 3. Evcil Hayvan Bilgileri -->
                                <div class="mb-4">
                                    <label class="form-label fw-medium mb-3">Evcil Hayvan Bilgileri <span class="text-danger">*</span></label>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="pet_name" class="form-label">Evcil Hayvan Adı</label>
                                            <input type="text" class="form-control <?php echo isset($form_errors['pet_name']) ? 'is-invalid' : ''; ?>" id="pet_name" name="pet_name" value="<?php echo escape($pet_name); ?>">
                                            <?php if (isset($form_errors['pet_name'])): ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $form_errors['pet_name']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="pet_type" class="form-label">Evcil Hayvan Türü</label>
                                            <select class="form-select <?php echo isset($form_errors['pet_type']) ? 'is-invalid' : ''; ?>" id="pet_type" name="pet_type">
                                                <option value="">Seçin</option>
                                                <option value="dog" <?php echo $pet_type === 'dog' ? 'selected' : ''; ?>>Köpek</option>
                                                <option value="cat" <?php echo $pet_type === 'cat' ? 'selected' : ''; ?>>Kedi</option>
                                                <option value="other" <?php echo $pet_type === 'other' ? 'selected' : ''; ?>>Diğer</option>
                                            </select>
                                            <?php if (isset($form_errors['pet_type'])): ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $form_errors['pet_type']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <label for="pet_breed" class="form-label">Irk / Cins (Opsiyonel)</label>
                                            <input type="text" class="form-control" id="pet_breed" name="pet_breed" value="<?php echo escape($pet_breed); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 4. Ek Notlar -->
                                <div class="mb-4">
                                    <label for="notes" class="form-label fw-medium">Özel İstekler / Notlar (Opsiyonel)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Evcil hayvanınızla ilgili özel durumlar veya istekleriniz varsa belirtebilirsiniz."><?php echo escape($notes); ?></textarea>
                                </div>
                                
                                <!-- Gönder Butonu -->
                                <div>
                                    <button type="submit" class="btn bg-pet-blue text-white px-5 py-2">
                                        <i class="fas fa-calendar-check me-2"></i> Randevu Oluştur
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Bilgilendirme Bölümü -->
<section class="py-5 bg-dark">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <h2 class="h4 fw-bold text-white mb-4">Randevu Politikası</h2>
                
                <div class="card bg-dark border-start border-pet-teal border-4 mb-3 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="text-pet-teal"><i class="fas fa-calendar-alt fa-2x"></i></span>
                            </div>
                            <div>
                                <h3 class="h5 text-white">İptal ve Değişiklik</h3>
                                <p class="text-muted mb-0">Randevunuzu randevu saatinden en az 4 saat öncesine kadar ücretsiz olarak iptal edebilirsiniz. Daha sonraki iptaller için salon politikası geçerlidir.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-dark border-start border-pet-teal border-4 mb-3 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="text-pet-teal"><i class="fas fa-clock fa-2x"></i></span>
                            </div>
                            <div>
                                <h3 class="h5 text-white">Randevu Saati</h3>
                                <p class="text-muted mb-0">Lütfen randevu saatinden 10 dakika önce salonda olun. 15 dakikadan fazla gecikmelerde randevunuz iptal edilebilir.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-dark border-start border-pet-teal border-4 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="text-pet-teal"><i class="fas fa-syringe fa-2x"></i></span>
                            </div>
                            <div>
                                <h3 class="h5 text-white">Aşı Gereksinimleri</h3>
                                <p class="text-muted mb-0">Evcil hayvanınızın güncel aşılarının olması gerekmektedir. Lütfen randevunuza aşı karnesini getirin.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <h2 class="h4 fw-bold text-white mb-4">Sıkça Sorulan Sorular</h2>
                
                <div class="accordion" id="bookingFaq">
                    <div class="accordion-item bg-dark border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button bg-dark text-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">
                                Randevumu nasıl iptal edebilirim?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#bookingFaq">
                            <div class="accordion-body text-muted">
                                "Randevularım" sayfasından randevunuzu görüntüleyebilir ve iptal edebilirsiniz. Ayrıca salon ile doğrudan iletişime geçerek de iptal işleminizi gerçekleştirebilirsiniz.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item bg-dark border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button bg-dark text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Randevu saatinden önce salonda olmam gerekiyor mu?
                            </button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#bookingFaq">
                            <div class="accordion-body text-muted">
                                Evet, randevu saatinden yaklaşık 10 dakika önce salonda olmanız önerilir. Bu, gerekli formların doldurulması ve evcil hayvanınızın ortama alışması için zaman tanır.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item bg-dark border-0">
                        <h3 class="accordion-header">
                            <button class="accordion-button bg-dark text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Randevu sırasında evcil hayvanımın yanında bulunabilir miyim?
                            </button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#bookingFaq">
                            <div class="accordion-body text-muted">
                                Bu salon politikasına göre değişebilir. Bazı salonlar, evcil hayvanın daha rahat olması için sahibinin yanında olmasını tercih ederken, bazıları işlemlerin daha verimli yapılabilmesi için sahiplerin bekleme alanında beklemesini isteyebilir. Salon ile önceden iletişime geçerek bu konuyu netleştirebilirsiniz.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hizmet seçimi değiştiğinde
    const serviceSelect = document.getElementById('service_id');
    const petTypeFilterRadios = document.querySelectorAll('input[name="pet_type_filter"]');
    
    // Pet türü filtresi değiştiğinde
    petTypeFilterRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            filterServices(this.value);
        });
    });
    
    // Sayfa yüklendiğinde seçili pet türüne göre filtreleme yap
    const selectedPetType = document.querySelector('input[name="pet_type_filter"]:checked').value;
    filterServices(selectedPetType);
    
    // Hizmet bilgilerini güncelle
    if (serviceSelect) {
        serviceSelect.addEventListener('change', updateServiceInfo);
        
        // Sayfa yüklendiğinde hizmet bilgilerini güncelle
        updateServiceInfo();
    }
    
    // Tarih seçimi değiştiğinde
    const dateInput = document.getElementById('date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            updateAvailableTimeSlots(this.value);
        });
        
        // Sayfa yüklendiğinde tarih seçiliyse zaman dilimlerini güncelle
        if (dateInput.value) {
            updateAvailableTimeSlots(dateInput.value);
        }
    }
    
    // Zaman dilimi seçimi değiştiğinde
    const timeSelect = document.getElementById('time_slot');
    if (timeSelect) {
        timeSelect.addEventListener('change', updateSummary);
    }
    
    // Hizmetleri filtrele
    function filterServices(petType) {
        const options = serviceSelect.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') return; // Boş seçenek her zaman görünür
            
            const servicePetType = option.getAttribute('data-pet-type');
            
            if (petType === 'all' || petType === servicePetType || servicePetType === 'both') {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
        
        // Eğer seçili hizmet artık görünür değilse, seçimi temizle
        if (serviceSelect.selectedIndex > 0) {
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const selectedPetType = selectedOption.getAttribute('data-pet-type');
            
            if (petType !== 'all' && petType !== selectedPetType && selectedPetType !== 'both') {
                serviceSelect.selectedIndex = 0;
                updateServiceInfo();
            }
        }
    }
    
    // Hizmet bilgilerini güncelle
    function updateServiceInfo() {
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        const summaryDiv = document.getElementById('bookingSummary');
        
        if (selectedOption && selectedOption.value) {
            const price = selectedOption.getAttribute('data-price');
            const duration = selectedOption.getAttribute('data-duration');
            const serviceName = selectedOption.text;
            
            // Özet panelini göster
            summaryDiv.classList.remove('d-none');
            
            // Hizmet bilgilerini güncelle
            document.getElementById('summaryService').textContent = serviceName;
            document.getElementById('summaryPrice').textContent = formatCurrency(price);
            
            // Tarih seçiliyse zaman dilimlerini güncelle
            const dateInput = document.getElementById('date');
            if (dateInput.value) {
                updateAvailableTimeSlots(dateInput.value);
            }
        } else {
            // Özet panelini gizle
            summaryDiv.classList.add('d-none');
        }
        
        updateSummary();
    }
    
    // Mevcut tarihe göre müsait zaman dilimlerini güncelle
    function updateAvailableTimeSlots(selectedDate) {
        // Gerçek uygulamada bu veriler API'den alınır
        // Bu örnek için sabit saatler kullanılıyor
        const timeSelect = document.getElementById('time_slot');
        timeSelect.innerHTML = '';
        
        // Seçilebilecek temsili saatler
        const availableTimes = [
            '09:00:00', '09:30:00', '10:00:00', '10:30:00', '11:00:00', '11:30:00',
            '13:00:00', '13:30:00', '14:00:00', '14:30:00', '15:00:00', '15:30:00',
            '16:00:00', '16:30:00', '17:00:00'
        ];
        
        // Placeholder option
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Saat seçin';
        timeSelect.appendChild(placeholder);
        
        // Gerçek saatleri ekle
        availableTimes.forEach(time => {
            const option = document.createElement('option');
            option.value = time;
            option.textContent = formatTimeDisplay(time);
            timeSelect.appendChild(option);
        });
        
        // Daha önce seçili bir saat varsa tekrar seç
        const previouslySelected = '<?php echo $time_slot; ?>';
        if (previouslySelected) {
            timeSelect.value = previouslySelected;
        }
        
        updateSummary();
    }
    
    // Özet panelini güncelle
    function updateSummary() {
        const dateInput = document.getElementById('date');
        const timeSelect = document.getElementById('time_slot');
        
        if (dateInput.value) {
            const formattedDate = new Date(dateInput.value).toLocaleDateString('tr-TR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            document.getElementById('summaryDate').textContent = formattedDate;
        } else {
            document.getElementById('summaryDate').textContent = '-';
        }
        
        if (timeSelect.value) {
            document.getElementById('summaryTime').textContent = formatTimeDisplay(timeSelect.value);
        } else {
            document.getElementById('summaryTime').textContent = '-';
        }
    }
    
    // Saat formatını düzenle (HH:MM:SS -> HH:MM)
    function formatTimeDisplay(timeString) {
        return timeString.substring(0, 5);
    }
    
    // Para birimini formatla
    function formatCurrency(amount) {
        return new Intl.NumberFormat('tr-TR', {
            style: 'currency',
            currency: 'TRY',
            minimumFractionDigits: 2
        }).format(amount);
    }
});
</script>