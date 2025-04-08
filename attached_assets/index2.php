<?php
// Başlık ve diğer meta bilgileri
$pageTitle = "PetKuaför - Evcil Hayvan Bakım ve Kuaför Hizmetleri";
include_once 'includes/header.php';

// Kullanıcı giriş yapmış mı kontrol et
$loggedIn = isset($_SESSION['user_id']);
?>

<!-- Hero Bölümü -->
<section class="relative bg-gradient-to-b from-pet-blue/5 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold text-pet-blue leading-tight mb-4">
                    Evcil Hayvanınız İçin <span class="text-pet-teal">Profesyonel Bakım</span> Hizmetleri
                </h1>
                <p class="text-lg text-gray-600 mb-8">
                    PetKuaför ile evcil hayvanınıza özel profesyonel bakım hizmetleri alın. Kuaför seçin, randevu oluşturun ve sevimli dostunuza en iyi bakımı sağlayın.
                </p>
                
                <div class="flex flex-wrap gap-4 mb-8">
                    <a href="./pages/booking.php" class="px-6 py-3 bg-pet-blue text-white font-bold rounded-md hover:bg-pet-teal transition-all">
                        Kuaför Bul
                    </a>
                    <a href="./pages/services.php" class="px-6 py-3 border-2 border-pet-blue text-pet-blue font-bold rounded-md hover:bg-pet-blue hover:text-white transition-all">
                        Hizmetleri İncele
                    </a>
                </div>
                
                <div class="flex flex-wrap items-center gap-8">
                    <div class="flex items-center">
                        <div class="flex -space-x-2">
                            <img src="assets/images/avatar-1.jpg" alt="Kullanıcı" class="w-10 h-10 rounded-full border-2 border-white">
                            <img src="assets/images/avatar-2.jpg" alt="Kullanıcı" class="w-10 h-10 rounded-full border-2 border-white">
                            <img src="assets/images/avatar-3.jpg" alt="Kullanıcı" class="w-10 h-10 rounded-full border-2 border-white">
                            <img src="assets/images/avatar-4.jpg" alt="Kullanıcı" class="w-10 h-10 rounded-full border-2 border-white">
                        </div>
                        <span class="ml-2 text-gray-600 text-sm">2000+ mutlu müşteri</span>
                    </div>
                    
                    <div class="flex items-center text-pet-yellow">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <span class="ml-2 text-gray-600 text-sm">4.8/5 ortalama puan</span>
                    </div>
                </div>
            </div>
            
            <div class="relative">
                <img src="assets/images/hero-image.jpg" alt="Evcil Hayvan Bakımı" class="rounded-lg shadow-xl">
                
                <!-- İlk bilgi kartı -->
                <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-lg shadow-lg hidden md:block">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-pet-teal rounded-full flex items-center justify-center text-white mr-4">
                            <i class="fas fa-check text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Güvenilir Kuaförler</p>
                            <p class="font-semibold">Onaylı ve Profesyonel</p>
                        </div>
                    </div>
                </div>
                
                <!-- İkinci bilgi kartı - SAĞ ÜST -->
                <div class="absolute -top-6 -right-6 bg-white p-4 rounded-lg shadow-lg hidden md:block">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-pet-pink rounded-full flex items-center justify-center text-white mr-4">
                            <i class="fas fa-medal text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Kaliteli Hizmet</p>
                            <p class="font-semibold">%100 Memnuniyet</p>
                        </div>
                    </div>
                </div>
                
                <!-- Üçüncü bilgi kartı - KÜÇÜK RESİM -->
                <div class="absolute top-1/2 right-0 transform translate-x-1/4 -translate-y-1/2 hidden md:block">
                    <img src="assets/images/dog-grooming.jpg" alt="Köpek Bakımı" class="w-32 h-32 object-cover rounded-lg shadow-lg border-4 border-white">
                </div>
                
                                <!-- Dördüncü bilgi kartı - KÜÇÜK RESİM -->
                <div class="absolute top-1/4 -left-10 transform -translate-x-1/4 hidden md:block">
                    <img src="assets/images/cat-grooming.jpg" alt="Kedi Bakımı" class="w-24 h-24 object-cover rounded-lg shadow-lg border-4 border-white">
    </div>
</section>

<!-- Hizmetlerimiz Bölümü -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-pet-blue">Hizmetlerimiz</h2>
            <div class="w-16 h-1 bg-pet-teal mx-auto mt-2 mb-4 rounded-full"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Evcil hayvanınız için sunduğumuz profesyonel bakım hizmetlerimizi keşfedin. Her türlü evcil hayvan için özel bakım seçenekleri sunuyoruz.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Hizmet 1 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="h-48 bg-pet-blue/10 flex items-center justify-center">
                    <i class="fas fa-bath text-6xl text-pet-blue"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-pet-blue mb-2">Yıkama ve Bakım</h3>
                    <p class="text-gray-600 mb-4">Evcil hayvanınız için özel şampuanlar ve ürünlerle profesyonel yıkama ve bakım hizmetleri.</p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-pet-teal mr-2"></i> Irka özel şampuanlar
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-pet-teal mr-2"></i> Tüy bakımı ve tarama
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-pet-teal mr-2"></i> Tırnak kesimi
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-pet-teal mr-2"></i> Kulak temizliği
                        </li>
                    </ul>
                    <a href="./pages/services.php" class="block text-center px-4 py-2 bg-pet-blue text-white font-medium rounded-md hover:bg-pet-teal transition-colors">
                        Daha Fazla Bilgi
                    </a>
                </div>
            </div>
            
            <!-- Hizmet 2 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="h-48 bg-pet-teal/10 flex items-center justify-center">
                    <i class="fas fa-cut text-6xl text-pet-teal"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-pet-blue mb-2">Tüy Kesimi</h3>
                    <p class="text-gray-600 mb-4">Evcil hayvanınızın ırkına ve ihtiyaçlarına özel profesyonel tüy kesim hizmetleri.</p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-pet-teal mr-2"></i> Irka özel kesim stilleri
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-pet-teal mr-2"></i> Profesyonel ekipmanlar
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-pet-teal mr-2"></i> Hijyenik ortam
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-pet-teal mr-2"></i> Özel tasarım kesimler
                        </li>
                    </ul>
                    <a href="./pages/services.php" class="block text-center px-4 py-2 bg-pet-blue text-white font-medium rounded-md hover:bg-pet-teal transition-colors">
                        Daha Fazla Bilgi
                    </a>
                </div>
            </div>
            
            <!-- Hizmet 3 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="h-48 bg-pet-pink/10 flex items-center justify-center">
                    <i class="fas fa-spa text-6xl text-pet-pink"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-pet-blue mb-2">Spa ve Özel Bakım</h3>
                    <p class="text-gray-600 mb-4">Evcil hayvanınızın kendini özel hissetmesi için lüks spa ve bakım hizmetleri.</p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-pet-teal mr-2"></i> Aromaterapi
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-pet-teal mr-2"></i> Masaj terapisi
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-pet-teal mr-2"></i> Cilt ve tüy maskeleri
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-pet-teal mr-2"></i> Lüks bakım ürünleri
                        </li>
                    </ul>
                    <a href="./pages/services.php" class="block text-center px-4 py-2 bg-pet-blue text-white font-medium rounded-md hover:bg-pet-teal transition-colors">
                        Daha Fazla Bilgi
                    </a>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <a href="./pages/services.php" class="inline-flex items-center px-6 py-3 bg-pet-blue text-white font-bold rounded-md hover:bg-pet-teal transition-all">
                Tüm Hizmetleri Görüntüle <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Nasıl Çalışır Bölümü -->
<section class="py-16 bg-pet-blue/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-pet-blue">Nasıl Çalışır?</h2>
            <div class="w-16 h-1 bg-pet-teal mx-auto mt-2 mb-4 rounded-full"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">PetKuaför ile evcil hayvanınız için randevu almanın ne kadar kolay olduğunu keşfedin. Sadece birkaç adımda sevimli dostunuza profesyonel bakım sağlayın.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Adım 1 -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 w-8 h-8 bg-pet-blue text-white rounded-full flex items-center justify-center font-bold">
                    1
                </div>
                <div class="w-20 h-20 bg-pet-blue/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-3xl text-pet-blue"></i>
                </div>
                <h3 class="text-lg font-semibold text-pet-blue mb-2">Kuaför Bul</h3>
                <p class="text-gray-600">Size en yakın ve en uygun pet kuaförünü bulun.</p>
            </div>
            
            <!-- Adım 2 -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 w-8 h-8 bg-pet-teal text-white rounded-full flex items-center justify-center font-bold">
                    2
                </div>
                <div class="w-20 h-20 bg-pet-teal/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-list-alt text-3xl text-pet-teal"></i>
                </div>
                <h3 class="text-lg font-semibold text-pet-teal mb-2">Hizmet Seç</h3>
                <p class="text-gray-600">Evcil hayvanınız için ihtiyacınız olan hizmeti seçin.</p>
            </div>
            
            <!-- Adım 3 -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 w-8 h-8 bg-pet-pink text-white rounded-full flex items-center justify-center font-bold">
                    3
                </div>
                <div class="w-20 h-20 bg-pet-pink/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-alt text-3xl text-pet-pink"></i>
                </div>
                <h3 class="text-lg font-semibold text-pet-pink mb-2">Randevu Al</h3>
                <p class="text-gray-600">Uygun tarih ve saati seçerek randevunuzu oluşturun.</p>
            </div>
            
            <!-- Adım 4 -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 w-8 h-8 bg-pet-yellow text-white rounded-full flex items-center justify-center font-bold">
                    4
                </div>
                <div class="w-20 h-20 bg-pet-yellow/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-paw text-3xl text-pet-yellow"></i>
                </div>
                <h3 class="text-lg font-semibold text-pet-yellow mb-2">Bakımı Alın</h3>
                <p class="text-gray-600">Randevu saatinde gelin ve profesyonel bakım hizmetinden yararlanın.</p>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <a href="./pages/booking.php" class="inline-flex items-center px-6 py-3 bg-pet-blue text-white font-bold rounded-md hover:bg-pet-teal transition-all">
                Hemen Randevu Al <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Neden Biz Bölümü -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold text-pet-blue mb-6">Neden PetKuaför?</h2>
                <p class="text-gray-600 mb-8">
                    PetKuaför, evcil hayvanınızın bakımı için en kaliteli ve güvenilir platformdur. Size ve sevimli dostunuza en iyi deneyimi sunmak için buradayız.
                </p>
                
                <ul class="space-y-4">
                    <li class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-pet-blue text-white">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Onaylı Kuaförler</h3>
                            <p class="mt-2 text-gray-600">
                                Tüm kuaförlerimiz profesyonel sertifikalara sahip ve düzenli olarak denetlenmektedir.
                            </p>
                        </div>
                    </li>
                    
                    <li class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-pet-teal text-white">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Kaliteli Hizmet</h3>
                            <p class="mt-2 text-gray-600">
                                En kaliteli ürünler ve ekipmanlarla evcil hayvanınıza en iyi bakımı sunuyoruz.
                            </p>
                        </div>
                    </li>
                    
                    <li class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-pet-pink text-white">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Güvenli Ortam</h3>
                            <p class="mt-2 text-gray-600">
                                Evcil hayvanınızın sağlığı ve güvenliği bizim için en büyük önceliktir.
                            </p>
                        </div>
                    </li>
                    
                    <li class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-pet-yellow text-white">
                                <i class="fas fa-tag"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Uygun Fiyatlar</h3>
                            <p class="mt-2 text-gray-600">
                                Kaliteli hizmeti uygun fiyatlarla sunarak tüm evcil hayvan sahiplerine hizmet veriyoruz.
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="relative">
                <img src="assets/images/neden-biz.jpg" alt="Neden Biz" class="rounded-lg shadow-xl">
                
                <div class="absolute -bottom-6 -right-6 bg-white py-3 px-6 rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <div class="mr-3">
                            <p class="text-2xl font-bold text-pet-blue">7/24</p>
                            <p class="text-sm text-gray-500">Online Destek</p>
                        </div>
                        <div class="w-12 h-12 bg-pet-teal/10 rounded-full flex items-center justify-center">
                            <i class="fas fa-headset text-2xl text-pet-teal"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sık Sorulan Sorular -->
<section class="py-16 bg-pet-blue/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-pet-blue">Sık Sorulan Sorular</h2>
            <div class="w-16 h-1 bg-pet-teal mx-auto mt-2 mb-4 rounded-full"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">PetKuaför hakkında merak ettiklerinizi sizin için yanıtladık. Hala sorularınız varsa, bizimle iletişime geçmekten çekinmeyin.</p>
        </div>
        
        <div class="max-w-3xl mx-auto">
            <!-- SSS Item 1 -->
            <div class="mb-4">
                <div class="bg-white p-4 rounded-lg shadow-md cursor-pointer" onclick="toggleFAQ(this)">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Randevu nasıl alabilirim?</h3>
                        <i class="fas fa-chevron-down text-pet-blue transition-transform transform"></i>
                    </div>
                </div>
                <div class="bg-white p-4 pb-6 rounded-b-lg shadow-md hidden mt-1 border-t">
                    <p class="text-gray-600">
                        PetKuaför üzerinden randevu almak çok kolay! Önce "Kuaför Bul" seçeneğine tıklayarak size en yakın kuaförleri görebilirsiniz. İstediğiniz kuaförün profilini inceleyip, sunduğu hizmetleri görüntüleyebilir ve uygun bir tarih ve saat seçerek randevunuzu oluşturabilirsiniz. Randevu onayı anında e-posta adresinize gönderilecektir.
                    </p>
                </div>
            </div>
            
            <!-- SSS Item 2 -->
            <div class="mb-4">
                <div class="bg-white p-4 rounded-lg shadow-md cursor-pointer" onclick="toggleFAQ(this)">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Ödeme nasıl yapılır?</h3>
                        <i class="fas fa-chevron-down text-pet-blue transition-transform transform"></i>
                    </div>
                </div>
                <div class="bg-white p-4 pb-6 rounded-b-lg shadow-md hidden mt-1 border-t">
                    <p class="text-gray-600">
                        PetKuaför'de ödeme işlemleri güvenli ve esnektir. Randevunuzu oluşturduktan sonra, hizmet bedelini kredi kartı ile çevrimiçi olarak ödeyebilir veya hizmet sonrası kuaförde nakit ya da kredi kartı ile ödeme yapabilirsiniz. Bazı kuaförler taksitli ödeme seçenekleri de sunabilmektedir. Ödeme tercihinizi randevu oluşturma aşamasında belirtebilirsiniz.
                    </p>
                </div>
            </div>
            
            <!-- SSS Item 3 -->
            <div class="mb-4">
                <div class="bg-white p-4 rounded-lg shadow-md cursor-pointer" onclick="toggleFAQ(this)">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Randevumu iptal edebilir miyim?</h3>
                        <i class="fas fa-chevron-down text-pet-blue transition-transform transform"></i>
                    </div>
                </div>
                <div class="bg-white p-4 pb-6 rounded-b-lg shadow-md hidden mt-1 border-t">
                    <p class="text-gray-600">
                        Evet, randevunuzu iptal edebilirsiniz. Hesabınıza giriş yaparak "Randevularım" bölümünden iptal etmek istediğiniz randevuyu seçerek işlemi tamamlayabilirsiniz. Lütfen iptal işlemlerini randevu saatinden en az 24 saat önce gerçekleştirmeniz gerektiğini unutmayın. 24 saatten daha kısa sürede yapılan iptallerde, kuaför politikasına bağlı olarak kısmi ücret alınabilir.
                    </p>
                </div>
            </div>
            
            <!-- SSS Item 4 -->
            <div class="mb-4">
                <div class="bg-white p-4 rounded-lg shadow-md cursor-pointer" onclick="toggleFAQ(this)">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Hangi hizmetler sunuluyor?</h3>
                        <i class="fas fa-chevron-down text-pet-blue transition-transform transform"></i>
                    </div>
                </div>
                <div class="bg-white p-4 pb-6 rounded-b-lg shadow-md hidden mt-1 border-t">
                    <p class="text-gray-600">
                        PetKuaför'de köpek, kedi, kuş ve küçük memeli hayvanlar için çeşitli bakım hizmetleri sunulmaktadır. Bu hizmetler arasında yıkama, tüy kesimi, tırnak bakımı, kulak temizliği, diş bakımı, tüy boyama, spa ve masaj terapisi gibi özel bakım hizmetleri bulunmaktadır. Her kuaför farklı hizmetler sunabileceğinden, kuaför profillerini inceleyerek detaylı bilgi alabilirsiniz.
                    </p>
                </div>
            </div>
            
            <!-- SSS Item 5 -->
            <div class="mb-4">
                <div class="bg-white p-4 rounded-lg shadow-md cursor-pointer" onclick="toggleFAQ(this)">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Evde bakım hizmeti alabilir miyim?</h3>
                        <i class="fas fa-chevron-down text-pet-blue transition-transform transform"></i>
                    </div>
                </div>
                <div class="bg-white p-4 pb-6 rounded-b-lg shadow-md hidden mt-1 border-t">
                    <p class="text-gray-600">
                        Evet, bazı kuaförlerimiz evde bakım hizmeti sunmaktadır. Kuaför profillerinde "Evde Bakım" ikonunu görebilirsiniz. Bu hizmeti sunan kuaförleri filtreleyerek randevu oluşturabilirsiniz. Evde bakım hizmetleri için ekstra ücret uygulanabilir ve genellikle belirli bir mesafe dahilinde hizmet verilmektedir.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- İletişim Bilgileri ve Çalışma Saatleri Bölümü -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-pet-blue">İletişime Geçin</h2>
            <div class="w-16 h-1 bg-pet-teal mx-auto mt-2 mb-4 rounded-full"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Sorularınız mı var? Müşteri hizmetlerimiz size yardımcı olmaktan memnuniyet duyacaktır. Aşağıdaki iletişim bilgilerinden bize ulaşabilirsiniz.</p>
        </div>
        
        <div class="bg-pet-blue/5 p-8 rounded-lg max-w-4xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- İletişim Bilgileri -->
                <div>
                    <h2 class="text-2xl font-bold text-pet-blue mb-6">İletişim Bilgileri</h2>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-pet-blue/10 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-envelope text-pet-blue"></i>
                            </div>
                            <span class="text-gray-600">info@petkuafor.com</span>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-pet-teal/10 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-phone-alt text-pet-teal"></i>
                            </div>
                            <span class="text-gray-600">+90 (212) 123 45 67</span>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-pet-pink/10 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-map-marker-alt text-pet-pink"></i>
                            </div>
                            <span class="text-gray-600">Bahçelievler, İstanbul, Türkiye</span>
                        </div>
                    </div>
                </div>
                
                <!-- Çalışma Saatleri -->
                <div>
                    <h2 class="text-2xl font-bold text-pet-blue mb-6">Çalışma Saatleri</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-600">Pazartesi - Cuma</span>
                            <span class="font-semibold">09:00 - 19:00</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-600">Cumartesi</span>
                            <span class="font-semibold">10:00 - 18:00</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-600">Pazar</span>
                            <span class="font-semibold">Kapalı</span>
                        </div>
                    </div>
                    
                    <div class="mt-8 text-center md:text-left">
                        <a href="./pages/contact.php" class="inline-flex items-center px-6 py-3 bg-pet-blue text-white font-bold rounded-md hover:bg-pet-teal transition-all">
                            İletişim Formu <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript - FAQ Fonksiyonu -->
<script>
function toggleFAQ(element) {
    const content = element.nextElementSibling;
    const icon = element.querySelector('i');
    
    // İçeriği göster/gizle
    content.classList.toggle('hidden');
    
    // İkonu döndür
    icon.classList.toggle('rotate-180');
}
</script>

<?php
// Footer'ı dahil et
include_once 'includes/footer.php';
?>