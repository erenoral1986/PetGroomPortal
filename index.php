
<?php
session_start();

// Sample data
$salons = [
    [
        'id' => 1,
        'name' => 'Happy Paws Pet Salon',
        'address' => 'Bağdat Caddesi No:123',
        'city' => 'İstanbul',
        'district' => 'Kadıköy',
        'rating' => 4.8,
        'review_count' => 156,
        'phone' => '0216 123 45 67',
        'description' => 'Profesyonel pet kuaför hizmetleri',
        'opens_at' => '09:00',
        'closes_at' => '19:00'
    ],
    [
        'id' => 2,
        'name' => 'Pati Pet Grooming',
        'address' => 'Nispetiye Caddesi No:45',
        'city' => 'İstanbul',
        'district' => 'Beşiktaş',
        'rating' => 4.6,
        'review_count' => 98,
        'phone' => '0212 345 67 89',
        'description' => 'Uzman kadromuzla evcil dostlarınıza özel bakım',
        'opens_at' => '10:00',
        'closes_at' => '20:00'
    ]
];

require_once 'header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-85">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Evcil Hayvanınız İçin<br>En İyi Bakım</h1>
                    <p class="lead mb-4">Profesyonel kuaförlerimiz ile evcil hayvanınıza özel bakım hizmetleri sunuyoruz.</p>
                    <div class="d-flex gap-3">
                        <a href="salons.php" class="btn btn-primary btn-lg px-4">Kuaför Bul</a>
                        <a href="services.php" class="btn btn-outline-primary btn-lg px-4">Hizmetleri İncele</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="static/img/pet-grooming.jpg" alt="Pet Grooming" class="img-fluid rounded-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="text-center mb-4">Size En Yakın Pet Kuaförlerini Bulun</h5>
                            <form action="salons.php" method="get">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            <input type="text" class="form-control" name="location" placeholder="Şehir">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                                            <select class="form-select" name="district">
                                                <option value="">Tüm Mahalleler</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary w-100">Kuaför Bul</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Hizmetlerimiz</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card service-card">
                        <div class="card-body text-center">
                            <i class="fas fa-bath fa-3x mb-3 text-primary"></i>
                            <h5 class="card-title">Yıkama & Bakım</h5>
                            <p class="card-text">Evcil dostunuz için profesyonel yıkama ve bakım hizmetleri</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card">
                        <div class="card-body text-center">
                            <i class="fas fa-cut fa-3x mb-3 text-primary"></i>
                            <h5 class="card-title">Tıraş</h5>
                            <p class="card-text">Uzman kuaförlerimizle evcil dostunuza özel tıraş hizmetleri</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card">
                        <div class="card-body text-center">
                            <i class="fas fa-spa fa-3x mb-3 text-primary"></i>
                            <h5 class="card-title">Spa & Masaj</h5>
                            <p class="card-text">Evcil dostunuz için rahatlatıcı spa ve masaj hizmetleri</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'footer.php'; ?>
