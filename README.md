# Pet Kuaför - Evcil Hayvan Bakım ve Rezervasyon Platformu

![Pet Kuaför Logo](static/img/logo.png)

Pet Kuaför, evcil hayvan sahiplerinin kolayca ve hızlı bir şekilde yakınlarındaki kuaför salonlarını bulmalarını, karşılaştırmalarını ve online randevu almalarını sağlayan kapsamlı bir web platformudur.

## Proje Hakkında

Pet Kuaför, aşağıdaki özellikleri sunan entegre bir platformdur:

- **Konum Tabanlı Salon Arama**: Kullanıcılar, bulundukları konuma en yakın pet kuaför salonlarını kolayca bulabilirler.
- **İlçe/Mahalle Düzeyinde Filtreleme**: Şehir ve mahalle bazında detaylı arama yapma imkanı sunar.
- **Online Randevu Sistemi**: Kullanıcılar, müsait zaman dilimlerini görebilir ve anında randevu oluşturabilirler.
- **Salon Yönetim Paneli**: Salon sahipleri için özel yönetim arayüzü ile hizmet ve randevu yönetimi.
- **Kullanıcı Değerlendirmeleri**: Gerçek kullanıcı deneyimlerine dayalı salon değerlendirmeleri.
- **Gelişmiş Konum Entegrasyonu**: Tarayıcı tabanlı konum tespiti ve yakındaki salonların önerilmesi.

## Teknolojik Altyapı

Proje şu teknolojiler kullanılarak geliştirilmiştir:

- **Backend**: PHP 7.4+
- **Veritabanı**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework/Kütüphaneler**: Bootstrap 5, Font Awesome
- **Harita API**: OpenStreetMap (Nominatim)
- **Konum Servisleri**: Geolocation API

## Kurulum

### Gereksinimler
- PHP 7.4 veya üzeri
- MySQL/MariaDB veritabanı
- Web sunucusu (Apache, Nginx vb.)

### Adımlar

1. Proje dosyalarını web sunucunuza yükleyin.

2. `database_schema.sql` dosyasını kullanarak veritabanını oluşturun:
   - PHPMyAdmin'de yeni bir veritabanı oluşturun (varsayılan: `petkuafor_db`)
   - SQL sekmesine gidin ve `database_schema.sql` dosyasını içe aktarın

3. Veritabanı bağlantı ayarlarını `config/database.php` dosyasında düzenleyin:
   ```php
   $host = 'localhost'; // Veritabanı sunucunuz
   $db_name = 'petkuafor_db'; // Veritabanı adınız
   $username = 'root'; // Veritabanı kullanıcı adınız
   $password = ''; // Veritabanı şifreniz
   ```

4. Web sunucunuzu yapılandırın:
   - Apache için `.htaccess` dosyası hazırdır
   - Nginx için şu konfigurasyon gerekebilir:
     ```
     location / {
         try_files $uri $uri/ /index.php?$query_string;
     }
     ```

5. Sistemi test edin:
   - Varsayılan yönetici: admin@example.com / 123456
   - Varsayılan salon sahibi: salon1@example.com / 123456 
   - Varsayılan müşteri: user1@example.com / 123456

## Proje Yapısı

```
/
├── api/                  # API endpoint'leri
├── config/               # Yapılandırma dosyaları
├── includes/             # Yardımcı fonksiyonlar ve şablonlar
├── pages/                # Ana sayfa içerikleri
├── admin/                # Yönetim paneli sayfaları
├── static/               # Statik dosyalar (CSS, JS, resimler)
│   ├── css/
│   ├── js/
│   ├── img/
├── database_schema.sql   # Veritabanı şeması
├── index.php             # Ana giriş noktası
└── README.md             # Dokümantasyon
```

## Ana Özellikler

### 1. Salon Arama
- Konum tabanlı arama
- Şehir ve mahalle filtreleme
- Evcil hayvan türüne göre filtreleme (köpek, kedi, her ikisi)
- Harita entegrasyonu ile görsel sonuçlar

### 2. Hizmet İnceleme
- Kategorize edilmiş hizmet listesi
- Fiyat ve süre bilgileri
- Detaylı açıklamalar
- Salon değerlendirmeleri

### 3. Randevu Oluşturma
- Tarih ve saat seçimi
- Müsaitlik kontrolü
- Randevu onayı
- İptal ve değişiklik imkanı

### 4. Kullanıcı Profili
- Hesap yönetimi
- Randevu geçmişi
- Yaklaşan randevular
- Değerlendirme yapma

### 5. Salon Yönetimi
- Hizmet ekleme/düzenleme
- Çalışma saatleri ayarlama
- Randevu onaylama/reddetme
- İstatistikler görüntüleme

## Örnek API Endpointleri

- `api/get_districts.php?city=İstanbul`: Şehir için mahalle listesi
- `api/available_slots.php?salon_id=1&date=2023-10-15`: Salon için müsait saatler
- `api/search_salons.php?location=İstanbul&district=Kadıköy&pet_type=dog`: Salon arama

## Bağımlılıklar

- Bootstrap 5.3.2
- Font Awesome 6.4.0
- Flatpickr (Takvim bileşeni)
- OpenStreetMap Nominatim API

## Geliştirme İpuçları

- Yerel geliştirme için `index.php` üzerine şu satırları ekleyin:
  ```php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  ```

- Konum API'si ile test için tarayıcı konsolunda şu kodu çalıştırabilirsiniz:
  ```javascript
  localStorage.removeItem('locationPermissionGranted');
  ```

## İletişim ve Katkıda Bulunma

Proje ile ilgili sorularınız, önerileriniz veya katkılarınız için lütfen iletişime geçin.

## Lisans

Bu proje açık kaynaklıdır ve [MIT lisansı](LICENSE) ile lisanslanmıştır.

---

&copy; 2025 Pet Kuaför. Tüm hakları saklıdır.