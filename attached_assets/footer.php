    </main>

    <!-- Alt Bilgi / Footer -->
    <footer class="bg-gray-800 text-white py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo ve Kısa Açıklama -->
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center mb-4">
                        <span class="text-2xl font-bold text-white">Pet<span class="text-pet-teal">Kuaför</span></span>
                    </div>
                    <p class="text-gray-300 text-sm">
                        Evcil hayvanınız için profesyonel bakım ve kuaför hizmetleri sunan platformumuzda, sevimli dostunuza en iyi hizmeti vermekten gurur duyuyoruz.
                    </p>
                    <div class="flex space-x-4 mt-6">
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Hızlı Linkler -->
                <div class="col-span-1">
                    <h3 class="text-lg font-semibold mb-4 text-pet-teal">Hızlı Linkler</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-300 hover:text-white transition-colors">Ana Sayfa</a></li>
                        <li><a href="/#services" class="text-gray-300 hover:text-white transition-colors">Hizmetlerimiz</a></li>
                        <li><a href="/#booking" class="text-gray-300 hover:text-white transition-colors">Randevu Al</a></li>
                        <li><a href="/pages/about.php" class="text-gray-300 hover:text-white transition-colors">Hakkımızda</a></li>
                        <li><a href="/pages/contact.php" class="text-gray-300 hover:text-white transition-colors">İletişim</a></li>
                    </ul>
                </div>

                <!-- Hizmetler -->
                <div class="col-span-1">
                    <h3 class="text-lg font-semibold mb-4 text-pet-pink">Hizmetlerimiz</h3>
                    <ul class="space-y-2">
                        <li><a href="/#services" class="text-gray-300 hover:text-white transition-colors">Temel Bakım Paketi</a></li>
                        <li><a href="/#services" class="text-gray-300 hover:text-white transition-colors">Premium Bakım</a></li>
                        <li><a href="/#services" class="text-gray-300 hover:text-white transition-colors">Köpek Kuaförü</a></li>
                        <li><a href="/#services" class="text-gray-300 hover:text-white transition-colors">Kedi Bakımı</a></li>
                        <li><a href="/#services" class="text-gray-300 hover:text-white transition-colors">Özel Irklar İçin Bakım</a></li>
                    </ul>
                </div>

                <!-- İletişim Bilgileri -->
                <div class="col-span-1">
                    <h3 class="text-lg font-semibold mb-4 text-pet-yellow">İletişim</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-pet-teal"></i>
                            <span>Bahçelievler Mah. Atatürk Cad. No:123, İstanbul, Türkiye</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3 text-pet-teal"></i>
                            <span>+90 (212) 123 45 67</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-pet-teal"></i>
                            <span>info@petkuafor.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3 text-pet-teal"></i>
                            <span>Pazartesi - Cumartesi: 09:00 - 19:00</span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="my-8 border-gray-700">

            <!-- Alt Footer -->
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-400">
                    &copy; <?php echo date("Y"); ?> PetKuaför - Tüm Hakları Saklıdır.
                </p>
                <div class="mt-4 md:mt-0">
                    <ul class="flex space-x-6">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-gray-300">Gizlilik Politikası</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-gray-300">Kullanım Şartları</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-gray-300">Çerez Politikası</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript Dosyaları -->
    <script src="/assets/js/main.js"></script>
    <script>
        // Mobil menü toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>