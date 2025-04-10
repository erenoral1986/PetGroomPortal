<?php
// Hizmetler sayfası

// Hizmet kategorilerini al
$service_categories = [
    'basic' => [
        'name' => 'Temel Bakım Hizmetleri',
        'description' => 'Evcil dostunuzun temel bakım ihtiyaçları için profesyonel hizmetler.',
        'icon' => 'paw',
        'services' => [
            [
                'name' => 'Köpek Temel Banyo',
                'price' => '150 ₺',
                'description' => 'Irka özel şampuan ve bakım ürünleriyle yıkama, kurutma, tüy bakımı, kulak temizliği.',
                'duration' => '60 dk',
                'pet_type' => 'dog'
            ],
            [
                'name' => 'Kedi Temel Banyo',
                'price' => '120 ₺',
                'description' => 'Hassas ciltli kediler için özel formüllü şampuan ile yıkama, nazik kurutma ve tarama.',
                'duration' => '45 dk',
                'pet_type' => 'cat'
            ],
            [
                'name' => 'Tırnak Kesimi',
                'price' => '50 ₺',
                'description' => 'Ağrısız ve güvenli tırnak kesimi hizmeti, kedi ve köpekler için uygundur.',
                'duration' => '15 dk',
                'pet_type' => 'both'
            ],
            [
                'name' => 'Kulak Temizliği',
                'price' => '45 ₺',
                'description' => 'Profesyonel kulak temizliği ve bakımı, enfeksiyon riskini azaltır.',
                'duration' => '15 dk',
                'pet_type' => 'both'
            ]
        ]
    ],
    'grooming' => [
        'name' => 'Tıraş ve Şekillendirme',
        'description' => 'Evcil dostunuzun ırkına ve ihtiyaçlarına özel profesyonel tıraş ve şekillendirme hizmetleri.',
        'icon' => 'cut',
        'services' => [
            [
                'name' => 'Köpek Tam Tıraş',
                'price' => '220 ₺',
                'description' => 'Komple vücut tıraşı, yıkama, kurutma ve şekillendirme.',
                'duration' => '90 dk',
                'pet_type' => 'dog'
            ],
            [
                'name' => 'Köpek Bölgesel Tıraş',
                'price' => '150 ₺',
                'description' => 'Yüz, patiler ve tuvalet bölgesi tıraşı.',
                'duration' => '45 dk',
                'pet_type' => 'dog'
            ],
            [
                'name' => 'Kedi Tüy Şekillendirme',
                'price' => '180 ₺',
                'description' => 'Uzun tüylü kediler için tüy düzenleme ve şekillendirme.',
                'duration' => '60 dk',
                'pet_type' => 'cat'
            ],
            [
                'name' => 'Irka Özel Tıraş',
                'price' => '250 ₺',
                'description' => 'Yorkshire, Maltese, Shih Tzu gibi ırklar için özel tıraş modelleri.',
                'duration' => '120 dk',
                'pet_type' => 'dog'
            ]
        ]
    ],
    'special' => [
        'name' => 'Özel Bakım Paketleri',
        'description' => 'Evcil dostunuz için ekstra bakım içeren premium hizmet paketleri.',
        'icon' => 'gem',
        'services' => [
            [
                'name' => 'VIP Köpek Bakımı',
                'price' => '350 ₺',
                'description' => 'Premium şampuan, kondisyoner, parfüm, tırnak bakımı, kulak temizliği, diş bakımı ve masaj.',
                'duration' => '150 dk',
                'pet_type' => 'dog'
            ],
            [
                'name' => 'VIP Kedi Bakımı',
                'price' => '300 ₺',
                'description' => 'Hassas cilt için özel bakım, yumak giderme, tırnak ve kulak bakımı, masaj.',
                'duration' => '120 dk',
                'pet_type' => 'cat'
            ],
            [
                'name' => 'Mevsimsel Tüy Bakımı',
                'price' => '280 ₺',
                'description' => 'Mevsim geçişlerinde tüy dökümünü azaltan özel tüy bakımı ve tarama.',
                'duration' => '90 dk',
                'pet_type' => 'both'
            ],
            [
                'name' => 'Yaşlı Pet Bakımı',
                'price' => '270 ₺',
                'description' => 'Yaşlı evcil hayvanlar için ekstra nazik ve özel bakım paketi.',
                'duration' => '90 dk',
                'pet_type' => 'both'
            ]
        ]
    ],
    'health' => [
        'name' => 'Sağlık ve Hijyen',
        'description' => 'Evcil dostunuzun sağlığını koruyan özel hizmetler.',
        'icon' => 'heart',
        'services' => [
            [
                'name' => 'Parazit Kontrolü',
                'price' => '80 ₺',
                'description' => 'Kene, pire gibi dış parazitlerin kontrolü ve temizliği.',
                'duration' => '30 dk',
                'pet_type' => 'both'
            ],
            [
                'name' => 'Diş Bakımı',
                'price' => '90 ₺',
                'description' => 'Diş taşı temizliği ve ağız bakımı, kötü nefes önleme.',
                'duration' => '25 dk',
                'pet_type' => 'both'
            ],
            [
                'name' => 'Göz Bakımı',
                'price' => '45 ₺',
                'description' => 'Göz çevresi temizliği ve bakımı.',
                'duration' => '15 dk',
                'pet_type' => 'both'
            ],
            [
                'name' => 'Anal Bez Kontrolü',
                'price' => '60 ₺',
                'description' => 'Anal bezlerin kontrolü ve gerekirse boşaltılması.',
                'duration' => '15 dk',
                'pet_type' => 'both'
            ]
        ]
    ]
];
?>

<!-- Page Header -->
<div class="py-5 bg-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="fw-bold text-white mb-3">Pet Kuaför <span class="text-pet-teal">Hizmetlerimiz</span></h1>
                <p class="lead text-muted">Evcil dostunuzun bakımı için sunduğumuz profesyonel hizmetlerimizi keşfedin.</p>
            </div>
        </div>
    </div>
</div>

<!-- Services Section -->
<section class="py-5">
    <div class="container">
        <!-- Servis Kategorileri -->
        <?php foreach ($service_categories as $category): ?>
            <div class="mb-5">
                <div class="row align-items-center mb-4">
                    <div class="col-auto">
                        <div class="bg-pet-blue text-white p-3 rounded-circle">
                            <i class="fas fa-<?php echo $category['icon']; ?> fa-2x"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h2 class="h3 fw-bold mb-0"><?php echo $category['name']; ?></h2>
                        <p class="text-muted mb-0"><?php echo $category['description']; ?></p>
                    </div>
                </div>
                
                <div class="row g-4 mt-2">
                    <?php foreach ($category['services'] as $service): ?>
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h3 class="h5 fw-bold mb-0"><?php echo $service['name']; ?></h3>
                                        <div class="badge bg-pet-blue"><?php echo $service['price']; ?></div>
                                    </div>
                                    <p class="text-muted mb-3"><?php echo $service['description']; ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="d-flex align-items-center text-muted small">
                                                <i class="far fa-clock me-1"></i> <?php echo $service['duration']; ?>
                                            </span>
                                        </div>
                                        <div>
                                            <?php if ($service['pet_type'] === 'dog'): ?>
                                                <span class="badge bg-dark">Köpek</span>
                                            <?php elseif ($service['pet_type'] === 'cat'): ?>
                                                <span class="badge bg-dark">Kedi</span>
                                            <?php else: ?>
                                                <span class="badge bg-dark">Kedi & Köpek</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <!-- Randevu CTA -->
        <div class="text-center mt-5">
            <h3 class="mb-4">Hemen evcil dostunuz için randevu alın</h3>
            <a href="<?php echo url('salons'); ?>" class="btn bg-pet-blue text-white px-4 py-2 rounded-pill">
                <i class="fas fa-search me-2"></i> Salon Ara
            </a>
        </div>
    </div>
</section>

<!-- Info Section -->
<section class="py-5 bg-dark">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <h2 class="fw-bold text-white mb-4">Sıkça Sorulan Sorular</h2>
                
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item bg-dark border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button bg-dark text-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">
                                Hangi ırk köpekler için hizmet veriyorsunuz?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Tüm köpek ırkları için hizmet vermekteyiz. Irk özelliklerine ve tüy yapısına göre bakım uygulamaları yapılmaktadır. Büyük ırk köpekler için (30 kg üzeri) bazı salonlarımızda ek ücret talep edilebilir.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item bg-dark border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button bg-dark text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Evcil hayvanımın aşıları olmak zorunda mı?
                            </button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Evcil hayvanınızın ve diğer müşterilerimizin hayvanlarının sağlığı için aşıların tam olması önemlidir. Özellikle karma aşı ve kuduz aşısının güncel olmasını rica ediyoruz. Aşı kartını randevu sırasında göstermeniz gerekecektir.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item bg-dark border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button bg-dark text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Agresif evcil hayvanlar için hizmet veriyor musunuz?
                            </button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Hafif ve orta dereceli huzursuz olan evcil hayvanlar için hizmet vermekteyiz. Ancak yüksek derecede agresif veya tehlikeli davranışlar sergileyen hayvanlar için özel bir değerlendirme yapılması gerekebilir. Randevu alırken bu durumu belirtmeniz önemlidir.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item bg-dark border-0">
                        <h3 class="accordion-header">
                            <button class="accordion-button bg-dark text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Ne kadar süre önceden randevu almam gerekiyor?
                            </button>
                        </h3>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Randevular salonların müsaitlik durumuna göre değişiklik gösterebilir. Hafta içi genellikle 1-2 gün önceden, hafta sonu için ise en az 3-4 gün önceden randevu almanızı öneririz. Bayram ve tatil dönemlerinde bu süre daha uzun olabilir.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <h2 class="fw-bold text-white mb-4">Hizmet Süreci</h2>
                
                <div class="card bg-dark border-start border-pet-teal border-4 mb-3 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <div class="bg-pet-teal rounded-circle p-2 text-white">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="h5 fw-bold text-white">1. Randevu Alın</h3>
                                <p class="text-muted mb-0">Size en yakın salonu bulun ve uygun bir tarih/saat için randevu alın.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-dark border-start border-pet-teal border-4 mb-3 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <div class="bg-pet-teal rounded-circle p-2 text-white">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="h5 fw-bold text-white">2. Hazırlıkları Yapın</h3>
                                <p class="text-muted mb-0">Aşı kartını hazırlayın, varsa sağlık sorunlarını not edin ve evcil hayvanınızı salona getirin.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-dark border-start border-pet-teal border-4 mb-3 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <div class="bg-pet-teal rounded-circle p-2 text-white">
                                    <i class="fas fa-hand-holding-heart"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="h5 fw-bold text-white">3. Hizmet Alın</h3>
                                <p class="text-muted mb-0">Profesyonel ekibimiz evcil hayvanınızın bakımını özenle gerçekleştirir.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-dark border-start border-pet-teal border-4 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <div class="bg-pet-teal rounded-circle p-2 text-white">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="h5 fw-bold text-white">4. Deneyiminizi Paylaşın</h3>
                                <p class="text-muted mb-0">Aldığınız hizmeti değerlendirin ve yorumlarınızı paylaşın.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>