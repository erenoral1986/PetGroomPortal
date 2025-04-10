-- Pet Kuaför Veritabanı Şeması
-- PHPMyAdmin için uygun SQL formatında

-- Veritabanını oluştur
CREATE DATABASE IF NOT EXISTS `petkuafor_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `petkuafor_db`;

-- Kullanıcılar tablosu
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(256) NOT NULL,
  `first_name` varchar(64) DEFAULT NULL,
  `last_name` varchar(64) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'customer',
  `date_joined` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `salon_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Remember Me tokenleri tablosu
CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Salon tablosu
CREATE TABLE `salons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` varchar(200) NOT NULL,
  `city` varchar(100) NOT NULL,
  `zip_code` varchar(20) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `opens_at` time DEFAULT NULL,
  `closes_at` time DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Hizmetler tablosu
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `salon_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` float NOT NULL,
  `duration` int(11) NOT NULL,
  `pet_type` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `salon_id` (`salon_id`),
  CONSTRAINT `services_ibfk_1` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Müsaitlik tablosu
CREATE TABLE `availability` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `salon_id` int(11) NOT NULL,
  `day_of_week` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `salon_id` (`salon_id`),
  CONSTRAINT `availability_ibfk_1` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Randevular tablosu
CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `salon_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `pet_name` varchar(50) DEFAULT NULL,
  `pet_type` varchar(50) DEFAULT NULL,
  `pet_breed` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `salon_id` (`salon_id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Yorumlar ve derecelendirmeler tablosu
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `salon_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `salon_id` (`salon_id`),
  KEY `appointment_id` (`appointment_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Örnek salon verileri
INSERT INTO `salons` (`name`, `address`, `city`, `zip_code`, `phone`, `email`, `description`, `opens_at`, `closes_at`) VALUES
('Pati Kuaför', 'Bahçelievler Mah. Atatürk Cad. No:123', 'İstanbul', '34180', '05321234567', 'info@patikuafor.com', 'Köpek ve kediler için profesyonel bakım hizmetleri', '09:00:00', '18:00:00'),
('HavHav Pet Salon', 'Çankaya Cad. No:45', 'Ankara', '06690', '05331234567', 'iletisim@havhav.com', 'Evinizin yakınında profesyonel köpek bakımı', '10:00:00', '19:00:00'),
('Kedi Dünyası', 'Alsancak Mah. Kıbrıs Şehitleri Cad. No:78', 'İzmir', '35220', '05351234567', 'info@kedidunyasi.com', 'Sadece kedilere özel bakım ve spa hizmetleri', '09:00:00', '17:00:00'),
('Elit Pet Care', 'Lara Cad. No:32', 'Antalya', '07100', '05361234567', 'info@elitpetcare.com', 'Lüks ve premium pet bakım hizmetleri', '09:00:00', '20:00:00');

-- Örnek hizmetler
INSERT INTO `services` (`salon_id`, `name`, `description`, `price`, `duration`, `pet_type`) VALUES
(1, 'Köpek Temel Bakım', 'Yıkama, kurutma, tırnak kesimi ve kulak temizliği', 150.00, 60, 'dog'),
(1, 'Kedi Temel Bakım', 'Yıkama, kurutma ve tırnak kesimi', 120.00, 45, 'cat'),
(1, 'Tam Tıraş', 'Komple vücut tıraşı, yıkama ve bakım', 200.00, 90, 'dog'),
(2, 'Köpek Yıkama', 'Standart şampuan ile yıkama ve kurutma', 100.00, 30, 'dog'),
(2, 'Köpek Tıraşı', 'Irka özel tıraş ve şekillendirme', 180.00, 75, 'dog'),
(2, 'Premium Bakım', 'Özel şampuan, kondisyoner, parfüm ve masaj', 250.00, 120, 'dog'),
(3, 'Kedi Tüy Bakımı', 'Tarama, yumak giderme ve tüy şekillendirme', 140.00, 60, 'cat'),
(3, 'Kedi Tırnak Kesimi', 'Güvenli ve stressiz tırnak kesim hizmeti', 50.00, 15, 'cat'),
(3, 'Kedi Spa', 'Özel şampuan ve balsamlar ile komple bakım', 180.00, 90, 'cat'),
(4, 'VIP Köpek Bakımı', 'Premium şampuan, özel masaj ve profesyonel tıraş', 300.00, 120, 'dog'),
(4, 'VIP Kedi Bakımı', 'Hassas cilt için özel bakım ve masaj', 250.00, 90, 'cat'),
(4, 'Hijyen Paketi', 'Gözler, kulaklar, patiler ve tuvalet bölgesi temizliği', 120.00, 45, 'both');

-- Örnek müsaitlik verileri
INSERT INTO `availability` (`salon_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(1, 0, '09:00:00', '18:00:00'), -- Pazartesi
(1, 1, '09:00:00', '18:00:00'), -- Salı
(1, 2, '09:00:00', '18:00:00'), -- Çarşamba
(1, 3, '09:00:00', '18:00:00'), -- Perşembe
(1, 4, '09:00:00', '18:00:00'), -- Cuma
(1, 5, '10:00:00', '16:00:00'), -- Cumartesi
(2, 0, '10:00:00', '19:00:00'),
(2, 1, '10:00:00', '19:00:00'),
(2, 2, '10:00:00', '19:00:00'),
(2, 3, '10:00:00', '19:00:00'),
(2, 4, '10:00:00', '19:00:00'),
(2, 5, '11:00:00', '17:00:00'),
(3, 0, '09:00:00', '17:00:00'),
(3, 1, '09:00:00', '17:00:00'),
(3, 2, '09:00:00', '17:00:00'),
(3, 3, '09:00:00', '17:00:00'),
(3, 4, '09:00:00', '17:00:00'),
(4, 0, '09:00:00', '20:00:00'),
(4, 1, '09:00:00', '20:00:00'),
(4, 2, '09:00:00', '20:00:00'),
(4, 3, '09:00:00', '20:00:00'),
(4, 4, '09:00:00', '20:00:00'),
(4, 5, '10:00:00', '18:00:00'),
(4, 6, '10:00:00', '16:00:00');

-- Örnek kullanıcı
-- Şifre: '123456' için hash değeri
INSERT INTO `users` (`username`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `role`) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', '05551234567', 'admin'),
('salon1', 'salon1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Salon', 'Owner', '05552345678', 'salon_owner'),
('user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test', 'User', '05553456789', 'customer');

-- Salon sahibini salon ile ilişkilendirme
UPDATE `users` SET `salon_id` = 1 WHERE `username` = 'salon1';