
<?php
session_start();
require_once 'header.php';
?>

<main>
    <section class="hero-section position-relative">
        <div class="container">
            <div class="row align-items-center min-vh-85">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Evcil Hayvanınız İçin<br>En İyi Bakım</h1>
                    <p class="lead mb-4">Profesyonel kuaförlerimiz ile evcil hayvanınıza özel bakım hizmetleri sunuyoruz.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-primary btn-lg px-4" data-bs-toggle="modal" data-bs-target="#loginModal">Giriş Yap</a>
                        <a href="#" class="btn btn-outline-primary btn-lg px-4" data-bs-toggle="modal" data-bs-target="#registerModal">Üye Ol</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
require_once 'footer.php';
?>
