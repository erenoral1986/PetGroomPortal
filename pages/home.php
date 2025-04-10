<?php
// Ana sayfa içeriği

// Şehirler listesi
$cities = get_cities();
?>

<!-- Hero Bölümü -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold text-primary mb-3">Evcil Hayvanınıza <span class="text-pet-pink">Özel Bakım</span> Hizmeti</h1>
                <p class="lead mb-4">Sevimli dostunuzun en güzel hizmeti hak ettiğini biliyoruz. Size en yakın pet kuaförünü bulun, hemen online randevu alın!</p>
                
                <!-- Lokasyon Arama Formu -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <form action="<?php echo url('salons'); ?>" method="GET" id="searchForm">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="location" class="form-label fw-bold">Size en yakın pet kuaförleri bulmak için konum seçin:</label>
                                <div class="input-group position-relative">
                                    <input type="text" id="location" name="location" class="form-control" placeholder="Şehir adı girin (ör: İstanbul)" autocomplete="off" onkeyup="filterCities(this.value)" onfocus="showCityList()">
                                    <button type="button" id="locationButton" class="btn btn-outline-secondary" title="Konumumu Kullan">
                                        <i class="fas fa-location-dot"></i>
                                    </button>
                                    
                                    <!-- Şehir listesi dropdown -->
                                    <div id="cityList" class="position-absolute w-100 mt-1"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="district" class="form-label fw-bold">Mahalle</label>
                                <select id="district" name="district" class="form-select">
                                    <option value="all">Tüm Mahalleler</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="pet_type" class="form-label fw-bold">Evcil Hayvan Türü</label>
                                <select id="pet_type" name="pet_type" class="form-select">
                                    <option value="all">Tüm Evcil Hayvanlar</option>
                                    <option value="dog">Köpek</option>
                                    <option value="cat">Kedi</option>
                                    <option value="both">Kedi ve Köpek</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn bg-pet-blue text-white w-100 py-2">
                                    <i class="fas fa-search me-2"></i> Pet Kuaför Ara
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Özellikler -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="info-card-icon bg-pet-pink me-3 flex-shrink-0">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <h5 class="fs-6 fw-bold mb-1">Online Randevu</h5>
                                <p class="small text-muted mb-0">7/24 hızlı ve kolay randevu sistemi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="info-card-icon bg-pet-teal me-3 flex-shrink-0">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div>
                                <h5 class="fs-6 fw-bold mb-1">Kaliteli Hizmet</h5>
                                <p class="small text-muted mb-0">Profesyonel ve deneyimli kuaförler</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="hero-image d-none d-lg-block">
                    <img src="static/img/hero-dog.jpg" alt="Köpek tıraşı" class="img-fluid rounded-4">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Hizmetlerimiz -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fs-1 fw-bold text-pet-blue mb-2">Hizmetlerimiz</h2>
            <p class="lead text-muted">Evcil hayvanınız için en iyi bakım hizmetlerini sunuyoruz</p>
        </div>
        
        <div class="row g-4">
            <!-- Köpek Bakımı -->
            <div class="col-md-6 col-lg-4">
                <div class="service-card h-100">
                    <img src="static/img/dog-grooming.jpg" alt="Köpek Bakımı" class="w-100">
                    <div class="p-4">
                        <h3 class="fs-5 fw-bold mb-2">Köpek Bakımı</h3>
                        <p class="text-muted mb-3">Köpeğinizin ırkına ve tüy tipine özel profesyonel tıraş ve bakım hizmeti.</p>
                        <a href="<?php echo url('salons', ['pet_type' => 'dog']); ?>" class="btn service-btn bg-pet-blue text-white px-4">
                            Detaylar <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Kedi Bakımı -->
            <div class="col-md-6 col-lg-4">
                <div class="service-card h-100">
                    <img src="static/img/cat-grooming.jpg" alt="Kedi Bakımı" class="w-100">
                    <div class="p-4">
                        <h3 class="fs-5 fw-bold mb-2">Kedi Bakımı</h3>
                        <p class="text-muted mb-3">Kedilere özel tüy bakımı, yıkama ve tırnak kesimi hizmetleri.</p>
                        <a href="<?php echo url('salons', ['pet_type' => 'cat']); ?>" class="btn service-btn bg-pet-blue text-white px-4">
                            Detaylar <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Premium Hizmetler -->
            <div class="col-md-6 col-lg-4">
                <div class="service-card h-100">
                    <img src="static/img/premium-grooming.jpg" alt="Premium Hizmetler" class="w-100">
                    <div class="p-4">
                        <h3 class="fs-5 fw-bold mb-2">Premium Hizmetler</h3>
                        <p class="text-muted mb-3">Özel bakım, spa ve masaj gibi lüks hizmetlerle evcil hayvanınızı şımartın.</p>
                        <a href="<?php echo url('salons'); ?>" class="btn service-btn bg-pet-blue text-white px-4">
                            Detaylar <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Nasıl Çalışır -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fs-1 fw-bold text-pet-blue mb-2">Nasıl Çalışır?</h2>
            <p class="lead text-muted">3 kolay adımda sevimli dostunuza randevu alın</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="step-card h-100">
                    <div class="step-circle d-flex align-items-center justify-content-center mx-auto mb-4">1</div>
                    <h3 class="fs-5 fw-bold mb-3">Salon Bulun</h3>
                    <p class="text-muted">Size en yakın pet kuaförünü seçin ve hizmetlerini inceleyin.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="step-card h-100">
                    <div class="step-circle d-flex align-items-center justify-content-center mx-auto mb-4">2</div>
                    <h3 class="fs-5 fw-bold mb-3">Tarih ve Saat Seçin</h3>
                    <p class="text-muted">Size uygun bir gün ve saat seçin. Müsait zaman dilimlerini görebilirsiniz.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="step-card h-100">
                    <div class="step-circle d-flex align-items-center justify-content-center mx-auto mb-4">3</div>
                    <h3 class="fs-5 fw-bold mb-3">Randevunuzu Onaylayın</h3>
                    <p class="text-muted">Evcil hayvanınızın bilgilerini girin ve randevunuzu onaylayın.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Müşteri Yorumları Bölümü -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fs-1 fw-bold text-pet-blue mb-2">Müşteri Yorumları</h2>
            <p class="lead text-muted">Evcil hayvan sahiplerinin deneyimleri</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text mb-4">"Köpeğim Paşa'nın bakımı muhteşem oldu. Personel çok ilgili ve randevu almak çok kolaydı. Kesinlikle tekrar geleceğiz!"</p>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-pet-blue text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <span class="fw-bold">AY</span>
                            </div>
                            <div>
                                <h5 class="fs-6 fw-bold mb-1">Ayşe Yılmaz</h5>
                                <p class="small text-muted mb-0">İstanbul, Kadıköy</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text mb-4">"Kedim Boncuk normalde yabancılarla çok gergin olur ama buradaki ekip onu çok iyi sakinleştirdi. Tüy bakımı harika oldu!"</p>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-pet-pink text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <span class="fw-bold">MK</span>
                            </div>
                            <div>
                                <h5 class="fs-6 fw-bold mb-1">Mehmet Kaya</h5>
                                <p class="small text-muted mb-0">Ankara, Çankaya</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                        </div>
                        <p class="card-text mb-4">"Online randevu sistemi çok pratik! Golden retriever'ım Max'e verilen özel ilgi için teşekkürler. Tüyleri hiç olmadığı kadar parlak."</p>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-pet-teal text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <span class="fw-bold">ZA</span>
                            </div>
                            <div>
                                <h5 class="fs-6 fw-bold mb-1">Zeynep Arslan</h5>
                                <p class="small text-muted mb-0">İzmir, Bornova</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Bölümü -->
<section class="py-5">
    <div class="container">
        <div class="cta-section">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <h2 class="fs-1 fw-bold text-pet-blue mb-2">Hemen Randevu Alın!</h2>
                    <p class="lead mb-0">Evcil dostunuza profesyonel kuaför hizmeti için bugün randevu alın.</p>
                </div>
                <div class="col-lg-4">
                    <a href="<?php echo url('salons'); ?>" class="btn bg-pet-blue text-white btn-lg w-100 py-3">
                        Şimdi Randevu Al <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JS Değişkenleri ve Türkçe yerelleştirme -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Flatpickr Türkçe dil desteği
    flatpickr.localize(flatpickr.l10ns.tr);
    
    // Şehirler listesi
    const cities = <?php echo json_encode($cities); ?>;
    
    // cities.js dosyasına erişim için global değişken
    window.CITIES_LIST = cities;
});
</script>