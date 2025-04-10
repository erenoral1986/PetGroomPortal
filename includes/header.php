<!DOCTYPE html>
<html lang="tr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Kuaför - <?php echo ucfirst(get_current_page()); ?></title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.replit.com/agent/bootstrap-agent-dark-theme.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="static/css/style.css">
    
    <!-- Özel font yükleniyor -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-black py-3 sticky-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="<?php echo url(''); ?>">
                <span class="pet-brand"><span class="pet-blue">Pet</span><span class="pet-teal">Kuaför</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link px-lg-3 <?php echo get_current_page() === 'home' ? 'active' : ''; ?>" href="<?php echo url('home'); ?>">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-lg-3 <?php echo get_current_page() === 'services' ? 'active' : ''; ?>" href="<?php echo url('services'); ?>">Hizmetlerimiz</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-lg-3 <?php echo get_current_page() === 'salons' ? 'active' : ''; ?>" href="<?php echo url('salons'); ?>">Randevu Al</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-lg-3 <?php echo get_current_page() === 'about' ? 'active' : ''; ?>" href="<?php echo url('about'); ?>">Hakkımızda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-lg-3 <?php echo get_current_page() === 'contact' ? 'active' : ''; ?>" href="<?php echo url('contact'); ?>">İletişim</a>
                    </li>
                    
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?php echo escape($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (has_role('admin')): ?>
                                    <li><a class="dropdown-item" href="<?php echo url('admin_dashboard'); ?>"><i class="fas fa-tachometer-alt me-2"></i>Yönetim Paneli</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php elseif (has_role('salon_owner')): ?>
                                    <li><a class="dropdown-item" href="<?php echo url('admin_dashboard'); ?>"><i class="fas fa-store me-2"></i>Salon Yönetimi</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                
                                <li><a class="dropdown-item" href="<?php echo url('profile'); ?>"><i class="fas fa-user me-2"></i>Profilim</a></li>
                                <li><a class="dropdown-item" href="<?php echo url('bookings'); ?>"><i class="fas fa-calendar-check me-2"></i>Randevularım</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo url('logout'); ?>"><i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                            <a href="<?php echo url('login'); ?>" class="btn btn-outline-light btn-sm px-3 rounded-pill">Giriş Yap</a>
                        </li>
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <a href="<?php echo url('register'); ?>" class="btn bg-pet-blue text-white btn-sm px-3 rounded-pill">Kayıt Ol</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Flash Mesajları Göster -->
    <div class="container mt-3">
        <?php echo get_flash_message(); ?>
    </div>
    
    <!-- Ana İçerik Başlangıcı -->