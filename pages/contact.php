<?php
// İletişim sayfası

// Form gönderildi mi kontrolü
$submitted = false;
$form_errors = [];

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Basit doğrulama
    if (empty($name)) {
        $form_errors['name'] = 'Ad Soyad gereklidir';
    }
    
    if (empty($email)) {
        $form_errors['email'] = 'E-posta adresi gereklidir';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_errors['email'] = 'Geçerli bir e-posta adresi giriniz';
    }
    
    if (empty($subject)) {
        $form_errors['subject'] = 'Konu gereklidir';
    }
    
    if (empty($message)) {
        $form_errors['message'] = 'Mesaj gereklidir';
    }
    
    // Hata yoksa mesajı kaydet/gönder
    if (empty($form_errors)) {
        // Gerçek uygulamada burada e-posta gönderme veya veritabanına kaydetme işlemi yapılır
        
        // Form başarıyla gönderildi olarak işaretle
        $submitted = true;
        
        // Formu temizle
        $name = $email = $subject = $message = '';
    }
}
?>

<!-- Page Header -->
<div class="bg-dark py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="fw-bold text-white mb-3">İletişim</h1>
                <p class="lead text-muted">Sorularınız ve önerileriniz için bizimle iletişime geçebilirsiniz.</p>
            </div>
        </div>
    </div>
</div>

<!-- Contact Section -->
<div class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Info -->
            <div class="col-lg-4">
                <h2 class="h4 fw-bold mb-4">İletişim Bilgileri</h2>
                
                <div class="mb-4">
                    <h3 class="h6 fw-bold">Adres</h3>
                    <p class="text-muted">
                        Pet Kuaför A.Ş.<br>
                        Örnek Mahallesi, Örnek Caddesi No:123<br>
                        Ataşehir, İstanbul
                    </p>
                </div>
                
                <div class="mb-4">
                    <h3 class="h6 fw-bold">Bize Ulaşın</h3>
                    <p class="text-muted mb-2">
                        <i class="fas fa-phone me-2 text-pet-blue"></i> +90 212 123 45 67
                    </p>
                    <p class="text-muted mb-2">
                        <i class="fas fa-envelope me-2 text-pet-blue"></i> info@petkuafor.com
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-globe me-2 text-pet-blue"></i> www.petkuafor.com
                    </p>
                </div>
                
                <div class="mb-4">
                    <h3 class="h6 fw-bold">Çalışma Saatleri</h3>
                    <p class="text-muted mb-1">
                        <span class="fw-medium">Pazartesi - Cuma:</span> 09:00 - 18:00
                    </p>
                    <p class="text-muted">
                        <span class="fw-medium">Cumartesi:</span> 10:00 - 15:00
                    </p>
                </div>
                
                <div class="mb-4">
                    <h3 class="h6 fw-bold">Bizi Takip Edin</h3>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-muted fs-5"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-muted fs-5"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-muted fs-5"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-muted fs-5"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-8">
                <?php if ($submitted): ?>
                    <div class="alert alert-success mb-4" role="alert">
                        <h4 class="alert-heading"><i class="fas fa-check-circle me-2"></i> Teşekkürler!</h4>
                        <p>Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.</p>
                    </div>
                <?php else: ?>
                    <form action="<?php echo url('contact'); ?>" method="POST" class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-lg-5">
                            <h2 class="h4 fw-bold mb-4">Bize Mesaj Gönderin</h2>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Ad Soyad</label>
                                        <input type="text" class="form-control <?php echo isset($form_errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo isset($name) ? escape($name) : ''; ?>">
                                        <?php if (isset($form_errors['name'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo $form_errors['name']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">E-posta Adresi</label>
                                        <input type="email" class="form-control <?php echo isset($form_errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo isset($email) ? escape($email) : ''; ?>">
                                        <?php if (isset($form_errors['email'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo $form_errors['email']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="subject" class="form-label">Konu</label>
                                        <input type="text" class="form-control <?php echo isset($form_errors['subject']) ? 'is-invalid' : ''; ?>" id="subject" name="subject" value="<?php echo isset($subject) ? escape($subject) : ''; ?>">
                                        <?php if (isset($form_errors['subject'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo $form_errors['subject']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="mb-4">
                                        <label for="message" class="form-label">Mesajınız</label>
                                        <textarea class="form-control <?php echo isset($form_errors['message']) ? 'is-invalid' : ''; ?>" id="message" name="message" rows="5"><?php echo isset($message) ? escape($message) : ''; ?></textarea>
                                        <?php if (isset($form_errors['message'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo $form_errors['message']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <button type="submit" class="btn bg-pet-blue text-white px-4 py-2">
                                        <i class="fas fa-paper-plane me-2"></i> Mesaj Gönder
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Map Section -->
<div class="py-5 bg-dark">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="h4 fw-bold text-white mb-4">Bizi Ziyaret Edin</h2>
                
                <div class="card border-0 shadow">
                    <div class="card-body p-0">
                        <div class="ratio ratio-21x9" style="min-height: 400px;">
                            <!-- Harita görüntüsü (gerçek uygulamada Google Maps veya başka bir harita servisi entegre edilecektir) -->
                            <img src="https://maps.googleapis.com/maps/api/staticmap?center=Istanbul,Turkey&zoom=13&size=600x300&maptype=roadmap&key=YOUR_API_KEY" alt="Ofis Konumu" class="w-100 h-100" style="object-fit: cover;">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-75">
                                <div class="text-center p-4">
                                    <i class="fas fa-map-marked-alt fa-3x text-pet-teal mb-3"></i>
                                    <h3 class="text-white">Harita Görüntüsü</h3>
                                    <p class="text-muted">Gerçek uygulamada ofis konumumuz interaktif haritada gösterilecektir.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold">Sıkça Sorulan Sorular</h2>
                <p class="text-muted">Sıkça sorulan soruların cevaplarını burada bulabilirsiniz.</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="accordion" id="contactFaq">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">
                                Salon sahibiyim, platformunuza nasıl katılabilirim?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#contactFaq">
                            <div class="accordion-body">
                                <p class="mb-0">Salonunuzu platformumuza eklemek için iletişim formunu doldurarak bize ulaşabilirsiniz. Ekibimiz en kısa sürede sizinle iletişime geçecek ve kayıt sürecini başlatacaktır. Kayıt süreci genellikle 3-5 iş günü içerisinde tamamlanmaktadır.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Randevumu nasıl iptal edebilirim?
                            </button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#contactFaq">
                            <div class="accordion-body">
                                <p class="mb-0">Randevunuzu, hesap sayfanızdaki "Randevularım" bölümünden kolayca iptal edebilirsiniz. Randevunuzu, randevu saatinden en az 4 saat öncesine kadar iptal etmeniz durumunda herhangi bir ücret ödemezsiniz. Daha geç iptaller için salon politikalarına göre ücret yansıtılabilir.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Platformunuzdaki hizmet ücretleri güncel mi?
                            </button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#contactFaq">
                            <div class="accordion-body">
                                <p class="mb-0">Platformumuzda görünen fiyatlar, salon sahipleri tarafından güncel tutulmaktadır. Ancak, salon sahibi tarafından fiyat güncellemesi yapılmış ve henüz sistemimize yansımamış olabilir. Bu nedenle, randevu oluşturmadan önce fiyatların güncel olduğunu salon ile doğrulamanızı öneririz.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Şikayetimi nasıl bildirebilirim?
                            </button>
                        </h3>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#contactFaq">
                            <div class="accordion-body">
                                <p class="mb-0">Aldığınız hizmetle ilgili bir şikayetiniz varsa, bize iletişim formu üzerinden veya info@petkuafor.com e-posta adresi üzerinden ulaşabilirsiniz. Şikayetiniz 48 saat içerisinde değerlendirilip, gerekli işlemler başlatılacaktır. Müşteri memnuniyeti bizim için en önemli önceliktir.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>