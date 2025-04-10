    <!-- Ana İçerik Sonu -->
    
    <!-- Footer -->
    <footer class="footer mt-5 py-5 bg-black text-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <h5 class="mb-4 fw-bold"><span class="pet-blue">Pet</span><span class="pet-teal">Kuaför</span></h5>
                    <p class="text-muted mb-4">Evcil dostlarınızın bakımı için profesyonel kuaförlük ve bakım hizmetleri. Size en yakın pet kuaför salonlarını hemen keşfedin.</p>
                    <div class="d-flex gap-3 pt-2">
                        <a href="#" class="text-muted fs-5"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-muted fs-5"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-muted fs-5"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-muted fs-5"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5 class="mb-4 fw-bold">Hızlı Erişim</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a href="<?php echo url('home'); ?>" class="nav-link p-0 text-muted">Ana Sayfa</a></li>
                        <li class="nav-item mb-2"><a href="<?php echo url('services'); ?>" class="nav-link p-0 text-muted">Hizmetlerimiz</a></li>
                        <li class="nav-item mb-2"><a href="<?php echo url('salons'); ?>" class="nav-link p-0 text-muted">Randevu Al</a></li>
                        <li class="nav-item mb-2"><a href="<?php echo url('about'); ?>" class="nav-link p-0 text-muted">Hakkımızda</a></li>
                        <li class="nav-item mb-2"><a href="<?php echo url('contact'); ?>" class="nav-link p-0 text-muted">İletişim</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5 class="mb-4 fw-bold">Hizmetlerimiz</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Köpek Bakımı</a></li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Kedi Bakımı</a></li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Tırnak Kesimi</a></li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Banyo & Yıkama</a></li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Fön & Tıraş</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="mb-4 fw-bold">İletişim</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-3">
                            <p class="m-0 text-muted">
                                <i class="fas fa-map-marker-alt me-2 text-pet-teal"></i> 
                                Örnek Mahallesi, Örnek Caddesi No:123, İstanbul
                            </p>
                        </li>
                        <li class="nav-item mb-3">
                            <p class="m-0 text-muted">
                                <i class="fas fa-phone-alt me-2 text-pet-teal"></i> 
                                0212 123 45 67
                            </p>
                        </li>
                        <li class="nav-item mb-3">
                            <p class="m-0 text-muted">
                                <i class="fas fa-envelope me-2 text-pet-teal"></i> 
                                info@petkuafor.com
                            </p>
                        </li>
                        <li class="nav-item">
                            <p class="m-0 text-muted">
                                <i class="fas fa-clock me-2 text-pet-teal"></i> 
                                Hafta içi: 09:00 - 19:00, Hafta sonu: 10:00 - 17:00
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="mb-md-0 text-muted">&copy; <?php echo date('Y'); ?> PetKuaför. Tüm hakları saklıdır.</p>
                <ul class="nav">
                    <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Gizlilik Politikası</a></li>
                    <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Kullanım Şartları</a></li>
                    <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Çerez Politikası</a></li>
                </ul>
            </div>
        </div>
    </footer>
    
    <!-- Türkiye'deki şehirler listesi -->
    <script>
    const CITIES_LIST = [
        "Adana", "Adıyaman", "Afyonkarahisar", "Ağrı", "Amasya", "Ankara", "Antalya", "Artvin", "Aydın", "Balıkesir",
        "Bilecik", "Bingöl", "Bitlis", "Bolu", "Burdur", "Bursa", "Çanakkale", "Çankırı", "Çorum", "Denizli", "Diyarbakır",
        "Edirne", "Elazığ", "Erzincan", "Erzurum", "Eskişehir", "Gaziantep", "Giresun", "Gümüşhane", "Hakkari", "Hatay",
        "Isparta", "Mersin", "İstanbul", "İzmir", "Kars", "Kastamonu", "Kayseri", "Kırklareli", "Kırşehir", "Kocaeli",
        "Konya", "Kütahya", "Malatya", "Manisa", "Kahramanmaraş", "Mardin", "Muğla", "Muş", "Nevşehir", "Niğde", "Ordu",
        "Rize", "Sakarya", "Samsun", "Siirt", "Sinop", "Sivas", "Tekirdağ", "Tokat", "Trabzon", "Tunceli", "Şanlıurfa",
        "Uşak", "Van", "Yozgat", "Zonguldak", "Aksaray", "Bayburt", "Karaman", "Kırıkkale", "Batman", "Şırnak", "Bartın",
        "Ardahan", "Iğdır", "Yalova", "Karabük", "Kilis", "Osmaniye", "Düzce"
    ];
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="static/js/script.js"></script>
    
    <?php if (get_current_page() === 'home'): ?>
    <!-- Sadece anasayfada konum izni JavaScript'i -->
    <script src="static/js/location-permission-real.js"></script>
    <script src="static/js/cities.js"></script>
    <?php endif; ?>
    
    <?php if (in_array(get_current_page(), ['salons', 'salon_detail'])): ?>
    <!-- Salon arama sayfalarında gereken JS -->
    <script src="static/js/cities.js"></script>
    <?php endif; ?>
    
    <?php if (get_current_page() === 'book_appointment'): ?>
    <!-- Randevu alma sayfasında gereken JS -->
    <script src="static/js/booking.js"></script>
    <?php endif; ?>
</body>
</html>