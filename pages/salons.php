<?php
// Salon arama ve listeleme sayfası

// Arama parametrelerini al
$search_params = get_salon_search_params();
$location = $search_params['location'];
$district = $search_params['district'];
$pet_type = $search_params['pet_type'];

// Salon listesini al
$salons = [];
if (!empty($location)) {
    $salons = get_salons($search_params);
}

// Arama yapıldı mı kontrolü
$search_performed = !empty($location);

// Pet türleri için etiketler
$pet_type_labels = [
    'all' => 'Tüm Evcil Hayvanlar',
    'dog' => 'Köpek',
    'cat' => 'Kedi',
    'both' => 'Kedi ve Köpek'
];

// Filtreleme yapıldı mı?
$is_filtered = ($district !== 'all' || $pet_type !== 'all');

// Sonuç sayısı
$results_count = count($salons);
?>

<!-- Page Header -->
<div class="py-4 bg-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="fw-bold text-white mb-3">Pet Kuaför <span class="text-pet-teal">Salonları</span></h1>
                <p class="lead text-muted">Konumunuza en yakın pet kuaför salonlarını bulun ve hemen randevu alın.</p>
            </div>
        </div>
    </div>
</div>

<!-- Search Section -->
<section class="py-4 bg-dark border-bottom border-secondary">
    <div class="container">
        <form id="searchForm" action="<?php echo url('salons'); ?>" method="GET">
            <div class="row g-2">
                <div class="col-lg-4 col-md-4">
                    <div class="city-dropdown">
                        <input type="text" class="form-control" id="location" name="location" placeholder="Şehir seçin veya yazın" value="<?php echo escape($location); ?>" autocomplete="off" oninput="filterCities(this.value)">
                        <div id="cityList" class="city-list"></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3">
                    <select class="form-select" id="district" name="district">
                        <option value="all">Tüm Mahalleler</option>
                        <?php
                        if (!empty($location)) {
                            $districts = get_districts($location);
                            foreach ($districts as $d) {
                                $selected = ($district === $d) ? 'selected' : '';
                                echo "<option value=\"" . escape($d) . "\" $selected>" . escape($d) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-3">
                    <select class="form-select" id="pet_type" name="pet_type">
                        <?php foreach ($pet_type_labels as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo ($pet_type === $value) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-2">
                    <button type="submit" class="btn bg-pet-blue text-white w-100">Ara</button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Results Section -->
<section class="py-5">
    <div class="container">
        <?php if ($search_performed): ?>
            <!-- Arama sonuçları -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 fw-bold mb-0">
                        <?php echo $location; ?> <?php echo $district !== 'all' ? '- ' . $district : ''; ?> 
                        <?php if ($pet_type !== 'all'): ?>
                            <span class="badge bg-dark ms-2"><?php echo $pet_type_labels[$pet_type]; ?></span>
                        <?php endif; ?>
                    </h2>
                    <p class="text-muted mb-0"><?php echo $results_count; ?> salon bulundu</p>
                </div>
                
                <?php if ($is_filtered): ?>
                    <a href="<?php echo url('salons') . '?location=' . urlencode($location); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Filtreleri Temizle
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if (empty($salons)): ?>
                <!-- Sonuç bulunamadı -->
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h3>Salon Bulunamadı</h3>
                    <p class="text-muted mb-4">Seçtiğiniz kriterlere uygun salon bulunamadı. Lütfen farklı bir konum veya filtre seçin.</p>
                    <a href="<?php echo url('salons'); ?>" class="btn btn-outline-secondary">Filtreleri Temizle</a>
                </div>
            <?php else: ?>
                <!-- Salon listesi -->
                <div class="row g-4">
                    <?php foreach ($salons as $salon): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 border-0 shadow-sm salon-card">
                                <div class="card-img-top position-relative">
                                    <img src="https://source.unsplash.com/random/300x180/?pet,grooming,salon&sig=<?php echo $salon['id']; ?>" alt="<?php echo escape($salon['name']); ?>" class="w-100">
                                    <?php
                                    // Eğer pet_type filtrelemesi yapılmışsa, ilgili badge'i göster
                                    if ($pet_type !== 'all'):
                                        echo '<span class="badge bg-pet-teal">' . $pet_type_labels[$pet_type] . ' Bakımı</span>';
                                    endif;
                                    ?>
                                </div>
                                <div class="card-body p-4">
                                    <h3 class="h5 fw-bold mb-2"><?php echo escape($salon['name']); ?></h3>
                                    <p class="text-muted small mb-3">
                                        <i class="fas fa-map-marker-alt me-1 text-pet-teal"></i> 
                                        <?php echo escape($salon['address']); ?>
                                    </p>
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="text-warning me-2">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </span>
                                        <span class="small text-muted">(18 değerlendirme)</span>
                                    </div>
                                    <div class="d-flex align-items-center small text-muted mb-3">
                                        <div class="me-3">
                                            <i class="far fa-clock me-1"></i> 
                                            <?php 
                                            echo format_time($salon['opens_at']) . ' - ' . format_time($salon['closes_at']); 
                                            ?>
                                        </div>
                                        <div>
                                            <i class="fas fa-phone-alt me-1"></i> 
                                            <?php echo escape($salon['phone']); ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="<?php echo url('salon_detail') . '&id=' . $salon['id']; ?>" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-info-circle me-1"></i> Detaylar
                                        </a>
                                        <a href="<?php echo url('book_appointment') . '&salon_id=' . $salon['id']; ?>" class="btn bg-pet-blue text-white btn-sm">
                                            <i class="fas fa-calendar-plus me-1"></i> Randevu Al
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- Arama yapılmadı, konum seçimi isteği -->
            <div class="text-center py-5">
                <i class="fas fa-search-location fa-4x text-pet-blue mb-3"></i>
                <h2 class="mb-3">Pet Kuaför Salonlarını Keşfedin</h2>
                <p class="lead text-muted mb-4">Size en yakın pet kuaför salonlarını bulmak için lütfen bir şehir seçin.</p>
                
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="list-group mb-4">
                            <a href="<?php echo url('salons') . '?location=İstanbul'; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                İstanbul
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <a href="<?php echo url('salons') . '?location=Ankara'; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Ankara
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <a href="<?php echo url('salons') . '?location=İzmir'; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                İzmir
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <a href="<?php echo url('salons') . '?location=Antalya'; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Antalya
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                        
                        <button id="locationButton" class="btn bg-pet-blue text-white px-4">
                            <i class="fas fa-map-marker-alt me-2"></i> Konumumu Kullan
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Map and Info Section -->
<?php if ($search_performed && !empty($salons)): ?>
<section class="py-5 bg-dark">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <h2 class="h4 fw-bold text-white mb-4">Kuaför Salonu Seçerken Dikkat Edilmesi Gerekenler</h2>
                
                <div class="card bg-dark border-start border-pet-teal border-4 mb-3 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="text-pet-teal"><i class="fas fa-certificate fa-2x"></i></span>
                            </div>
                            <div>
                                <h3 class="h5 text-white">Uzman Kadro</h3>
                                <p class="text-muted mb-0">Pet kuaförünün eğitimli ve deneyimli personele sahip olduğundan emin olun.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-dark border-start border-pet-teal border-4 mb-3 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="text-pet-teal"><i class="fas fa-star fa-2x"></i></span>
                            </div>
                            <div>
                                <h3 class="h5 text-white">Müşteri Yorumları</h3>
                                <p class="text-muted mb-0">Daha önce hizmet alan müşterilerin değerlendirmelerini kontrol edin.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-dark border-start border-pet-teal border-4 mb-3 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="text-pet-teal"><i class="fas fa-broom fa-2x"></i></span>
                            </div>
                            <div>
                                <h3 class="h5 text-white">Temizlik ve Hijyen</h3>
                                <p class="text-muted mb-0">Salonun temizliği ve kullanılan ekipmanların hijyeni önemlidir.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-dark border-start border-pet-teal border-4 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="text-pet-teal"><i class="fas fa-tags fa-2x"></i></span>
                            </div>
                            <div>
                                <h3 class="h5 text-white">Fiyatlandırma</h3>
                                <p class="text-muted mb-0">Fiyat ve sunulan hizmet kalitesi arasında doğru dengeyi bulun.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-0 shadow h-100">
                    <div class="card-body p-0">
                        <div id="map" class="w-100 h-100" style="min-height: 400px;">
                            <!-- Harita görüntüsü -->
                            <img src="https://maps.googleapis.com/maps/api/staticmap?center=<?php echo urlencode($location); ?>&zoom=12&size=600x400&maptype=roadmap&key=YOUR_API_KEY" alt="Salon Haritası" class="w-100 h-100" style="object-fit: cover;">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-75">
                                <div class="text-center p-4">
                                    <i class="fas fa-map-marked-alt fa-3x text-pet-teal mb-3"></i>
                                    <h3 class="text-white">Harita Görüntüsü</h3>
                                    <p class="text-muted">Gerçek uygulamada salon konumları haritada gösterilecektir.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-5 bg-pet-blue text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h2 class="fw-bold">Pet kuaför salonunuzu platforma eklemek ister misiniz?</h2>
                <p class="lead mb-0">Binlerce potansiyel müşteriye ulaşın ve işinizi büyütün.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo url('contact'); ?>" class="btn btn-light text-pet-blue px-4 py-2 rounded-pill fw-bold">
                    <i class="fas fa-store me-2"></i> Salon Ekle
                </a>
            </div>
        </div>
    </div>
</section>