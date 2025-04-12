
<?php
require_once 'header.php';

// Sample salon data
$salons = [
    [
        'id' => 1,
        'name' => 'Happy Paws Pet Salon',
        'address' => 'Bağdat Caddesi No:123',
        'district' => 'Kadıköy',
        'city' => 'İstanbul',
        'rating' => 4.8,
        'review_count' => 156,
        'phone' => '0216 123 45 67',
        'description' => 'Profesyonel pet kuaför hizmetleri',
        'opens_at' => '09:00',
        'closes_at' => '19:00',
        'image' => 'static/img/salon1.jpg',
        'services' => ['Kedi Bakımı', 'Köpek Bakımı', 'Tırnak Kesimi']
    ],
    [
        'id' => 2,
        'name' => 'Pati Pet Grooming',
        'address' => 'Nispetiye Caddesi No:45',
        'district' => 'Beşiktaş',
        'city' => 'İstanbul', 
        'rating' => 4.9,
        'review_count' => 203,
        'phone' => '0212 345 67 89',
        'description' => 'Uzman kadromuzla evcil dostlarınıza özel bakım',
        'opens_at' => '10:00',
        'closes_at' => '20:00',
        'image' => 'static/img/salon2.jpg',
        'services' => ['Kedi Bakımı', 'Köpek Bakımı', 'Spa & Masaj']
    ],
    [
        'id' => 3,
        'name' => 'VIP Pet Care',
        'address' => 'Ataşehir Bulvarı No:78',
        'district' => 'Ataşehir',
        'city' => 'İstanbul',
        'rating' => 4.7,
        'review_count' => 178,
        'phone' => '0216 789 01 23',
        'description' => 'VIP pet bakım ve güzellik merkezi',
        'opens_at' => '09:00',
        'closes_at' => '21:00',
        'image' => 'static/img/salon3.jpg',
        'services' => ['Kedi Bakımı', 'Köpek Bakımı', 'Özel Bakım']
    ]
];

// Filter and sort logic
$location = isset($_GET['location']) ? $_GET['location'] : '';
$district = isset($_GET['district']) ? $_GET['district'] : '';
$pet_type = isset($_GET['pet_type']) ? $_GET['pet_type'] : '';

// Check if any filter is applied
$is_filtered = !empty($location) || !empty($district) || ($pet_type && $pet_type !== 'all');

// Filter salons if filters are applied
if ($is_filtered) {
    $salons = array_filter($salons, function($salon) use ($location, $district, $pet_type) {
        $locationMatch = empty($location) || 
                        stripos($salon['city'], $location) !== false || 
                        stripos($salon['address'], $location) !== false;
        $districtMatch = empty($district) || $district === 'Tüm Mahalleler' || 
                        stripos($salon['district'], $district) !== false;
        return $locationMatch && $districtMatch;
    });
}

// Always sort by rating (descending)
usort($salons, function($a, $b) {
    return $b['rating'] <=> $a['rating'];
});
?>

<main class="py-5">
    <div class="container">
        <!-- Search Section -->
        <div class="card shadow-sm mb-5">
            <div class="card-body p-4">
                <h4 class="mb-4">Pet Kuaförü Bul</h4>
                <form action="" method="get" class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-map-marker-alt text-muted"></i>
                            </span>
                            <input type="text" name="location" class="form-control border-start-0" 
                                   placeholder="Şehir" value="<?php echo htmlspecialchars($location); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-building text-muted"></i>
                            </span>
                            <select name="district" class="form-select border-start-0">
                                <option value="Tüm Mahalleler">Tüm Mahalleler</option>
                                <option value="Kadıköy" <?php echo $district === 'Kadıköy' ? 'selected' : ''; ?>>Kadıköy</option>
                                <option value="Beşiktaş" <?php echo $district === 'Beşiktaş' ? 'selected' : ''; ?>>Beşiktaş</option>
                                <option value="Ataşehir" <?php echo $district === 'Ataşehir' ? 'selected' : ''; ?>>Ataşehir</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Kuaför Bul</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results -->
        <div class="row">
            <?php if ($is_filtered): ?>
                <div class="col-12">
                    <h4 class="mb-4">Arama Sonuçları: <?php echo htmlspecialchars($location); ?></h4>
                </div>
            <?php else: ?>
                <div class="col-12">
                    <h4 class="mb-4">En İyi Değerlendirilen Kuaförler</h4>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($salons)): ?>
                <?php foreach ($salons as $salon): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($salon['name']); ?></h5>
                                    <span class="badge bg-primary"><?php echo number_format($salon['rating'], 1); ?></span>
                                </div>
                                <div class="mb-2">
                                    <?php for($i = 0; $i < 5; $i++): ?>
                                        <?php if ($i < floor($salon['rating'])): ?>
                                            <i class="fas fa-star text-warning"></i>
                                        <?php elseif ($i < $salon['rating']): ?>
                                            <i class="fas fa-star-half-alt text-warning"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-warning"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <span class="ms-1 text-muted">(<?php echo $salon['review_count']; ?>)</span>
                                </div>
                                <p class="card-text">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <?php echo htmlspecialchars($salon['address']); ?>
                                </p>
                                <p class="card-text">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    <?php echo $salon['opens_at']; ?> - <?php echo $salon['closes_at']; ?>
                                </p>
                                <div class="mb-3">
                                    <small class="text-muted">Hizmetler:</small><br>
                                    <?php foreach($salon['services'] as $service): ?>
                                        <span class="badge bg-light text-dark me-1"><?php echo htmlspecialchars($service); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <a href="salon_detail.php?id=<?php echo $salon['id']; ?>" class="btn btn-outline-primary w-100">
                                    Detayları Gör
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                    <h4 class="text-muted">Kuaför bulunamadı</h4>
                    <p>Farklı bir konum için arama yapabilir veya tüm kuaförleri görebilirsiniz.</p>
                    <a href="salons.php" class="btn btn-outline-primary mt-2">Tüm Kuaförleri Gör</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>
