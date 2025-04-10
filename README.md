# Pet Kuaför - Evcil Hayvan Bakım ve Kuaför Platformu

Evcil hayvan sahipleri için pet kuaför salonlarını bulma, inceleme ve online randevu alma platformu.

## Kurulum

### Gereksinimler
- PHP 7.4 veya üzeri
- MySQL veya MariaDB veritabanı
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

## Özellikler

- **Kullanıcı Yönetimi**: Kayıt, giriş, profil düzenleme
- **Salon Arama**: Konuma ve evcil hayvan türüne göre salon arama
- **Online Randevu**: Müsait zaman dilimlerine göre randevu oluşturma
- **Salon Yönetimi**: Salon sahipleri için hizmet ve randevu yönetimi
- **Konum Entegrasyonu**: Gerçek konuma göre salon önerisi
- **Responsive Tasarım**: Tüm cihazlarla uyumlu

## Sistem Mimarisi

### Dosya Yapısı
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

### Veritabanı Şeması
- `users`: Kullanıcı bilgileri
- `salons`: Salon bilgileri
- `services`: Salon hizmetleri
- `availability`: Salon müsait zamanları
- `appointments`: Randevular
- `reviews`: Kullanıcı yorumları

## Kullanılan Teknolojiler

- **Arka Uç**: PHP, MySQL
- **Ön Uç**: HTML, CSS, JavaScript, Bootstrap 5
- **API Entegrasyonları**: Nominatim (OpenStreetMap) için konum hizmetleri
- **JavaScript Kütüphaneleri**: Flatpickr (tarih/saat seçici)

## Notlar

- Sistem, PHPMyAdmin üzerinden veritabanı yönetimi için optimize edilmiştir.
- Konum izinleri sadece ana sayfada istenir ve yerel depolamada saklanır.
- Şifreler güvenli bir şekilde hash'lenir ve saklanır.
- Tüm veriler UTF-8 karakter seti kullanılarak saklanır.