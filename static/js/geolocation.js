// Kullanıcının konumunu alıp, koordinatlardan en yakın şehri belirleyen fonksiyon
document.addEventListener('DOMContentLoaded', function() {
    const locationInput = document.getElementById('location');
    
    // Uygulamanın başlangıcında konumu almak için buton oluştur
    const locationInputParent = locationInput ? locationInput.parentElement : null;
    
    if (locationInput && locationInputParent) {
        // Konum butonu ekle
        const locationButton = document.createElement('span');
        locationButton.className = 'input-group-text bg-light border-start-0 border-end-0 cursor-pointer';
        locationButton.innerHTML = '<i class="fas fa-crosshairs text-muted"></i>';
        locationButton.style.cursor = 'pointer';
        locationButton.title = 'Konumunuzu kullanın';
        
        // Konum butonunu input grubuna ekleyin
        const inputIcon = locationInputParent.querySelector('.input-group-text');
        if (inputIcon) {
            locationInputParent.insertBefore(locationButton, inputIcon.nextSibling);
        }
        
        // Konum butonuna tıklanınca konum izni iste
        locationButton.addEventListener('click', function() {
            // Konum butonu tıklandığında daha önce izni reddedilmiş olsa bile tekrar iste
            // localStorage'dan permission değerini temizle
            localStorage.removeItem('locationPermissionGranted');
            // Sayfa değişkeni değerini sıfırla ki tekrar popup çıksın
            window.locationPromptShownThisPageLoad = false;
            // Konum iznini göster
            showLocationPermissionPrompt();
        });
    }
    
    // Sayfa yüklendiğinde otomatik olarak konum izni kontrolü ve gerekirse izin isteme
    checkLocationPermission();
});

// Konum iznini kontrol et, izin verilmemişse iste
function checkLocationPermission() {
    // Konum izni verilmiş mi kontrol et
    const permissionGranted = localStorage.getItem('locationPermissionGranted');
    
    // Eğer izin verilmişse, konumu al
    if (permissionGranted === 'true') {
        getGeolocation();
        return;
    }
    
    // Eğer izin verilmemişse ('false'), hiçbir şey yapma
    if (permissionGranted === 'false') {
        return;
    }
    
    // Eğer hiç sorulmamışsa (null veya undefined), izin iste
    showLocationPermissionPrompt();
}

// Doğrudan tarayıcıdan konum izni al ve konumu al
function getGeolocation() {
    const locationInput = document.getElementById('location');
    if (!locationInput) return;
    
    // Konum alınıyor mesajı göster
    locationInput.value = "Konum alınıyor...";
    locationInput.disabled = true;
    
    // Konumu al
    navigator.geolocation.getCurrentPosition(
        // Başarılı olunca
        function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            
            // Koordinatları yakın şehre çevir 
            findNearestCity(latitude, longitude, locationInput);
        },
        // Hata olunca
        function(error) {
            locationInput.disabled = false;
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    showLocationError("Konum izni reddedildi");
                    localStorage.setItem('locationPermissionGranted', 'false');
                    break;
                case error.POSITION_UNAVAILABLE:
                    showLocationError("Konum bilgisi mevcut değil");
                    break;
                case error.TIMEOUT:
                    showLocationError("İstek zaman aşımına uğradı");
                    break;
                case error.UNKNOWN_ERROR:
                    showLocationError("Bilinmeyen bir hata oluştu");
                    break;
            }
        },
        // Geolocation Ayarları
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

// Konum izni isteme mesajını göster
function showLocationPermissionPrompt() {
    // Eğer izin zaten verilmişse, tekrar popup gösterme
    if (localStorage.getItem('locationPermissionGranted') === 'true') {
        // İzin verilmişse otomatik olarak konumu al
        getGeolocation();
        return;
    }
    
    // Sadece bu sayfa görüntülemesinde daha önce gösterilmiş mi kontrolü
    if (window.locationPromptShownThisPageLoad) {
        return;
    }
    
    // Bu sayfa yüklemesinde gösterildiğini işaretle (sayfayı yenilememize kadar geçerli)
    window.locationPromptShownThisPageLoad = true;
    
    // Konum izni isteme modal
    const permissionModal = document.createElement('div');
    permissionModal.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    permissionModal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    permissionModal.style.zIndex = '9999';
    
    permissionModal.innerHTML = `
        <div class="bg-white p-4 rounded-3 shadow-lg" style="max-width: 400px;">
            <div class="text-center mb-3">
                <i class="fas fa-map-marker-alt fa-3x text-pet-blue mb-3"></i>
                <h5 class="fw-bold">Konumunuzu kullanmamıza izin verin</h5>
                <p class="text-muted mb-3">Size en yakın kuaförleri gösterebilmemiz için konumunuzu paylaşın.</p>
            </div>
            <div class="d-flex justify-content-between">
                <button id="denyLocation" class="btn btn-outline-secondary px-4">Şimdi Değil</button>
                <button id="allowLocation" class="btn bg-pet-blue text-white px-4">İzin Ver</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(permissionModal);
    
    // İzin verilince konumu al
    document.getElementById('allowLocation').addEventListener('click', function() {
        localStorage.setItem('locationPermissionGranted', 'true');
        permissionModal.remove();
        
        // Konum almaya başla
        const locationInput = document.getElementById('location');
        if (locationInput) {
            // Konum alınıyor mesajı göster
            locationInput.value = "Konum alınıyor...";
            locationInput.disabled = true;
            
            // Konumu al
            navigator.geolocation.getCurrentPosition(
                // Başarılı olunca
                function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    
                    // Koordinatları yakın şehre çevir 
                    findNearestCity(latitude, longitude, locationInput);
                },
                // Hata olunca
                function(error) {
                    locationInput.disabled = false;
                    
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            showLocationError("Konum izni reddedildi");
                            localStorage.setItem('locationPermissionGranted', 'false');
                            break;
                        case error.POSITION_UNAVAILABLE:
                            showLocationError("Konum bilgisi mevcut değil");
                            break;
                        case error.TIMEOUT:
                            showLocationError("İstek zaman aşımına uğradı");
                            break;
                        case error.UNKNOWN_ERROR:
                            showLocationError("Bilinmeyen bir hata oluştu");
                            break;
                    }
                },
                // Geolocation Ayarları
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }
    });
    
    // İzin verilmezse modal kapat
    document.getElementById('denyLocation').addEventListener('click', function() {
        localStorage.setItem('locationPermissionGranted', 'false');
        permissionModal.remove();
    });
}

// Kullanıcı konumunu al
function getUserLocation() {
    const locationInput = document.getElementById('location');
    if (!locationInput) return;
    
    // Kullanıcının tarayıcısı geolocation destekliyor mu?
    if (navigator.geolocation) {
        // Her durumda önce bizim özel konum izni popup'ını gösterelim
        // Popup'taki "İzin Ver" butonuna tıklanınca browser'ın izni de istenerek konum alınacak
        showLocationPermissionPrompt();
    } else {
        showLocationError("Tarayıcınız konum bilgisini desteklemiyor");
    }
}

// Konum alınırken hata oluşursa mesaj göster
function showLocationError(message) {
    const locationInput = document.getElementById('location');
    if (locationInput) {
        locationInput.value = "";
        locationInput.placeholder = message;
        
        // 3 saniye sonra normal placeholder'a dön
        setTimeout(() => {
            locationInput.placeholder = "Şehir veya posta kodu giriniz";
        }, 3000);
    }
}

// Koordinatlardan en yakın şehri bul (basit algoritma)
function findNearestCity(latitude, longitude, locationInput) {
    // Türkiye'nin büyük şehirleri ve koordinatları
    const turkishCities = [
        { name: "İstanbul", lat: 41.0082, lon: 28.9784 },
        { name: "Ankara", lat: 39.9334, lon: 32.8597 },
        { name: "İzmir", lat: 38.4237, lon: 27.1428 },
        { name: "Bursa", lat: 40.1885, lon: 29.0610 },
        { name: "Antalya", lat: 36.8969, lon: 30.7133 },
        { name: "Adana", lat: 37.0000, lon: 35.3213 },
        { name: "Konya", lat: 37.8664, lon: 32.4857 },
        { name: "Gaziantep", lat: 37.0662, lon: 37.3833 },
        { name: "Mersin", lat: 36.8000, lon: 34.6333 },
        { name: "Diyarbakır", lat: 37.9144, lon: 40.2306 },
        { name: "Kayseri", lat: 38.7312, lon: 35.4787 },
        { name: "Eskişehir", lat: 39.7767, lon: 30.5206 },
        { name: "Samsun", lat: 41.2867, lon: 36.3300 },
        { name: "Denizli", lat: 37.7765, lon: 29.0864 },
        { name: "Şanlıurfa", lat: 37.1674, lon: 38.7955 },
        { name: "Malatya", lat: 38.3552, lon: 38.3095 },
        { name: "Trabzon", lat: 41.0015, lon: 39.7178 },
        { name: "Erzurum", lat: 39.9000, lon: 41.2700 },
        { name: "Van", lat: 38.4891, lon: 43.4089 },
        { name: "Manisa", lat: 38.6191, lon: 27.4289 },
        { name: "Kocaeli", lat: 40.7655, lon: 29.9408 },
        { name: "Adıyaman", lat: 37.7648, lon: 38.2786 },
        { name: "Ağrı", lat: 39.7191, lon: 43.0503 },
        { name: "Amasya", lat: 40.6499, lon: 35.8353 },
        { name: "Artvin", lat: 41.1828, lon: 41.8183 },
        { name: "Aydın", lat: 37.8560, lon: 27.8416 },
        { name: "Balıkesir", lat: 39.6484, lon: 27.8826 },
        { name: "Bilecik", lat: 40.1431, lon: 29.9792 },
        { name: "Bingöl", lat: 39.0626, lon: 40.7696 },
        { name: "Bitlis", lat: 38.4007, lon: 42.1095 },
        { name: "Bolu", lat: 40.7392, lon: 31.6089 },
        { name: "Burdur", lat: 37.7205, lon: 30.2900 },
        { name: "Çanakkale", lat: 40.1553, lon: 26.4142 },
        { name: "Çankırı", lat: 40.6013, lon: 33.6134 },
        { name: "Çorum", lat: 40.5489, lon: 34.9533 },
        { name: "Elazığ", lat: 38.6810, lon: 39.2264 },
        { name: "Edirne", lat: 41.6818, lon: 26.5623 },
        { name: "Erzincan", lat: 39.7500, lon: 39.5000 },
        { name: "Giresun", lat: 40.9128, lon: 38.3895 },
        { name: "Gümüşhane", lat: 40.4386, lon: 39.5086 },
        { name: "Hakkari", lat: 37.5833, lon: 43.7667 },
        { name: "Hatay", lat: 36.2000, lon: 36.1667 },
        { name: "Isparta", lat: 37.7648, lon: 30.5566 },
        { name: "Kars", lat: 40.6167, lon: 43.1000 },
        { name: "Kastamonu", lat: 41.3887, lon: 33.7827 },
        { name: "Kırklareli", lat: 41.7333, lon: 27.2167 },
        { name: "Kırşehir", lat: 39.1425, lon: 34.1709 },
        { name: "Kütahya", lat: 39.4167, lon: 29.9833 },
        { name: "Muğla", lat: 37.2167, lon: 28.3667 },
        { name: "Muş", lat: 38.7432, lon: 41.5065 },
        { name: "Nevşehir", lat: 38.6939, lon: 34.6857 },
        { name: "Niğde", lat: 37.9667, lon: 34.6833 },
        { name: "Ordu", lat: 40.9833, lon: 37.8833 },
        { name: "Rize", lat: 41.0201, lon: 40.5234 },
        { name: "Sakarya", lat: 40.7731, lon: 30.3946 },
        { name: "Siirt", lat: 37.9333, lon: 41.9500 },
        { name: "Sinop", lat: 42.0231, lon: 35.1531 },
        { name: "Sivas", lat: 39.7477, lon: 37.0179 },
        { name: "Tekirdağ", lat: 40.9833, lon: 27.5167 },
        { name: "Tokat", lat: 40.3167, lon: 36.5500 },
        { name: "Tunceli", lat: 39.1079, lon: 39.5401 },
        { name: "Uşak", lat: 38.6823, lon: 29.4082 },
        { name: "Yozgat", lat: 39.8181, lon: 34.8147 },
        { name: "Zonguldak", lat: 41.4564, lon: 31.7987 },
        { name: "Aksaray", lat: 38.3687, lon: 34.0370 },
        { name: "Bayburt", lat: 40.2552, lon: 40.2249 },
        { name: "Karaman", lat: 37.1759, lon: 33.2287 },
        { name: "Kırıkkale", lat: 39.8468, lon: 33.5153 },
        { name: "Batman", lat: 37.8812, lon: 41.1351 },
        { name: "Şırnak", lat: 37.5164, lon: 42.4611 },
        { name: "Bartın", lat: 41.6345, lon: 32.3375 },
        { name: "Ardahan", lat: 41.1105, lon: 42.7022 },
        { name: "Iğdır", lat: 39.9167, lon: 44.0333 },
        { name: "Yalova", lat: 40.6500, lon: 29.2667 },
        { name: "Karabük", lat: 41.2061, lon: 32.6204 },
        { name: "Kilis", lat: 36.7184, lon: 37.1212 },
        { name: "Osmaniye", lat: 37.0748, lon: 36.2465 },
        { name: "Düzce", lat: 40.8438, lon: 31.1565 }
    ];
    
    // En yakın şehri bul
    let nearestCity = null;
    let minDistance = Infinity;
    
    for (const city of turkishCities) {
        // İki nokta arasındaki mesafeyi hesapla (Haversine formülü)
        const distance = haversineDistance(latitude, longitude, city.lat, city.lon);
        
        if (distance < minDistance) {
            minDistance = distance;
            nearestCity = city.name;
        }
    }
    
    // En yakın şehri input kutusuna yaz
    if (nearestCity && locationInput) {
        locationInput.value = nearestCity;
        locationInput.disabled = false;
        
        // Şehir seçildiğinde dropdown listesini gizle
        const cityList = document.getElementById('cityList');
        if (cityList) {
            cityList.style.display = 'none';
            cityList.classList.add('hidden');
        }
    }
}

// İki koordinat arasındaki mesafeyi hesaplayan fonksiyon (km cinsinden)
function haversineDistance(lat1, lon1, lat2, lon2) {
    // Dünya yarıçapı (km)
    const R = 6371;
    
    // Radyana çevirme
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * 
        Math.sin(dLon/2) * Math.sin(dLon/2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c;
    
    return distance;
}

// Dereceyi radyana çeviren yardımcı fonksiyon
function toRad(degree) {
    return degree * (Math.PI / 180);
}