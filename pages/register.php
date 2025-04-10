<?php
// Kayıt sayfası

// Eğer kullanıcı zaten giriş yapmışsa ana sayfaya yönlendir
if (is_logged_in()) {
    redirect('home');
}

// Form gönderilmişse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    // Doğrulama
    $errors = [];
    
    if (empty($username)) {
        $errors['username'] = 'Kullanıcı adı gereklidir';
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $errors['username'] = 'Kullanıcı adı 3-20 karakter arasında olmalıdır';
    } else {
        // Kullanıcı adı benzersiz mi kontrol et
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $errors['username'] = 'Bu kullanıcı adı zaten kullanılıyor';
        }
    }
    
    if (empty($email)) {
        $errors['email'] = 'E-posta adresi gereklidir';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Geçerli bir e-posta adresi giriniz';
    } else {
        // E-posta benzersiz mi kontrol et
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors['email'] = 'Bu e-posta adresi zaten kullanılıyor';
        }
    }
    
    if (empty($password)) {
        $errors['password'] = 'Şifre gereklidir';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Şifre en az 6 karakter olmalıdır';
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Şifreler eşleşmiyor';
    }
    
    if (empty($first_name)) {
        $errors['first_name'] = 'Ad gereklidir';
    }
    
    if (empty($last_name)) {
        $errors['last_name'] = 'Soyad gereklidir';
    }
    
    if (empty($phone)) {
        $errors['phone'] = 'Telefon numarası gereklidir';
    }
    
    // Hata yoksa kayıt işlemini yap
    if (empty($errors)) {
        // Şifreyi hash'le
        $password_hash = hash_password($password);
        
        // Kullanıcıyı veritabanına ekle
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, phone, role) VALUES (?, ?, ?, ?, ?, ?, 'customer')");
        $result = $stmt->execute([$username, $email, $password_hash, $first_name, $last_name, $phone]);
        
        if ($result) {
            // Başarılı mesajı belirle
            set_flash_message('success', 'Kayıt işlemi başarılı! Şimdi giriş yapabilirsiniz.');
            
            // Giriş sayfasına yönlendir
            redirect('login');
        } else {
            // Hata mesajı belirle
            $errors['general'] = 'Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyin.';
        }
    }
}
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <h1 class="h3 fw-bold text-pet-blue mb-2">Kayıt Ol</h1>
                            <p class="text-muted">Pet Kuaför dünyasına hoş geldiniz!</p>
                        </div>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $errors['general']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo url('register'); ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label fw-medium">Ad</label>
                                    <input type="text" class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" id="first_name" name="first_name" value="<?php echo isset($first_name) ? escape($first_name) : ''; ?>">
                                    <?php if (isset($errors['first_name'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['first_name']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label fw-medium">Soyad</label>
                                    <input type="text" class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" id="last_name" name="last_name" value="<?php echo isset($last_name) ? escape($last_name) : ''; ?>">
                                    <?php if (isset($errors['last_name'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['last_name']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label fw-medium">Kullanıcı Adı</label>
                                <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo isset($username) ? escape($username) : ''; ?>">
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['username']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium">E-posta Adresi</label>
                                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo isset($email) ? escape($email) : ''; ?>">
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['email']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-medium">Telefon Numarası</label>
                                <input type="text" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" id="phone" name="phone" value="<?php echo isset($phone) ? escape($phone) : ''; ?>" placeholder="05XX XXX XX XX">
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['phone']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium">Şifre</label>
                                <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password">
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['password']; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text text-muted small">En az 6 karakter olmalıdır</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label fw-medium">Şifre Tekrar</label>
                                <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password">
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['confirm_password']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <button type="submit" class="btn bg-pet-blue text-white w-100 py-2 mb-3">Kayıt Ol</button>
                            
                            <div class="text-center mt-3">
                                <p class="small text-muted mb-0">Zaten hesabınız var mı? <a href="<?php echo url('login'); ?>" class="text-pet-blue fw-medium">Giriş Yapın</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>