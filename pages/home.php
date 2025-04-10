<?php
// Anasayfa içeriği

// Şehir listesini al (normalde veritabanından çekilir)
$cities = [
    "Adana", "Adıyaman", "Afyonkarahisar", "Ağrı", "Amasya", "Ankara", "Antalya", "Artvin", "Aydın", "Balıkesir",
    "Bilecik", "Bingöl", "Bitlis", "Bolu", "Burdur", "Bursa", "Çanakkale", "Çankırı", "Çorum", "Denizli", "Diyarbakır",
    "Edirne", "Elazığ", "Erzincan", "Erzurum", "Eskişehir", "Gaziantep", "Giresun", "Gümüşhane", "Hakkari", "Hatay",
    "Isparta", "Mersin", "İstanbul", "İzmir", "Kars", "Kastamonu", "Kayseri", "Kırklareli", "Kırşehir", "Kocaeli",
    "Konya", "Kütahya", "Malatya", "Manisa", "Kahramanmaraş", "Mardin", "Muğla", "Muş", "Nevşehir", "Niğde", "Ordu",
    "Rize", "Sakarya", "Samsun", "Siirt", "Sinop", "Sivas", "Tekirdağ", "Tokat", "Trabzon", "Tunceli", "Şanlıurfa",
    "Uşak", "Van", "Yozgat", "Zonguldak", "Aksaray", "Bayburt", "Karaman", "Kırıkkale", "Batman", "Şırnak", "Bartın",
    "Ardahan", "Iğdır", "Yalova", "Karabük", "Kilis", "Osmaniye", "Düzce"
];

// Güncel evdehayvan sayısı (dinamik olarak veritabanından çekilebilir)
$total_pets = 87;
$total_salons = 56;
$total_services = 189;
$total_bookings = 1247;
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-4 fw-bold mb-4">Size En Yakın <span class="text-pet-teal">Pet Kuaför</span> Salonları</h1>
                <p class="lead mb-5">Evcil dostunuza en iyi bakımı sunmak için profesyonel pet kuaför salonlarını hemen keşfedin.</p>
                
                <!-- Arama Formu -->
                <form id="searchForm" action="<?php echo url('salons'); ?>" method="GET" class="mb-3">
                    <div class="row g-2 justify-content-center">
                        <div class="col-md-5 col-sm-12">
                            <div class="city-dropdown">
                                <input type="text" class="form-control form-control-lg shadow-sm" id="location" name="location" placeholder="Şehir seçin veya yazın" autocomplete="off" oninput="filterCities(this.value)">
                                <div id="cityList" class="city-list"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <select class="form-select form-select-lg shadow-sm" id="district" name="district">
                                <option value="all">Tüm Mahalleler</option>
                                <!-- JavaScript ile doldurulacak -->
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <button type="submit" class="btn bg-pet-blue text-white btn-lg w-100 shadow-sm">Ara</button>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pet_type" id="pet_type_all" value="all" checked>
                                <label class="form-check-label text-white" for="pet_type_all">Tüm Hayvanlar</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pet_type" id="pet_type_dog" value="dog">
                                <label class="form-check-label text-white" for="pet_type_dog">Köpek</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pet_type" id="pet_type_cat" value="cat">
                                <label class="form-check-label text-white" for="pet_type_cat">Kedi</label>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Konum Butonu -->
                <button id="locationButton" class="btn btn-outline-light btn-sm rounded-pill mt-2 mb-4">
                    <i class="fas fa-map-marker-alt me-2"></i> Konumumu Kullan
                </button>
                
                <!-- Durum mesajı -->
                <div id="locationStatus" class="small text-muted mt-2"></div>
            </div>
        </div>
    </div>
</section>

<!-- İstatistikler -->
<section class="py-5 bg-dark">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="display-5 text-pet-teal mb-3">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3 class="h2 text-white"><?php echo $total_salons; ?></h3>
                    <p class="text-muted">Aktif Salon</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="display-5 text-pet-teal mb-3">
                        <i class="fas fa-paw"></i>
                    </div>
                    <h3 class="h2 text-white"><?php echo $total_pets; ?>K+</h3>
                    <p class="text-muted">Mutlu Evcil Hayvan</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="display-5 text-pet-teal mb-3">
                        <i class="fas fa-list-alt"></i>
                    </div>
                    <h3 class="h2 text-white"><?php echo $total_services; ?></h3>
                    <p class="text-muted">Bakım Hizmeti</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="display-5 text-pet-teal mb-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="h2 text-white"><?php echo $total_bookings; ?></h3>
                    <p class="text-muted">Tamamlanan Randevu</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Hizmetlerimiz -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="fw-bold">Pet Kuaför <span class="text-pet-teal">Hizmetlerimiz</span></h2>
                <p class="text-muted">Evcil hayvanınızın bakımı için aradığınız tüm profesyonel hizmetler.</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <span class="display-5 text-pet-blue">
                                <i class="fas fa-bath"></i>
                            </span>
                        </div>
                        <h3 class="h4 mb-3">Banyo & Yıkama</h3>
                        <p class="text-muted mb-0">Özel şampuan ve bakım ürünleriyle evcil dostunuzun tüylerini temizler, parlatır ve sağlıklı kalmasını sağlarız.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <span class="display-5 text-pet-blue">
                                <i class="fas fa-cut"></i>
                            </span>
                        </div>
                        <h3 class="h4 mb-3">Tıraş & Şekillendirme</h3>
                        <p class="text-muted mb-0">Irka özgü tıraş teknikleri veya sizin tercih ettiğiniz modele göre profesyonel kesim ve şekillendirme hizmeti.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <span class="display-5 text-pet-blue">
                                <i class="fas fa-heart"></i>
                            </span>
                        </div>
                        <h3 class="h4 mb-3">Tırnak Bakımı</h3>
                        <p class="text-muted mb-0">Güvenli ve ağrısız şekilde tırnak kesimi yaparak evcil hayvanınızın konforunu ve sağlığını koruyoruz.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <span class="display-5 text-pet-blue">
                                <i class="fas fa-paw"></i>
                            </span>
                        </div>
                        <h3 class="h4 mb-3">Kulak Temizliği</h3>
                        <p class="text-muted mb-0">Evcil hayvanınızın kulaklarını özel solüsyonlarla temizleyerek enfeksiyon riskini azaltır ve sağlığını koruruz.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <span class="display-5 text-pet-blue">
                                <i class="fas fa-paint-brush"></i>
                            </span>
                        </div>
                        <h3 class="h4 mb-3">Tüy Bakımı</h3>
                        <p class="text-muted mb-0">Tarama, yumak çözme ve özel bakım ürünleriyle evcil hayvanınızın tüyleri parlak ve sağlıklı kalır.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <span class="display-5 text-pet-blue">
                                <i class="fas fa-spray-can"></i>
                            </span>
                        </div>
                        <h3 class="h4 mb-3">Parfüm Uygulaması</h3>
                        <p class="text-muted mb-0">Hayvan sağlığına uygun, özel olarak formüle edilmiş kokular ile evcil dostunuz uzun süre güzel kokar.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="<?php echo url('services'); ?>" class="btn bg-pet-blue text-white px-4 py-2 rounded-pill">
                Tüm Hizmetleri Görüntüle
            </a>
        </div>
    </div>
</section>

<!-- Nasıl Çalışır -->
<section class="py-5 bg-dark">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="fw-bold text-white">Nasıl <span class="text-pet-teal">Çalışır?</span></h2>
                <p class="text-muted">Sadece birkaç adımda kolayca randevu alabilirsiniz.</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-center">
                    <div class="mb-3">
                        <span class="display-5 text-pet-teal">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                    <h3 class="h4 mb-3 text-white">1. Salon Bul</h3>
                    <p class="text-muted">Konumunuza göre en yakın pet kuaför salonlarını bulun ve inceleyin.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="mb-3">
                        <span class="display-5 text-pet-teal">
                            <i class="fas fa-list-alt"></i>
                        </span>
                    </div>
                    <h3 class="h4 mb-3 text-white">2. Hizmet Seç</h3>
                    <p class="text-muted">Evcil hayvanınıza uygun olan hizmetleri ve fiyatları karşılaştırın.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="mb-3">
                        <span class="display-5 text-pet-teal">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                    <h3 class="h4 mb-3 text-white">3. Randevu Al</h3>
                    <p class="text-muted">Size uygun bir tarih ve saat belirleyerek online randevunuzu oluşturun.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="mb-3">
                        <span class="display-5 text-pet-teal">
                            <i class="fas fa-smile"></i>
                        </span>
                    </div>
                    <h3 class="h4 mb-3 text-white">4. Salona Gidin</h3>
                    <p class="text-muted">Randevu saatinde salona giderek profesyonel bakım hizmetinden yararlanın.</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="<?php echo url('salons'); ?>" class="btn bg-pet-teal text-white px-4 py-2 rounded-pill">
                Hemen Randevu Al
            </a>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="fw-bold">Mutlu <span class="text-pet-teal">Müşterilerimiz</span></h2>
                <p class="text-muted">Evcil hayvan sahiplerinin Pet Kuaför deneyimleri hakkında ne söylediklerini okuyun.</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm review-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="User" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <h5 class="mb-0">Ayşe Y.</h5>
                                <div class="star-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted">"Köpeğim Şila'yı ilk kez götürdüğüm bir kuaförlük hizmetiydi ve gerçekten harika bir deneyim yaşadık. Çok profesyonel ve sevgi dolu yaklaştılar. Kesinlikle tekrar geleceğiz."</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm review-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/men/44.jpg" alt="User" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <h5 class="mb-0">Mehmet K.</h5>
                                <div class="star-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star text-muted"></i>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted">"Kedim Zeytin tıraş olmayı pek sevmiyor, ancak buradaki çalışanlar çok sabırlı ve anlayışlıydı. Sakin bir ortamda sorunsuz bir şekilde bakımını yaptılar. Fiyatlar da gayet uygun."</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm review-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="User" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <h5 class="mb-0">Zeynep A.</h5>
                                <div class="star-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted">"Online randevu sistemi çok pratik. Önceden randevu alıp beklemeden hizmet alabildik. Köpeğimiz Tarçın'ın tüyleri hiç bu kadar güzel olmamıştı, teşekkürler!"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-5 bg-pet-blue text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-9 mb-4 mb-lg-0">
                <h2 class="fw-bold mb-3">Evcil dostunuz için profesyonel bakım hizmeti alın</h2>
                <p class="lead mb-0">Hemen size en yakın pet kuaför salonunu keşfedin ve online randevu alın.</p>
            </div>
            <div class="col-lg-3 text-lg-end">
                <a href="<?php echo url('salons'); ?>" class="btn btn-light text-pet-blue px-4 py-2 rounded-pill fw-bold">
                    <i class="fas fa-search me-2"></i> Salon Ara
                </a>
            </div>
        </div>
    </div>
</section>