<?php
// Profil sayfası

// Kullanıcı giriş yapmış mı kontrol et
if (!is_logged_in()) {
    // Flash mesajı ayarla
    set_flash_message('warning', 'Profil sayfasını görüntülemek için giriş yapmalısınız.');
    
    // Giriş sayfasına yönlendir
    redirect('login');
}

// Kullanıcı bilgilerini al
global $pdo;
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Kullanıcı bulunamadıysa hata ver
if (!$user) {
    set_flash_message('error', 'Kullanıcı bilgileri alınamadı.');
    redirect('home');
}

// Form verileri
$first_name = isset($_POST['first_name']) ? $_POST['first_name'] : $user['first_name'];
$last_name = isset($_POST['last_name']) ? $_POST['last_name'] : $user['last_name'];
$email = isset($_POST['email']) ? $_POST['email'] : $user['email'];
$phone = isset($_POST['phone']) ? $_POST['phone'] : $user['phone'];
$current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

// Form gönderildi mi kontrolü
$form_errors = [];

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Hangi form gönderildi
    $form_type = isset($_POST['form_type']) ? $_POST['form_type'] : '';
    
    if ($form_type === 'profile') {
        // Profil güncelleme formu
        
        // Basit doğrulama
        if (empty($first_name)) {
            $form_errors['first_name'] = 'Ad gereklidir';
        }
        
        if (empty($last_name)) {
            $form_errors['last_name'] = 'Soyad gereklidir';
        }
        
        if (empty($email)) {
            $form_errors['email'] = 'E-posta adresi gereklidir';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $form_errors['email'] = 'Geçerli bir e-posta adresi giriniz';
        } elseif ($email !== $user['email']) {
            // Eğer e-posta değiştiyse, benzersiz olup olmadığını kontrol et
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user['id']]);
            if ($stmt->rowCount() > 0) {
                $form_errors['email'] = 'Bu e-posta adresi zaten kullanılıyor';
            }
        }
        
        if (empty($phone)) {
            $form_errors['phone'] = 'Telefon numarası gereklidir';
        }
        
        // Hata yoksa profili güncelle
        if (empty($form_errors)) {
            $stmt = $pdo->prepare("
                UPDATE users 
                SET first_name = ?, last_name = ?, email = ?, phone = ? 
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$first_name, $last_name, $email, $phone, $user['id']]);
            
            if ($result) {
                set_flash_message('success', 'Profil bilgileriniz başarıyla güncellendi.');
                redirect('profile');
            } else {
                set_flash_message('error', 'Profil güncellenirken bir hata oluştu. Lütfen tekrar deneyin.');
                redirect('profile');
            }
        }
    } elseif ($form_type === 'password') {
        // Şifre değiştirme formu
        
        // Basit doğrulama
        if (empty($current_password)) {
            $form_errors['current_password'] = 'Mevcut şifre gereklidir';
        } elseif (!check_password($current_password, $user['password_hash'])) {
            $form_errors['current_password'] = 'Mevcut şifre yanlış';
        }
        
        if (empty($new_password)) {
            $form_errors['new_password'] = 'Yeni şifre gereklidir';
        } elseif (strlen($new_password) < 6) {
            $form_errors['new_password'] = 'Şifre en az 6 karakter olmalıdır';
        }
        
        if ($new_password !== $confirm_password) {
            $form_errors['confirm_password'] = 'Şifreler eşleşmiyor';
        }
        
        // Hata yoksa şifreyi güncelle
        if (empty($form_errors)) {
            $password_hash = hash_password($new_password);
            
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $result = $stmt->execute([$password_hash, $user['id']]);
            
            if ($result) {
                set_flash_message('success', 'Şifreniz başarıyla güncellendi.');
                redirect('profile');
            } else {
                set_flash_message('error', 'Şifre güncellenirken bir hata oluştu. Lütfen tekrar deneyin.');
                redirect('profile');
            }
        }
    }
}

// Randevu sayısını al
$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE user_id = ?");
$stmt->execute([$user['id']]);
$total_appointments = $stmt->fetchColumn();

// Yaklaşan randevu sayısını al
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM appointments 
    WHERE user_id = ? AND date >= CURDATE() AND status IN ('pending', 'confirmed')
");
$stmt->execute([$user['id']]);
$upcoming_appointments = $stmt->fetchColumn();
?>

<!-- Profil Header -->
<div class="profile-header py-5">
    <div class="container text-center text-white">
        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['first_name'] . '+' . $user['last_name']); ?>&background=00BED7&color=fff&size=100" class="profile-avatar mb-3" alt="<?php echo escape($user['username']); ?>">
        <h1 class="fw-bold mb-0"><?php echo escape($user['first_name']); ?> <?php echo escape($user['last_name']); ?></h1>
        <p class="lead text-white-50"><?php echo escape($user['username']); ?></p>
        <div class="d-flex justify-content-center gap-3 mt-3">
            <div class="text-center">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center text-pet-blue mx-auto" style="width: 40px; height: 40px;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <p class="small text-white-50 mt-2 mb-0">
                    <?php echo $total_appointments; ?> Randevu
                </p>
            </div>
            <div class="text-center">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center text-pet-blue mx-auto" style="width: 40px; height: 40px;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <p class="small text-white-50 mt-2 mb-0">
                    <?php echo date('d.m.Y', strtotime($user['date_joined'])); ?>
                </p>
            </div>
            <div class="text-center">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center text-pet-blue mx-auto" style="width: 40px; height: 40px;">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <p class="small text-white-50 mt-2 mb-0">
                    <?php echo $upcoming_appointments; ?> Yaklaşan
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Profil İçeriği -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Sol Kenar Çubuğu -->
            <div class="col-lg-3">
                <div class="list-group mb-4">
                    <a href="#profileInfo" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                        <i class="fas fa-user me-2"></i> Profil Bilgileri
                    </a>
                    <a href="#securitySettings" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-lock me-2"></i> Güvenlik
                    </a>
                    <a href="<?php echo url('bookings'); ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar-check me-2"></i> Randevularım
                        <?php if ($upcoming_appointments > 0): ?>
                            <span class="badge bg-pet-blue rounded-pill float-end"><?php echo $upcoming_appointments; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3 d-flex align-items-center">
                            <i class="fas fa-lightbulb text-warning me-2"></i> 
                            <span>İpuçları</span>
                        </h2>
                        <div class="small">
                            <p class="mb-2">Düzenli olarak şifrenizi değiştirmek hesap güvenliğinizi artırır.</p>
                            <p class="mb-0">Tüm randevularınızı takip etmek için "Randevularım" sayfasını ziyaret edebilirsiniz.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sağ İçerik -->
            <div class="col-lg-9">
                <div class="tab-content">
                    <!-- Profil Bilgileri -->
                    <div class="tab-pane fade show active" id="profileInfo">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4 p-lg-5">
                                <h2 class="h4 fw-bold mb-4">Profil Bilgileri</h2>
                                
                                <form method="POST" action="<?php echo url('profile'); ?>">
                                    <input type="hidden" name="form_type" value="profile">
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="first_name" class="form-label">Ad</label>
                                            <input type="text" class="form-control <?php echo isset($form_errors['first_name']) ? 'is-invalid' : ''; ?>" id="first_name" name="first_name" value="<?php echo escape($first_name); ?>">
                                            <?php if (isset($form_errors['first_name'])): ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $form_errors['first_name']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="last_name" class="form-label">Soyad</label>
                                            <input type="text" class="form-control <?php echo isset($form_errors['last_name']) ? 'is-invalid' : ''; ?>" id="last_name" name="last_name" value="<?php echo escape($last_name); ?>">
                                            <?php if (isset($form_errors['last_name'])): ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $form_errors['last_name']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="username" class="form-label">Kullanıcı Adı</label>
                                        <input type="text" class="form-control" id="username" value="<?php echo escape($user['username']); ?>" disabled>
                                        <div class="form-text small">Kullanıcı adınız değiştirilemez.</div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="email" class="form-label">E-posta Adresi</label>
                                        <input type="email" class="form-control <?php echo isset($form_errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo escape($email); ?>">
                                        <?php if (isset($form_errors['email'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo $form_errors['email']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="phone" class="form-label">Telefon Numarası</label>
                                        <input type="text" class="form-control <?php echo isset($form_errors['phone']) ? 'is-invalid' : ''; ?>" id="phone" name="phone" value="<?php echo escape($phone); ?>">
                                        <?php if (isset($form_errors['phone'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo $form_errors['phone']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="d-flex gap-3">
                                        <button type="submit" class="btn bg-pet-blue text-white px-4">Değişiklikleri Kaydet</button>
                                        <a href="<?php echo url('profile'); ?>" class="btn btn-outline-secondary">İptal</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Güvenlik Ayarları -->
                    <div class="tab-pane fade" id="securitySettings">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4 p-lg-5">
                                <h2 class="h4 fw-bold mb-4">Şifre Değiştir</h2>
                                
                                <form method="POST" action="<?php echo url('profile'); ?>">
                                    <input type="hidden" name="form_type" value="password">
                                    
                                    <div class="mb-4">
                                        <label for="current_password" class="form-label">Mevcut Şifre</label>
                                        <input type="password" class="form-control <?php echo isset($form_errors['current_password']) ? 'is-invalid' : ''; ?>" id="current_password" name="current_password">
                                        <?php if (isset($form_errors['current_password'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo $form_errors['current_password']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="new_password" class="form-label">Yeni Şifre</label>
                                        <input type="password" class="form-control <?php echo isset($form_errors['new_password']) ? 'is-invalid' : ''; ?>" id="new_password" name="new_password">
                                        <?php if (isset($form_errors['new_password'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo $form_errors['new_password']; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="form-text small">Şifreniz en az 6 karakter uzunluğunda olmalıdır.</div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="confirm_password" class="form-label">Yeni Şifre (Tekrar)</label>
                                        <input type="password" class="form-control <?php echo isset($form_errors['confirm_password']) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password">
                                        <?php if (isset($form_errors['confirm_password'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo $form_errors['confirm_password']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="d-flex gap-3">
                                        <button type="submit" class="btn bg-pet-blue text-white px-4">Şifreyi Değiştir</button>
                                        <a href="<?php echo url('profile'); ?>" class="btn btn-outline-secondary">İptal</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card border-0 shadow-sm mt-4">
                            <div class="card-body p-4">
                                <h2 class="h5 fw-bold mb-3">Hesap Güvenliği</h2>
                                
                                <div class="alert alert-info" role="alert">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fas fa-info-circle fa-2x"></i>
                                        </div>
                                        <div>
                                            <h3 class="h6 fw-bold">Hesabınızı Güvende Tutun</h3>
                                            <p class="small mb-0">Güçlü ve benzersiz bir şifre kullanın, şüpheli bir etkinlik fark ederseniz hemen şifrenizi değiştirin. Asla kullanıcı bilgilerinizi başkalarıyla paylaşmayın.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <div class="small text-muted">Son giriş</div>
                                        <div><?php echo date('d.m.Y H:i'); ?></div>
                                    </div>
                                    <a href="#" class="btn btn-outline-secondary btn-sm px-3">
                                        <i class="fas fa-history me-1"></i> Tüm Oturumlar
                                    </a>
                                </div>
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
                <h2 class="fw-bold mb-3">Randevu Oluşturmak İster misiniz?</h2>
                <p class="lead mb-0">Evcil dostunuz için randevu almak için hemen salonları keşfedin.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo url('salons'); ?>" class="btn btn-light text-pet-blue px-4 py-2 rounded-pill fw-bold">
                    <i class="fas fa-search me-2"></i> Salon Ara
                </a>
            </div>
        </div>
    </div>
</section>