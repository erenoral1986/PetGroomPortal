<?php
// Giriş yapma sayfası

// Eğer kullanıcı zaten giriş yapmışsa ana sayfaya yönlendir
if (is_logged_in()) {
    redirect('home');
}

// Form gönderilmişse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Basit doğrulama
    $errors = [];
    
    if (empty($email)) {
        $errors['email'] = 'E-posta adresi gereklidir';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Geçerli bir e-posta adresi giriniz';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Şifre gereklidir';
    }
    
    // Hata yoksa kullanıcıyı doğrula
    if (empty($errors)) {
        // Veritabanı sorgusunu hazırla
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // Kullanıcı var mı ve şifre doğru mu kontrol et
        if ($user && check_password($password, $user['password_hash'])) {
            // Oturum bilgilerini ayarla
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            
            // Beni hatırla seçeneği için çerez ayarla
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expires = time() + (86400 * 30); // 30 gün
                
                // Tokeni veritabanına kaydet
                $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires) VALUES (?, ?, ?)");
                $stmt->execute([$user['id'], $token, date('Y-m-d H:i:s', $expires)]);
                
                // Cookie ayarla
                setcookie('remember_token', $token, $expires, '/', '', false, true);
            }
            
            // Başarılı mesajı belirle
            set_flash_message('success', 'Başarıyla giriş yaptınız!');
            
            // Ana sayfaya yönlendir
            redirect('home');
        } else {
            // Hatalı giriş
            $errors['login'] = 'E-posta adresi veya şifre hatalı';
        }
    }
}
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <h1 class="h3 fw-bold text-pet-blue mb-2">Giriş Yap</h1>
                            <p class="text-muted">Hesabınıza giriş yaparak randevularınızı yönetin</p>
                        </div>
                        
                        <?php if (isset($errors['login'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $errors['login']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo url('login'); ?>">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium">E-posta Adresi</label>
                                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo isset($email) ? escape($email) : ''; ?>" placeholder="mail@example.com">
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['email']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label for="password" class="form-label fw-medium">Şifre</label>
                                    <a href="#" class="small text-pet-blue">Şifremi Unuttum</a>
                                </div>
                                <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="••••••••">
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['password']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember" <?php echo isset($remember) && $remember ? 'checked' : ''; ?>>
                                <label class="form-check-label small" for="remember">Beni Hatırla</label>
                            </div>
                            
                            <button type="submit" class="btn bg-pet-blue text-white w-100 py-2 mb-3">Giriş Yap</button>
                            
                            <div class="text-center mt-3">
                                <p class="small text-muted mb-0">Hesabınız yok mu? <a href="<?php echo url('register'); ?>" class="text-pet-blue fw-medium">Kayıt Olun</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>