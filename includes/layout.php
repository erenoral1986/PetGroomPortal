<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title><?php echo $site_title; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Flatpickr CSS (for date/time picker) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="static/css/style.css">
    
    <?php if (isset($extra_css)): ?>
    <?php echo $extra_css; ?>
    <?php endif; ?>
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen flex flex-col">
    <!-- Üst Navigasyon -->
    <header>
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-3">
                <div class="border-bottom w-100 position-absolute" style="bottom: 0; border-color: #f3f3f3 !important;"></div>
                <div class="d-flex align-items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0 d-flex align-items-center">
                        <a href="<?php echo url('home'); ?>" class="d-flex align-items-center text-decoration-none">
                            <span class="fs-5 fw-bold"><span class="text-pet-blue">Pet</span><span class="text-pet-teal">Kuaför</span></span>
                        </a>
                    </div>
                    
                    <!-- Ana Menü Linkleri (Desktop) -->
                    <div class="d-none d-md-flex ms-5">
                        <a href="<?php echo url('home'); ?>" class="text-decoration-none me-4 hover-menu <?php echo is_active('home'); ?>" style="color: #333;">Ana Sayfa</a>
                        <a href="<?php echo url('salons'); ?>" class="text-decoration-none me-4 hover-menu <?php echo is_active('salons'); ?>" style="color: #333;">Hizmetlerimiz</a>
                        <a href="<?php echo url('salons'); ?>" class="text-decoration-none me-4 hover-menu" style="color: #333;">Randevu Al</a>
                        <a href="#" class="text-decoration-none me-4 hover-menu" style="color: #333;">Hakkımızda</a>
                        <a href="#" class="text-decoration-none me-4 hover-menu" style="color: #333;">İletişim</a>
                        
                        <style>
                            .hover-menu:hover {
                                color: var(--pet-blue) !important;
                            }
                        </style>
                        <?php if (is_logged_in()): ?>
                            <a href="<?php echo url('bookings'); ?>" class="px-4 py-2 rounded-md text-sm fw-medium text-secondary hover-text-pet-blue me-3 transition <?php echo is_active('bookings') ? 'fw-bold text-pet-blue' : ''; ?>">Randevularım</a>
                            <?php if (is_admin()): ?>
                                <div class="dropdown">
                                    <a class="px-4 py-2 rounded-md text-sm fw-medium text-secondary hover-text-pet-blue dropdown-toggle me-3 transition" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Yönetim
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="<?php echo url('admin'); ?>">Gösterge Paneli</a></li>
                                        <li><a class="dropdown-item" href="<?php echo url('admin_services'); ?>">Hizmetler</a></li>
                                        <li><a class="dropdown-item" href="<?php echo url('admin_appointments'); ?>">Randevular</a></li>
                                        <li><a class="dropdown-item" href="<?php echo url('admin_availability'); ?>">Müsaitlik</a></li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Sağ Taraf - Kullanıcı Menüsü -->
                <div class="d-flex align-items-center">
                    <?php if (is_logged_in()): ?>
                        <div class="dropdown position-relative">
                            <button type="button" class="d-flex text-sm rounded-full" id="user-menu-button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="sr-only">Kullanıcı menüsü</span>
                                <div class="h-8 w-8 rounded-full bg-pet-blue" style="display: flex; align-items: center; justify-content: center; color: white;">
                                    <?php echo substr($_SESSION['username'], 0, 1); ?>
                                </div>
                            </button>
                            
                            <!-- Kullanıcı Menüsü Dropdown -->
                            <div class="dropdown-menu dropdown-menu-end py-1" aria-labelledby="user-menu-button">
                                <div class="px-4 py-2 text-xs text-muted">
                                    Merhaba, <?php echo $_SESSION['username']; ?>
                                </div>
                                
                                <a href="<?php echo url('profile'); ?>" class="dropdown-item px-4 py-2 text-sm">Profilim</a>
                                
                                <?php if (is_admin()): ?>
                                    <a href="<?php echo url('admin'); ?>" class="dropdown-item px-4 py-2 text-sm">Yönetim Paneli</a>
                                <?php endif; ?>
                                
                                <a href="<?php echo url('bookings'); ?>" class="dropdown-item px-4 py-2 text-sm">Randevularım</a>
                                <a href="<?php echo url('logout'); ?>" class="dropdown-item px-4 py-2 text-sm">Çıkış Yap</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo url('login'); ?>" class="text-decoration-none me-3" style="color: #333;">Giriş Yap</a>
                        <a href="<?php echo url('register'); ?>" class="btn btn-sm text-white rounded-0 px-3 bg-pet-blue">Kayıt Ol</a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobil Menü Butonu -->
                <div class="d-flex align-items-center d-md-none">
                    <button type="button" class="d-inline-flex align-items-center justify-content-center p-2 rounded-md text-secondary hover-text-pet-blue hover-bg-light" data-bs-toggle="collapse" data-bs-target="#mobile-menu" aria-controls="mobile-menu" aria-expanded="false" aria-label="Menüyü aç">
                        <span class="sr-only">Menüyü aç</span>
                        <i class="fas fa-bars h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobil Menü -->
        <div class="collapse d-md-none" id="mobile-menu">
            <div class="container px-2 pt-2 pb-3">
                <a href="<?php echo url('home'); ?>" class="d-block px-3 py-2 rounded-md text-base fw-medium text-secondary hover-text-pet-blue <?php echo is_active('home') ? 'fw-bold text-pet-blue' : ''; ?>">Ana Sayfa</a>
                <a href="<?php echo url('salons'); ?>" class="d-block px-3 py-2 rounded-md text-base fw-medium text-secondary hover-text-pet-blue <?php echo is_active('salons') ? 'fw-bold text-pet-blue' : ''; ?>">Hizmetlerimiz</a>
                <a href="<?php echo url('salons'); ?>" class="d-block px-3 py-2 rounded-md text-base fw-medium text-secondary hover-text-pet-blue">Randevu Al</a>
                
                <?php if (is_logged_in()): ?>
                    <div class="border-top border-gray-200 pt-4 pb-3 mt-3">
                        <div class="px-4 d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-pet-teal d-flex align-items-center justify-content-center text-white">
                                    <?php echo substr($_SESSION['username'], 0, 1); ?>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="text-base fw-medium"><?php echo $_SESSION['username']; ?></div>
                            </div>
                        </div>
                        <div class="mt-3 px-2">
                            <a href="<?php echo url('profile'); ?>" class="d-block px-3 py-2 rounded-md text-base fw-medium text-secondary hover-bg-light">Profilim</a>
                            
                            <?php if (is_admin()): ?>
                                <a href="<?php echo url('admin'); ?>" class="d-block px-3 py-2 rounded-md text-base fw-medium text-secondary hover-bg-light">Yönetim Paneli</a>
                            <?php endif; ?>
                            
                            <a href="<?php echo url('bookings'); ?>" class="d-block px-3 py-2 rounded-md text-base fw-medium text-secondary hover-bg-light">Randevularım</a>
                            <a href="<?php echo url('logout'); ?>" class="d-block px-3 py-2 rounded-md text-base fw-medium text-secondary hover-bg-light">Çıkış Yap</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="pt-4 pb-3 border-top border-gray-200 mt-3">
                        <a href="<?php echo url('login'); ?>" class="d-block px-3 py-2 rounded-md text-base fw-medium text-pet-blue hover-bg-light">Giriş Yap</a>
                        <a href="<?php echo url('register'); ?>" class="d-block px-3 py-2 rounded-md text-base fw-medium text-pet-blue hover-bg-light">Kayıt Ol</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <!-- Flash Messages -->
    <div class="container mt-3">
        <?php display_flash_message(); ?>
    </div>
    
    <!-- Main Content -->
    <main class="main-content">
        <?php echo $content; ?>
    </main>
    
    <!-- Section Divider -->
    <div class="section-divider"></div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-5">
        <div class="container">
            <div class="row mb-4">
                <!-- Sol Kolon - Logo ve Açıklama -->
                <div class="col-md-3 mb-4 mb-md-0">
                    <div class="mb-3">
                        <span class="fs-4 fw-bold"><span class="text-white">Pet</span><span class="text-pet-teal">Kuaför</span></span>
                    </div>
                    <p class="text-white-50 small mb-4">
                        Evcil hayvanınız için profesyonel bakım ve kuaför hizmetleri sunan platformumuzda, sevimli dostunuza en iyi hizmeti vermekten gurur duyuyoruz.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-white" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-white" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-white" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Hızlı Linkler -->
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="fs-6 fw-semibold mb-3 text-pet-teal">Hızlı Linkler</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a href="<?php echo url('home'); ?>" class="text-white-50 footer-link">Ana Sayfa</a></li>
                        <li class="mb-2"><a href="<?php echo url('salons'); ?>" class="text-white-50 footer-link">Hizmetlerimiz</a></li>
                        <li class="mb-2"><a href="<?php echo url('salons'); ?>" class="text-white-50 footer-link">Randevu Al</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 footer-link">Hakkımızda</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 footer-link">İletişim</a></li>
                    </ul>
                </div>

                <!-- Hizmetler -->
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="fs-6 fw-semibold mb-3 text-pet-pink">Hizmetlerimiz</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a href="<?php echo url('salons'); ?>" class="text-white-50 footer-link">Temel Bakım Paketi</a></li>
                        <li class="mb-2"><a href="<?php echo url('salons'); ?>" class="text-white-50 footer-link">Premium Bakım</a></li>
                        <li class="mb-2"><a href="<?php echo url('salons'); ?>" class="text-white-50 footer-link">Köpek Kuaförü</a></li>
                        <li class="mb-2"><a href="<?php echo url('salons'); ?>" class="text-white-50 footer-link">Kedi Bakımı</a></li>
                        <li class="mb-2"><a href="<?php echo url('salons'); ?>" class="text-white-50 footer-link">Özel Irklar İçin Bakım</a></li>
                    </ul>
                </div>

                <!-- İletişim Bilgileri -->
                <div class="col-md-3">
                    <h5 class="fs-6 fw-semibold mb-3 text-warning">İletişim</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex">
                            <i class="fas fa-map-marker-alt text-pet-teal me-2 mt-1"></i>
                            <a href="https://maps.google.com/?q=Bahçelievler Mah. Atatürk Cad. No:123, İstanbul, Türkiye" target="_blank" class="text-white-50 footer-link">Bahçelievler Mah. Atatürk Cad. No:123, İstanbul, Türkiye</a>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="fas fa-phone-alt text-pet-teal me-2"></i>
                            <a href="tel:+902121234567" class="text-white-50 footer-link">+90 (212) 123 45 67</a>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="fas fa-envelope text-pet-teal me-2"></i>
                            <a href="mailto:info@petkuafor.com" class="text-white-50 footer-link">info@petkuafor.com</a>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="fas fa-clock text-pet-teal me-2"></i>
                            <span class="text-white-50">Haftaiçi: 09:00 - 18:00<br>Haftasonu: 10:00 - 16:00</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="border-secondary my-4">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="text-white-50 small mb-2 mb-md-0">
                    &copy; <?php echo date('Y'); ?> PetKuaför. Tüm hakları saklıdır.
                </p>
                <div>
                    <a href="#" class="text-white-50 me-3 small">Gizlilik Politikası</a>
                    <a href="#" class="text-white-50 me-3 small">Hizmet Şartları</a>
                    <a href="#" class="text-white-50 small">Çerez Politikası</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/tr.js"></script>
    
    <!-- Custom JS -->
    <script src="static/js/script.js"></script>
    
    <!-- Additional Scripts -->
    <?php if (isset($extra_scripts)): ?>
    <?php echo $extra_scripts; ?>
    <?php endif; ?>
    
    <!-- Location Permission Scripts (only on homepage) -->
    <?php if ($page === 'home'): ?>
    <script src="static/js/location-permission-real.js"></script>
    <script src="static/js/cities.js"></script>
    <?php endif; ?>
</body>
</html>