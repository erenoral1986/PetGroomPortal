<?php
require_once 'header.php';

// Sabit kuaför verileri
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
        'name' => 'PetLux Beauty',
        'address' => 'İstiklal Caddesi No:78',
        'district' => 'Beyoğlu',
        'city' => 'İstanbul',
        'rating' => 4.7,
        'review_count' => 178,
        'phone' => '0212 987 65 43',
        'description' => 'Lüks pet bakım hizmetleri',
        'opens_at' => '09:00',
        'closes_at' => '21:00',
        'image' => 'static/img/salon3.jpg',
        'services' => ['Kedi Bakımı', 'Köpek Bakımı', 'Özel Bakım']
    ],
    [
        'id' => 4,
        'name' => 'Pet Style Studio',
        'address' => 'Teşvikiye Caddesi No:157',
        'district' => 'Nişantaşı',
        'city' => 'İstanbul',
        'rating' => 4.9,
        'review_count' => 245,
        'phone' => '0212 444 55 66',
        'description' => 'Lüks pet güzellik ve bakım merkezi',
        'opens_at' => '10:00',
        'closes_at' => '20:00',
        'image' => 'static/img/salon1.jpg',
        'services' => ['Premium Kedi Bakımı', 'Premium Köpek Bakımı', 'Spa Terapisi']
    ],
    [
        'id' => 5,
        'name' => 'Golden Paws Grooming',
        'address' => 'Acıbadem Caddesi No:89',
        'district' => 'Üsküdar',
        'city' => 'İstanbul',
        'rating' => 4.8,
        'review_count' => 167,
        'phone' => '0216 777 88 99',
        'description' => 'Evcil dostlarınız için premium bakım hizmetleri',
        'opens_at' => '09:00',
        'closes_at' => '19:00',
        'image' => 'static/img/salon2.jpg',
        'services' => ['Kedi Bakımı', 'Köpek Bakımı', 'Yıkama & Bakım']
    ]
];

?>

<main class="py-5">
    <div class="container">
        <!-- Search Section -->
        <div class="card shadow-sm mb-5">
            <div class="card-body p-4">
                <h4 class="mb-4">Pet Kuaförü Bul</h4>
                <form action="" method="get" class="row g-3" id="searchForm">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-map-marker-alt text-muted"></i>
                            </span>
                            <input type="text" name="location" id="location" class="form-control border-start-0" 
                                   placeholder="Şehir">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-building text-muted"></i>
                            </span>
                            <select name="district" id="district" class="form-select border-start-0">
                                <option value="Tüm Mahalleler">Tüm Mahalleler</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Kuaför Bul</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Section -->
        <div class="row" id="salonResults">
            <?php foreach ($salons as $salon): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?php echo $salon['image']; ?>" class="card-img-top" alt="<?php echo $salon['name']; ?>" 
                             style="height: 250px; object-fit: cover; border-radius: 15px;">
                        <div class="card-body px-0">
                            <h5 class="card-title mb-3"><?php echo $salon['name']; ?></h5>
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <span class="ms-1 fw-bold"><?php echo number_format($salon['rating'], 1); ?></span>
                                </div>
                                <span class="text-muted">(<?php echo $salon['review_count']; ?> değerlendirme)</span>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <?php foreach ($salon['services'] as $service): ?>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <?php echo $service; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="d-flex justify-content-between align-items-center text-muted mb-3">
                                <small><i class="fas fa-map-marker-alt me-2"></i><?php echo $salon['district']; ?></small>
                                <small><i class="fas fa-clock me-2"></i><?php echo $salon['opens_at']; ?> - <?php echo $salon['closes_at']; ?></small>
                            </div>
                            <a href="salon_detail.php?id=<?php echo $salon['id']; ?>" class="btn btn-primary w-100 rounded-pill">
                                Randevu Al
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<script>
// Update districts based on selected city
document.getElementById('location').addEventListener('change', function() {
    const city = this.value.toLowerCase();
    const districtSelect = document.getElementById('district');

    if (city === 'istanbul') {
        fetch('/get_districts.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ city: city })
        })
        .then(response => response.json())
        .then(data => {
            districtSelect.innerHTML = `<option value="Tüm Mahalleler">Tüm Mahalleler</option>` +
                data.districts.map(district => 
                    `<option value="${district}">${district}</option>`
                ).join('');
        })
        .catch(error => console.error('Error:', error));
    }
});
</script>

<?php require_once 'footer.php'; ?>