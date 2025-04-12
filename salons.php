<?php
require_once 'header.php';

// Sample salon data as PHP array
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
    ]
];

// Filter and sort logic
$location = isset($_GET['location']) ? $_GET['location'] : '';
$district = isset($_GET['district']) ? $_GET['district'] : '';

if (!empty($location) || !empty($district)) {
    $salons = array_filter($salons, function($salon) use ($location, $district) {
        $locationMatch = empty($location) || strtolower($salon['city']) === strtolower($location);
        $districtMatch = empty($district) || $district === 'Tüm Mahalleler' || 
                        stripos($salon['district'], $district) !== false;
        return $locationMatch && $districtMatch;
    });
}

// Sort by rating
usort($salons, function($a, $b) {
    return $b['rating'] <=> $a['rating'];
});

// Convert filtered salons to JSON for JavaScript
$salonsJson = json_encode($salons);
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
                                   placeholder="Şehir" value="<?php echo htmlspecialchars($location); ?>">
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
            <!-- Salon cards will be populated by JavaScript -->
        </div>
    </div>
</main>

<script>
// Store salons data from PHP
const salons = <?php echo $salonsJson; ?>;

// Render salon cards
function renderSalons(salonsData) {
    const resultsContainer = document.getElementById('salonResults');

    if (salonsData.length === 0) {
        resultsContainer.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                <h4 class="text-muted">Kuaför bulunamadı</h4>
                <p>Farklı bir konum için arama yapabilir veya tüm kuaförleri görebilirsiniz.</p>
                <a href="salons.php" class="btn btn-outline-primary mt-2">Tüm Kuaförleri Gör</a>
            </div>`;
        return;
    }

    resultsContainer.innerHTML = salonsData.map(salon => `
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0">${salon.name}</h5>
                        <span class="badge bg-primary">${salon.rating.toFixed(1)}</span>
                    </div>
                    <div class="mb-2">
                        ${Array(Math.floor(salon.rating)).fill('<i class="fas fa-star text-warning"></i>').join('')}
                        ${salon.rating % 1 >= 0.5 ? '<i class="fas fa-star-half-alt text-warning"></i>' : ''}
                        <span class="ms-1 text-muted">(${salon.review_count})</span>
                    </div>
                    <p class="card-text">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>${salon.address}
                    </p>
                    <p class="card-text">
                        <i class="fas fa-clock text-primary me-2"></i>${salon.opens_at} - ${salon.closes_at}
                    </p>
                    <div class="mb-3">
                        <small class="text-muted">Hizmetler:</small><br>
                        ${salon.services.map(service => 
                            `<span class="badge bg-light text-dark me-1">${service}</span>`
                        ).join('')}
                    </div>
                    <a href="salon_detail.php?id=${salon.id}" class="btn btn-outline-primary w-100">
                        Detayları Gör
                    </a>
                </div>
            </div>
        </div>
    `).join('');
}

// Initial render
renderSalons(salons);

// Update districts based on selected city
document.getElementById('location').addEventListener('change', function() {
    const city = this.value.trim();
    const districtSelect = document.getElementById('district');

    if (city.toLowerCase() === 'istanbul') {
        fetch('/get_districts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ city: city })
        })
        .then(response => response.json())
        .then(data => {
            districtSelect.innerHTML = data.districts.map(district => 
                `<option value="${district}">${district}</option>`
            ).join('');
        })
        .catch(error => console.error('Error:', error));
    }
});
</script>

<?php require_once 'footer.php'; ?>