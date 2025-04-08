<?php 
// Oturum başlat
session_start();

// Sayfa başlığı tanımlı değilse varsayılan başlık kullan
if(!isset($pageTitle)) {
    $pageTitle = "PetKuaför - Evcil Hayvan Bakım ve Kuaför Hizmetleri";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- CSS dosyaları -->
    <link rel="stylesheet" href="/assets/css/styles.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              'pet-blue': '#1a9cb7',
              'pet-teal': '#30c9c9',
              'pet-pink': '#ff6b8b',
              'pet-orange': '#ff9666',
              'pet-yellow': '#ffd166',
              'pet-green': '#4ecca3',
            }
          }
        }
      }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen flex flex-col">
    <!-- Üst Navigasyon -->
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/" class="flex items-center">
                            <span class="text-2xl font-bold text-pet-blue">Pet<span class="text-pet-teal">Kuaför</span></span>
                        </a>
                    </div>
                    
                    <!-- Ana Menü Linkleri (Masaüstü) -->
                    <div class="hidden md:ml-6 md:flex md:items-center md:space-x-4">
                        <a href="/" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-pet-blue">Ana Sayfa</a>
                        <a href="/pages/services.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-pet-blue">Hizmetlerimiz</a>
                        <a href="/pages/booking.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-pet-blue">Randevu Al</a>
                        <a href="/pages/about.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-pet-blue">Hakkımızda</a>
                        <a href="/pages/contact.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-pet-blue">İletişim</a>
                    </div>
                </div>
                
                <!-- Sağ Taraf - Kullanıcı Menüsü -->
                <div class="flex items-center">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <!-- Kullanıcı Giriş Yapmış -->
                        <div class="ml-3 relative group">
                            <div>
                                <button type="button" class="flex text-sm rounded-full focus:outline-none" id="user-menu-button">
                                    <span class="sr-only">Kullanıcı menüsü</span>
                                    <div class="h-8 w-8 rounded-full bg-pet-teal flex items-center justify-center text-white">
                                        <?php echo substr($_SESSION['username'], 0, 1); ?>
                                    </div>
                                </button>
                            </div>
                            
                            <!-- Kullanıcı Menüsü Dropdown -->
                            <div class="hidden group-hover:block origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu">
                                <div class="px-4 py-2 text-xs text-gray-500">
                                    Merhaba, <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </div>
                                
                                <a href="/pages/user-profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profilim</a>
                                
                                <?php if($_SESSION['role'] === 'groomer'): ?>
                                    <a href="/pages/groomer-dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Kuaför Paneli</a>
                                <?php endif; ?>
                                
                                <?php if($_SESSION['role'] === 'admin'): ?>
                                    <a href="/pages/admin-dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Paneli</a>
                                <?php endif; ?>
                                
                                <a href="/pages/my-appointments.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Randevularım</a>
                                <a href="/api/auth.php?logout=true" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Çıkış Yap</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Kullanıcı Giriş Yapmamış -->
                        <a href="/pages/login.php" class="px-4 py-2 text-sm font-medium text-pet-blue hover:text-pet-teal">Giriş Yap</a>
                        <a href="/pages/register.php" class="ml-2 px-4 py-2 text-sm font-medium text-white bg-pet-blue hover:bg-pet-teal rounded-md">Kayıt Ol</a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobil Menü Butonu -->
                <div class="flex items-center md:hidden">
                    <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-pet-blue" id="mobile-menu-button">
                        <span class="sr-only">Menüyü aç</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobil Menü -->
        <div class="hidden md:hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="/" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-pet-blue">Ana Sayfa</a>
                <a href="/#services" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-pet-blue">Hizmetlerimiz</a>
                <a href="/#booking" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-pet-blue">Randevu Al</a>
                <a href="/pages/about.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-pet-blue">Hakkımızda</a>
                <a href="/pages/contact.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-pet-blue">İletişim</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="border-t border-gray-200 pt-4 pb-3">
                        <div class="px-4 flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-pet-teal flex items-center justify-center text-white">
                                    <?php echo substr($_SESSION['username'], 0, 1); ?>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                            </div>
                        </div>
                        <div class="mt-3 px-2 space-y-1">
                            <a href="/pages/user-profile.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Profilim</a>
                            
                            <?php if($_SESSION['role'] === 'groomer'): ?>
                                <a href="/pages/groomer-dashboard.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Kuaför Paneli</a>
                            <?php endif; ?>
                            
                            <?php if($_SESSION['role'] === 'admin'): ?>
                                <a href="/pages/admin-dashboard.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Admin Paneli</a>
                            <?php endif; ?>
                            
                            <a href="/pages/my-appointments.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Randevularım</a>
                            <a href="/api/auth.php?logout=true" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Çıkış Yap</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="pt-4 pb-3 border-t border-gray-200">
                        <a href="/pages/login.php" class="block px-3 py-2 rounded-md text-base font-medium text-pet-blue hover:bg-gray-100">Giriş Yap</a>
                        <a href="/pages/register.php" class="block px-3 py-2 rounded-md text-base font-medium text-pet-blue hover:bg-gray-100">Kayıt Ol</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Ana İçerik -->
    <main class="flex-grow">